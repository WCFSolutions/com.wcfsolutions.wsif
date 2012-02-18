<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/category/Category.class.php');

// wcf imports
require_once(WCF_DIR.'lib/system/language/LanguageEditor.class.php');

/**
 * Represents a category in the filebase.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	data.category
 * @category	Infinite Filebase
 */
class CategoryEditor extends Category {
	/**
	 * Creates a new CategoryEditor object.
	 */
	public function __construct($categoryID, $row = null, $cacheObject = null, $useCache = true) {
		if ($useCache) parent::__construct($categoryID, $row, $cacheObject);
		else {
			$sql = "SELECT	*
				FROM	wsif".WSIF_N."_category
				WHERE	categoryID = ".$categoryID;
			$row = WCF::getDB()->getFirstRow($sql);
			parent::__construct(null, $row);
		}
	}
	
	/**
	 * Updates the amount of entries for this category.
	 * If you don't know exactly the new entry count, see Category::refresh()
	 * 
	 * @param	integer		$entries
	 * @param	integer		$images
	 * @param	integer		$files
	 * @see		Category::refresh()
	 */
	public function updateEntries($entries = 1, $images = 1, $files = 1) {
		$sql = "UPDATE	wsif".WSIF_N."_category
			SET	entries = entries + ".$entries."
				".($images ? ', entryImages = entryImages + '.$images : '')."
				".($files ? ', entryFiles = entryFiles + '.$files : '')."
			WHERE 	categoryID = ".$this->categoryID;
		WCF::getDB()->registerShutdownUpdate($sql);
	}
	
	/**
	 * Updates the amount of images of this category.
	 *
	 * @param	integer		$images
	 */
	public function updateEntryImages($images) {
		$sql = "UPDATE 	wsif".WSIF_N."_category
			SET	entryImages = IF(".$images." > 0 OR entryImages > ABS(".$images."), entryImages + ".$images.", 0)
			WHERE 	categoryID = ".$this->categoryID;
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Updates the amount of files of this category.
	 *
	 * @param	integer		$files
	 */
	public function updateEntryFiles($files) {
		$sql = "UPDATE 	wsif".WSIF_N."_category
			SET	entryFiles = IF(".$files." > 0 OR entryFiles > ABS(".$files."), entryFiles + ".$files.", 0)
			WHERE 	categoryID = ".$this->categoryID;
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Sets the last entry of this category.
	 * 
	 * @param 	 Entry		$entry
	 */
	public function setLastEntry($entry) {
		$sql = "REPLACE INTO	wsif".WSIF_N."_category_last_entry
					(categoryID, languageID, entryID) 
			VALUES 		(".$this->categoryID.", ".$entry->languageID.", ".$entry->entryID.")";
		WCF::getDB()->registerShutdownUpdate($sql);
	}
	
	
	/**
	 * Sets the last entry of this category for the given language ids.
	 * 
	 * @param 	string		$languageIDs
	 */
	public function setLastEntries($languageIDs = '') {
		// get all language ids if necessary
		if ($languageIDs === '') {
			$sql = "SELECT	DISTINCT languageID
				FROM	wsif".WSIF_N."_entry
				WHERE	categoryID = ".$this->categoryID."
					AND isDeleted = 0
					AND isDisabled = 0";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				if (!empty($languageIDs)) $languageIDs .= ',';
				$languageIDs .= $row['languageID'];
			}
		}
		
		// set last entries
		if ($languageIDs !== '') {
			$languages = explode(',', $languageIDs);
			foreach ($languages as $languageID) {		
				$sql = "SELECT		entryID
					FROM 		wsif".WSIF_N."_entry
					WHERE 		categoryID = ".$this->categoryID."
							AND isDeleted = 0
							AND isDisabled = 0
							AND languageID = ".$languageID."
					ORDER BY 	time DESC";
				$row = WCF::getDB()->getFirstRow($sql);
				if (!empty($row['entryID'])) {
					$sql = "REPLACE INTO	wsif".WSIF_N."_category_last_entry
								(categoryID, languageID, entryID) 
						VALUES 		(".$this->categoryID.", ".$languageID.", ".$row['entryID'].")";
					WCF::getDB()->registerShutdownUpdate($sql);
				}
			}
		}
		else {
			$sql = "DELETE FROM	wsif".WSIF_N."_category_last_entry
				WHERE		categoryID = ".$this->categoryID;
			WCF::getDB()->registerShutdownUpdate($sql);
		}
	}
	
	/**
	 * Updates the stats for this category.
	 */
	public function refresh() {
		$this->refreshAll($this->categoryID);
	}

	/**
	 * Removes the positions of this category.
	 */
	public function removePositions() {
		// unshift categories
		$sql = "SELECT 	parentID, position
			FROM	wsif".WSIF_N."_category_structure
			WHERE	categoryID = ".$this->categoryID;
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$sql = "UPDATE	wsif".WSIF_N."_category_structure
				SET	position = position - 1
				WHERE 	parentID = ".$row['parentID']."
					AND position > ".$row['position'];
			WCF::getDB()->sendQuery($sql);
		}
		
		// delete category structure record
		$sql = "DELETE FROM	wsif".WSIF_N."_category_structure
			WHERE		categoryID = ".$this->categoryID;
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Adds the position for this category.
	 * 
	 * @param	integer		$parentID
	 * @param	integer		$position
	 */
	public function addPosition($parentID, $position = null) {
		// shift categories
		if ($position !== null) {
			$sql = "UPDATE	wsif".WSIF_N."_category_structure
				SET	position = position + 1
				WHERE 	parentID = ".$parentID."
					AND position >= ".$position;
			WCF::getDB()->sendQuery($sql);
		}
		
		// get final position
		$sql = "SELECT 	IFNULL(MAX(position), 0) + 1 AS position
			FROM	wsif".WSIF_N."_category_structure
			WHERE	parentID = ".$parentID."
				".($position ? "AND position <= ".$position : '');
		$row = WCF::getDB()->getFirstRow($sql);
		$position = $row['position'];
		
		// save position
		$sql = "INSERT INTO	wsif".WSIF_N."_category_structure
					(parentID, categoryID, position)
			VALUES		(".$parentID.", ".$this->categoryID.", ".$position.")";
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Returns the cleaned permission list.
	 * Removes default permissions from the given permission list.
	 *
	 * @param	array		$permissions
	 * @return	array
	 */
	public static function getCleanedPermissions($permissions) {
		$noDefaultValue = false;
		foreach ($permissions as $key => $permission) {
			foreach ($permission['settings'] as $value) {
				if ($value != -1) $noDefaultValue = true;
			}
			if (!$noDefaultValue) {
				unset($permissions[$key]);
				continue;
			}
		}
		return $permissions;
	}
	
	/**
	 * Removes the user and group permissions of this category.
	 */
	public function removePermissions() {
		// user
		$sql = "DELETE FROM	wsif".WSIF_N."_category_to_user
			WHERE		categoryID = ".$this->categoryID;
		WCF::getDB()->sendQuery($sql);
		
		// group
		$sql = "DELETE FROM	wsif".WSIF_N."_category_to_group
			WHERE		categoryID = ".$this->categoryID;
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Adds the given permissions to this category.
	 *
	 * @param	array		$permissions
	 * @param	array		$permissionSettings
	 */
	public function addPermissions($permissions, $permissionSettings) {
		$userInserts = $groupInserts = '';
		foreach ($permissions as $key => $permission) {
			if ($permission['type'] == 'user') {
				if (!empty($userInserts)) $userInserts .= ',';
				$userInserts .= '('.$this->categoryID.',
						 '.intval($permission['id']).',
						 '.(implode(', ', ArrayUtil::toIntegerArray($permission['settings']))).')';
			
			}
			else {
				if (!empty($groupInserts)) $groupInserts .= ',';
				$groupInserts .= '('.$this->categoryID.',
						 '.intval($permission['id']).',
						 '.(implode(', ', ArrayUtil::toIntegerArray($permission['settings']))).')';
			}
		}
	
		if (!empty($userInserts)) {
			$sql = "INSERT INTO	wsif".WSIF_N."_category_to_user
						(categoryID, userID, ".implode(', ', $permissionSettings).")
				VALUES		".$userInserts;
			WCF::getDB()->sendQuery($sql);
		}
		
		if (!empty($groupInserts)) {
			$sql = "INSERT INTO	wsif".WSIF_N."_category_to_group
						(categoryID, groupID, ".implode(', ', $permissionSettings).")
				VALUES		".$groupInserts;
			WCF::getDB()->sendQuery($sql);
		}
		
		return $permissions;
	}
	
	/**
	 * Removes the moderator permissions of this category.
	 */
	public function removeModerators() {
		$sql = "DELETE FROM	wsif".WSIF_N."_category_moderator
			WHERE		categoryID = ".$this->categoryID;
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Adds the given moderators to this category.
	 *
	 * @param	array		$moderators
	 * @param	array		$moderatorSettings
	 */
	public function addModerators($moderators, $moderatorSettings) {
		$inserts = '';
		foreach ($moderators as $moderator) {
			if (!empty($inserts)) $inserts .= ',';
			$inserts .= '	('.$this->categoryID.',
					'.($moderator['type'] == 'user' ? intval($moderator['id']) : 0).',
					'.($moderator['type'] == 'group' ? intval($moderator['id']) : 0).',
					'.(implode(', ', ArrayUtil::toIntegerArray($moderator['settings']))).')';
		}
	
		if (!empty($inserts)) {
			$sql = "INSERT INTO	wsif".WSIF_N."_category_moderator
						(categoryID, userID, groupID, ".implode(', ', $moderatorSettings).")
				VALUES		".$inserts;
			WCF::getDB()->sendQuery($sql);
		}
	}

	/**
	 * Updates this category.
	 * 
	 * @param	integer		$parentID
	 * @param	string		$title
	 * @param	string		$description
	 * @param	integer		$allowDescriptionHtml
	 * @param	integer		$categoryType
	 * @param	string		$icon
	 * @param	string		$externalURL
	 * @param	integer		$styleID
	 * @param	integer		$enforceStyle
	 * @param	integer		$daysPrune
	 * @param	string		$sortField
	 * @param	string		$sortOrder
	 * @param	integer		$enableRating
	 * @param	integer		$entriesPerPage
	 * @param	integer		$languageID
	 */
	public function update($parentID, $title, $description = '', $allowDescriptionHtml = 0, $categoryType = 0, $icon = '', $externalURL = '', $styleID = 0, $enforceStyle = 0, $daysPrune = 0, $sortField = '', $sortOrder = '', $enableRating = -1, $entriesPerPage = 0, $languageID = 0) {
		// update category
		$sql = "UPDATE	wsif".WSIF_N."_category
			SET	parentID = ".$parentID.",
				".($languageID == 0 ? "category = '".escapeString($title)."'," : '')."
				allowDescriptionHtml = '".$allowDescriptionHtml."',
				categoryType = ".$categoryType.",
				icon = '".escapeString($icon)."',
				externalURL = '".escapeString($externalURL)."',
				styleID = ".$styleID.",
				enforceStyle = ".$enforceStyle.",
				daysPrune = ".$daysPrune.",
				sortField = '".escapeString($sortField)."',
				sortOrder = '".escapeString($sortOrder)."',
				enableRating = ".$enableRating.",
				entriesPerPage = ".$entriesPerPage."
			WHERE	categoryID = ".$this->categoryID;
		WCF::getDB()->sendQuery($sql);
		
		// update language items
		if ($languageID != 0) {
			// save language variables
			$language = new LanguageEditor($languageID);
			$language->updateItems(array('wsif.category.'.$this->category => $title, 'wsif.category.'.$this->category.'.description' => $description), 0, PACKAGE_ID, array('wsif.category.'.$this->category => 1, 'wsif.category.'.$this->category.'.description' => 1));
			LanguageEditor::deleteLanguageFiles($languageID, 'wsif.category', PACKAGE_ID);
		}
	}
	
	/**
	 * Deletes this category.
	 */
	public function delete() {
		// get all entry ids
		$entryIDs = '';
		$sql = "SELECT	entryID
			FROM	wsif".WSIF_N."_entry
			WHERE	categoryID = ".$this->categoryID;
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			if (!empty($entryIDs)) $entryIDs .= ',';
			$entryIDs .= $row['entryID'];
		}
		if (!empty($entryIDs)) {
			// delete entries
			require_once(WSIF_DIR.'lib/data/entry/EntryEditor.class.php');
			EntryEditor::deleteAllCompletely($entryIDs);
		}
		
		// remove positions
		$this->removePositions();
		
		// update sub categories
		$sql = "UPDATE	wsif".WSIF_N."_category
			SET	parentID = ".$this->parentID."
			WHERE	parentID = ".$this->categoryID;
		WCF::getDB()->sendQuery($sql);
		
		$sql = "UPDATE	wsif".WSIF_N."_category_structure
			SET	parentID = ".$this->parentID."
			WHERE	parentID = ".$this->categoryID;
		WCF::getDB()->sendQuery($sql);
		
		// delete category
		$sql = "DELETE FROM	wsif".WSIF_N."_category
			WHERE		categoryID = ".$this->categoryID;
		WCF::getDB()->sendQuery($sql);
		
		// delete last entry
		$sql = "DELETE FROM	wsif".WSIF_N."_category_last_entry
			WHERE		categoryID = ".$this->categoryID;
		WCF::getDB()->sendQuery($sql);
		
		// delete category moderator options
		$sql = "DELETE FROM	wsif".WSIF_N."_category_moderator
			WHERE		categoryID = ".$this->categoryID;
		WCF::getDB()->sendQuery($sql);
		
		// delete category group options
		$sql = "DELETE FROM	wsif".WSIF_N."_category_to_group
			WHERE		categoryID = ".$this->categoryID;
		WCF::getDB()->sendQuery($sql);
		
		// delete category user options
		$sql = "DELETE FROM	wsif".WSIF_N."_category_to_user
			WHERE		categoryID = ".$this->categoryID;
		WCF::getDB()->sendQuery($sql);
		
		// delete entry prefix to category options
		$sql = "DELETE FROM	wsif".WSIF_N."_entry_prefix_to_category
			WHERE		categoryID = ".$this->categoryID;
		WCF::getDB()->sendQuery($sql);
			
		// delete language variables
		LanguageEditor::deleteVariable('wsif.category.'.$this->category);
		LanguageEditor::deleteVariable('wsif.category.'.$this->category.'.description');
	}
	
	/**
	 * Creates a new category.
	 *
	 * @param	integer		$parentID
	 * @param	integer		$position
	 * @param	string		$title
	 * @param	string		$description
	 * @param	integer		$allowDescriptionHtml
	 * @param	integer		$categoryType
	 * @param	string		$icon
	 * @param	string		$externalURL
	 * @param	integer		$styleID
	 * @param	integer		$enforceStyle
	 * @param	integer		$daysPrune
	 * @param	string		$sortField
	 * @param	string		$sortOrder
	 * @param	integer		$enableRating
	 * @param	integer		$entriesPerPage
	 * @param	integer		$languageID
	 * @return	CategoryEditor
	 */
	public static function create($parentID, $position, $title, $description = '', $allowDescriptionHtml = 0, $categoryType = 0, $icon = '', $externalURL = '', $styleID = 0, $enforceStyle = 0, $daysPrune = 0, $sortField = '', $sortOrder = '', $enableRating = -1, $entriesPerPage = 0, $languageID = 0) {
		// get title
		$category = '';
		if ($languageID == 0) $category = $title;

		// save category
		$sql = "INSERT INTO	wsif".WSIF_N."_category
					(parentID, category, allowDescriptionHtml, categoryType, icon, time, externalURL,
					styleID, enforceStyle, daysPrune, sortField, sortOrder,
					enableRating, entriesPerPage)
			VALUES		(".$parentID.", '".escapeString($category)."', ".$allowDescriptionHtml.", ".$categoryType.", '".escapeString($icon)."', ".TIME_NOW.", '".escapeString($externalURL)."',
					".$styleID.", ".$enforceStyle.", ".$daysPrune.", '".escapeString($sortField)."', '".escapeString($sortOrder)."',
					".$enableRating.", ".$entriesPerPage.")";
		WCF::getDB()->sendQuery($sql);
		
		// get category id
		$categoryID = WCF::getDB()->getInsertID("wsif".WSIF_N."_category", 'categoryID');
		
		// update language items
		if ($languageID != 0) {
			// set name
			$category = "category".$categoryID;
			$sql = "UPDATE	wsif".WSIF_N."_category
				SET	category = '".escapeString($category)."'
				WHERE	categoryID = ".$categoryID;
			WCF::getDB()->sendQuery($sql);
			
			// save language variables
			$language = new LanguageEditor($languageID);
			$language->updateItems(array('wsif.category.'.$category => $title, 'wsif.category.'.$category.'.description' => $description));
			LanguageEditor::deleteLanguageFiles($languageID, 'wsif.category', PACKAGE_ID);
		}
		
		// get category
		$category = new CategoryEditor($categoryID, null, null, false);
		
		// add position
		$category->addPosition($parentID, $position);
		
		// return new category
		return $category;	
	}
	
	/**
	 * Updates the position of a specific category.
	 *
	 * @param	integer		$categoryID
	 * @param	integer		$parentID
	 * @param	integer		$position
	 */
	public static function updatePosition($categoryID, $parentID, $position) {		
		$sql = "UPDATE	wsif".WSIF_N."_category
			SET	parentID = ".$parentID."
			WHERE 	categoryID = ".$categoryID;
		WCF::getDB()->sendQuery($sql);
		
		$sql = "REPLACE INTO	wsif".WSIF_N."_category_structure
					(parentID, categoryID, position)
			VALUES		(".$parentID.", ".$categoryID.", ".$position.")";
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Updates the stats for the given categories.
	 * 
	 * @param	string		$categoryIDs
	 */
	public static function refreshAll($categoryIDs) {
		if (empty($categoryIDs)) return;
		
		$sql = "UPDATE	wsif".WSIF_N."_category category
			SET	entries = (
					SELECT	COUNT(*)
					FROM	wsif".WSIF_N."_entry
					WHERE	categoryID = category.categoryID
						AND isDeleted = 0
						AND isDisabled = 0
				),
				entryImages = (
					SELECT	IFNULL(SUM(images), 0)
					FROM	wsif".WSIF_N."_entry
					WHERE	categoryID = category.categoryID
						AND isDeleted = 0
						AND isDisabled = 0
				),
				entryFiles = (
					SELECT	IFNULL(SUM(files), 0)
					FROM	wsif".WSIF_N."_entry
					WHERE	categoryID = category.categoryID
						AND isDeleted = 0
						AND isDisabled = 0
				),
				entryDownloads = (
					SELECT	IFNULL(SUM(downloads), 0)
					FROM	wsif".WSIF_N."_entry
					WHERE	categoryID = category.categoryID
						AND isDeleted = 0
						AND isDisabled = 0
				)
			WHERE	categoryID IN (".$categoryIDs.")";
		WCF::getDB()->registerShutdownUpdate($sql);
	}
}
?>