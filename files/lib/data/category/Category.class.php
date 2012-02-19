<?php
// wcf imports
require_once(WCF_DIR.'lib/data/DatabaseObject.class.php');

/**
 * Represents a category in the filebase.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	data.category
 * @category	Infinite Filebase
 */
class Category extends DatabaseObject {
	protected $prefixList = null;
	
	// stats
	protected $clicks = null;
	protected $entries = null;
	protected $entryImages = null;
	protected $entryFiles = null;
	protected $entryDownloads = null;
	protected $entryDownloadsPerDay = null;
	
	protected static $categories = null;
	protected static $categoryStructure = null;
	protected static $categorySelect;
	
	// category types
	const TYPE_CATEGORY = 0;
	const TYPE_MAIN_CATEGORY = 1;
	const TYPE_LINK = 2;
	
	/**
	 * Creates a new Category object.
	 * 
	 * @param 	integer		$categoryID
	 * @param 	array		$row
	 * @param 	Category	$cacheObject
	 */
	public function __construct($categoryID, $row = null, $cacheObject = null) {
		if ($categoryID !== null) $cacheObject = self::getCategory($categoryID);
		if ($row != null) parent::__construct($row);
		if ($cacheObject != null) parent::__construct($cacheObject->data);
	}
	
	/**
	 * Enters this category.
	 */
	public function enter() {
		// check permissions
		if (!$this->getPermission('canViewCategory') || !$this->getPermission('canEnterCategory')) {
			throw new PermissionDeniedException();
		}
		
		// refresh session
		WCF::getSession()->setCategoryID($this->categoryID);
		
		// change style if necessary
		require_once(WCF_DIR.'lib/system/style/StyleManager.class.php');
		if ($this->styleID && (!WCF::getSession()->getStyleID() || $this->enforceStyle) && StyleManager::getStyle()->styleID != $this->styleID) {
			StyleManager::changeStyle($this->styleID, true);
		}
	}
	
	/**
	 * Returns the title of this category.
	 * 
	 * @return	string
	 */
	public function getTitle() {
		return StringUtil::encodeHTML(WCF::getLanguage()->get('wsif.category.'.$this->category));
	}
	
	/**
	 * Returns the formatted description of this category.
	 * 
	 * @return	string
	 */
	public function getFormattedDescription() {
		if ($this->allowDescriptionHtml) {
			return WCF::getLanguage()->get('wsif.category.'.$this->category.'.description');
		}
		return nl2br(StringUtil::encodeHTML(WCF::getLanguage()->get('wsif.category.'.$this->category.'.description')));
	}
	
	/**
	 * Returns true if this category is no main category and no external link.
	 *
	 * @return	boolean
	 */
	public function isCategory() {
		if ($this->categoryType == self::TYPE_CATEGORY) {
			return true;
		}
		return false;
	}
	
	/**
	 * Returns true if this category is a main category.
	 *
	 * @return	boolean
	 */
	public function isMainCategory() {
		if ($this->categoryType == self::TYPE_MAIN_CATEGORY) {
			return true;
		}
		return false;
	}
	
	/**
	 * Returns true if this category is an external link.
	 *
	 * @return	boolean
	 */
	public function isExternalLink() {
		if ($this->categoryType == self::TYPE_LINK) {
			return true;
		}
		return false;
	}

	/**
	 * Returns a list of the parent categories of this category.
	 * 
	 * @return	array
	 */
	public function getParentCategories() {
		$parentCategories = array();
		$categories = WCF::getCache()->get('category', 'categories');
			
		$parentCategory = $this;
		while ($parentCategory->parentID != 0) {
			$parentCategory = $categories[$parentCategory->parentID];
			array_unshift($parentCategories, $parentCategory);
		}
		
		return $parentCategories;
	}
	
	/**
	 * Checks whether the active user has the permission with the given name on this category.
	 * 
	 * @param	string		$permission	name of the requested permission
	 * @return	boolean
	 */
	public function getPermission($permission = 'canViewCategory') {
		return (boolean) WCF::getUser()->getCategoryPermission($permission, $this->categoryID);
	}
	
	/**
	 * Checks the requested permissions.
	 * Throws a PermissionDeniedException if the active user doesn't have one of the given permissions.
	 * @see 	Category::getModeratorPermission()
	 * 
	 * @param	mixed		$permissions
	 */
	public function checkPermission($permissions) {
		if (!is_array($permissions)) $permissions = array($permissions);
		
		$result = false;
		foreach ($permissions as $permission) {
			$result = $result || $this->getPermission($permission);
		}
		
		if (!$result) {
			throw new PermissionDeniedException();
		}
	}
	
	/**
	 * Checks whether the active user has the moderator permission with the given name on this category.
	 * 
	 * @param	string		$permission	name of the requested permission
	 * @return	boolean
	 */
	public function getModeratorPermission($permission) {
		return (boolean) WCF::getUser()->getCategoryModeratorPermission($permission, $this->categoryID);
	}

	/**
	 * Checks the requested moderator permissions.
	 * Throws a PermissionDeniedException if the active user doesn't have one of the given permissions.
	 * @see 	Category::getModeratorPermission()
	 * 
	 * @param	mixed		$permissions
	 */
	public function checkModeratorPermission($permissions) {
		if (!is_array($permissions)) $permissions = array($permissions);
		
		$result = false;
		foreach ($permissions as $permission) {
			$result = $result || $this->getModeratorPermission($permission);
		}
		
		if (!$result) {
			throw new PermissionDeniedException();
		}
	}
	
	/**
	 * Returns the filename of the category icon.
	 *
	 * @return	string		filename of the category icon
	 */
	public function getIconName() {
		if ($this->icon) {
			return $this->icon;
		}
		else if ($this->isCategory()) {
			return 'category';
		}
		else if ($this->isMainCategory()) {
			return 'categoryMain';
		}
		else {
			return 'categoryRedirect';
		}
	}
	
	/**
	 * Returns the moderator permissions of the active user.
	 * 
	 * @return	array
	 */
	public function getModeratorPermissions() {
		$permissions = array();
		$permissions['canDeleteEntry'] = intval($this->getModeratorPermission('canDeleteEntry'));
		$permissions['canViewDeletedEntry'] = intval($this->getModeratorPermission('canViewDeletedEntry'));
		$permissions['canDeleteEntryCompletely'] = intval($this->getModeratorPermission('canDeleteEntryCompletely'));
		$permissions['canEnableEntry'] = intval($this->getModeratorPermission('canEnableEntry'));
		$permissions['canEditEntry'] = intval($this->getModeratorPermission('canEditEntry'));
		$permissions['canMoveEntry'] = intval($this->getModeratorPermission('canMoveEntry'));
		$permissions['canMarkEntry'] = intval($permissions['canDeleteEntry'] || $permissions['canMoveEntry']);
		$permissions['canHandleEntry'] = intval($permissions['canEnableEntry'] || $permissions['canEditEntry'] || $permissions['canMarkEntry']);
		return $permissions;
	}
	
	/**
	 * Returns the global moderator permissions for the active user.
	 * 
	 * @return	array
	 */
	public static function getGlobalModeratorPermissions() {
		$permissions = array();
		$permissions['canDeleteEntry'] = intval(WCF::getUser()->getPermission('mod.filebase.canDeleteEntry'));
		$permissions['canViewDeletedEntry'] = intval(WCF::getUser()->getPermission('mod.filebase.canViewDeletedEntry'));
		$permissions['canDeleteEntryCompletely'] = intval(WCF::getUser()->getPermission('mod.filebase.canDeleteEntryCompletely'));
		$permissions['canEnableEntry'] = intval(WCF::getUser()->getPermission('mod.filebase.canEnableEntry'));
		$permissions['canEditEntry'] = intval(WCF::getUser()->getPermission('mod.filebase.canEditEntry'));
		$permissions['canMoveEntry'] = intval(WCF::getUser()->getPermission('mod.filebase.canMoveEntry'));
		$permissions['canMarkEntry'] = intval($permissions['canDeleteEntry'] || $permissions['canMoveEntry']);
		$permissions['canHandleEntry'] = intval($permissions['canEnableEntry'] || $permissions['canEditEntry'] || $permissions['canMarkEntry']);
		return $permissions;
	}
	
	/**
	 * Returns the last entry id of this category.
	 * 
	 * @return	integer
	 */
	public function getLastEntryID($languageID = null) {
		$sql = "SELECT 	entryID
			FROM 	wsif".WSIF_N."_category_last_entry
			WHERE 	categoryID = ".$this->categoryID.
				($languageID !== null ? " AND languageID = ".$languageID : "");
		$row = WCF::getDB()->getFirstRow($sql);		
		return $row['entryID'];
	}
	
	/**
	 * Returns the last entry time of this category.
	 * 
	 * @param	integer		$languageID
	 * @return	integer
	 */
	public function getLastEntryTime($languageID) {
		$cache = WCF::getCache()->get('categoryData', 'lastEntries');
		if (isset($cache[$this->categoryID][$languageID])) {
			return $cache[$this->categoryID][$languageID]['time'];
		}	
		return 0;
	}
	
	/**
	 * Returns the number of entries in this category.
	 * 
	 * @return	integer
	 */
	public function getEntries() {
		if (!$this->isCategory()) return null;
		
		if ($this->entries === null) {
			$this->entries = 0;
			$cache = WCF::getCache()->get('categoryData', 'stats');
			if (isset($cache[$this->categoryID]['entries'])) $this->entries = $cache[$this->categoryID]['entries'];
		}
		return $this->entries;
	}
	
	/**
	 * Returns the number of entry images in this category.
	 * 
	 * @return	integer
	 */
	public function getEntryImages() {
		if (!$this->isCategory()) return null;
		
		if ($this->entryImages === null) {
			$this->entryImages = 0;
			$cache = WCF::getCache()->get('categoryData', 'stats');
			if (isset($cache[$this->categoryID]['entryImages'])) $this->entryImages = $cache[$this->categoryID]['entryImages'];
		}
		return $this->entryImages;
	}
	
	/**
	 * Returns the number of entry files in this category.
	 * 
	 * @return	integer
	 */
	public function getEntryFiles() {
		if (!$this->isCategory()) return null;
		
		if ($this->entryFiles === null) {
			$this->entryFiles = 0;
			$cache = WCF::getCache()->get('categoryData', 'stats');
			if (isset($cache[$this->categoryID]['entryFiles'])) $this->entryFiles = $cache[$this->categoryID]['entryFiles'];
		}
		return $this->entryFiles;
	}
	
	/**
	 * Returns the number of entries in this category.
	 * 
	 * @return	integer
	 */
	public function getEntryDownloads() {
		if (!$this->isCategory()) return null;
		
		if ($this->entryDownloads === null) {
			$this->entryDownloads = 0;
			$cache = WCF::getCache()->get('categoryData', 'stats');
			if (isset($cache[$this->categoryID]['entryDownloads'])) $this->entryDownloads = $cache[$this->categoryID]['entryDownloads'];
		}
		return $this->entryDownloads;
	}
	
	/**
	 * Returns the number of entry downloads per day in this category.
	 * 
	 * @return	float
	 */
	public function getEntryDownloadsPerDay() {
		if ($this->entryDownlaodsPerDay === null) {
			$this->entryDownloadsPerDay = 0;
			$cache = WCF::getCache()->get('categoryData', 'stats');
			if (isset($cache[$this->categoryID]['entryDownloadsPerDay'])) $this->entryDownloadsPerDay = $cache[$this->categoryID]['entryDownloadsPerDay'];
		}
		return $this->entryDownloadsPerDay;
	}
	
	/**	
	 * Returns the prefixes of this category.
	 * 
	 * @return	array
	 */
	public function getPrefixes() {
		if ($this->prefixList === null) {
			require_once(WSIF_DIR.'lib/data/entry/prefix/EntryPrefix.class.php');
			
			// load cache
			$prefixes = WCF::getCache()->get('entryPrefix', 'prefixes');
			$categoryPrefixes = WCF::getCache()->get('entryPrefix', 'categories');
			
			// get prefix ids
			if (!isset($categoryPrefixes[$this->categoryID])) return array();
			$prefixIDArray = $categoryPrefixes[$this->categoryID];
			
			// init prefix list
			$this->prefixList = array();
			foreach ($prefixIDArray as $prefixID) {
				$this->prefixList[$prefixID] = $prefixes[$prefixID];
			}
		}
		return $this->prefixList;
	}
	
	/**
	 * Checks if the active user can add an entry in this category.
	 *
	 * @return	boolean
	 */
	public function canAddEntry() {
		return $this->getPermission('canAddEntry');
	}
	
	/**
	 * Returns an editor object for this category.
	 *
	 * @return	CategoryEditor
	 */
	public function getEditor() {
		require_once(WSIF_DIR.'lib/data/category/CategoryEditor.class.php');
		return new CategoryEditor(null, $this->data);
	}
	
	/**
	 * Gets the category with the given category id from cache.
	 * 
	 * @param 	integer		$categoryID	id of the requested category
	 * @return	Category
	 */
	public static function getCategory($categoryID) {
		if (self::$categories === null) {
			self::$categories = WCF::getCache()->get('category', 'categories');
		}
		
		if (!isset(self::$categories[$categoryID])) {
			throw new IllegalLinkException();
		}
		
		return self::$categories[$categoryID];
	}
	
	/**
	 * Creates the category select list.
	 * 
	 * @param	array		$permissions
	 * @param	boolean		$hideLinks
	 * @param	array		$ignoredCategories
	 * @return 	array
	 */
	public static function getCategorySelect($permissions = array('canViewCategory'), $hideLinks = false, $ignoredCategories = array()) {
		self::$categorySelect = array();
		
		if (self::$categories === null) self::$categories = WCF::getCache()->get('category', 'categories');
		if (self::$categoryStructure === null) self::$categoryStructure = WCF::getCache()->get('category', 'categoryStructure');
		
		self::makeCategorySelect(0, 0, $permissions, $hideLinks, $ignoredCategories);
		
		return self::$categorySelect;
	}
	
	/**
	 * Generates the category select list.
	 * 
	 * @param	integer		$parentID
	 * @param	integer		$depth
	 * @param	array		$permissions
	 * @param	boolean		$hideLinks
	 * @param	array		$ignoredCategories
	 */
	protected static function makeCategorySelect($parentID = 0, $depth = 0, $permissions = array('canViewCategory'), $hideLinks = false, $ignoredCategories = array()) {
		if (!isset(self::$categoryStructure[$parentID])) return;
		
		foreach (self::$categoryStructure[$parentID] as $categoryID) {
			if (in_array($categoryID, $ignoredCategories)) continue;
			
			$category = self::$categories[$categoryID];
			if ($hideLinks && $category->isExternalLink()) continue;
			
			$result = true;
			foreach ($permissions as $permission) {
				$result = $result && $category->getPermission($permission);
			}
			if (!$result) continue;
			
			$title = $category->getTitle();
			if ($depth > 0) $title = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $depth). ' ' . $title;
			
			self::$categorySelect[$categoryID] = $title;
			self::makeCategorySelect($categoryID, $depth + 1, $permissions, $hideLinks, $ignoredCategories);
		}
	}
	
	/**
	 * Returns a list of sub categories of this category.
	 * 
	 * @param	mixed		$categoryID
	 * @return	string
	 */
	public static function getSubCategories($categoryID) {
		return implode(',', self::getSubCategoryIDArray($categoryID));
	}
	
	/**
	 * Returns an array of sub categories of this category.
	 * 
	 * @param	mixed		$categoryID
	 * @return	array<integer>
	 */
	public static function getSubCategoryIDArray($categoryID) {
		$categoryIDArray = (is_array($categoryID) ? $categoryID : array($categoryID));
		$subCategoryIDArray = array();
		
		if (self::$categoryStructure === null) self::$categoryStructure = WCF::getCache()->get('category', 'categoryStructure');

		foreach ($categoryIDArray as $categoryID) {
			$subCategoryIDArray = array_merge($subCategoryIDArray, self::makeSubCategoryIDArray($categoryID, 0));
		}
		
		$subCategoryIDArray = array_unique($subCategoryIDArray);
		return $subCategoryIDArray;
	}
	
	/**
	 * Creates an array of sub categories of this category.
	 * 
	 * @param	integer		$parentCategoryID
	 * @param	integer		$depth
	 * @return	array<integer>
	 */
	public static function makeSubCategoryIDArray($parentCategoryID, $depth) {
		if (!isset(self::$categoryStructure[$parentCategoryID])) {
			return array();
		}
		
		$subCategoryIDArray = array();
		foreach (self::$categoryStructure[$parentCategoryID] as $categoryID) {
			$subCategoryIDArray = array_merge($subCategoryIDArray, self::makeSubCategoryIDArray($categoryID, $depth + 1));
			$subCategoryIDArray[] = $categoryID;
		}
		
		return $subCategoryIDArray;
	}
	
	/**
	 * Returns a list of accessible categories for the active user.
	 * 
	 * @param	array		$permissions
	 * @return	string
	 */
	public static function getAccessibleCategories($permissions = array('canViewCategory', 'canEnterCategory')) {
		return implode(',', self::getAccessibleCategoryIDArray($permissions));
	}
	
	/**
	 * Returns an array of accessible categories for the active user.
	 * 
	 * @param	array		$permissions
	 * @return	array<integer>
	 */
	public static function getAccessibleCategoryIDArray($permissions = array('canViewCategory', 'canEnterCategory')) {
		if (self::$categories === null) self::$categories = WCF::getCache()->get('category', 'categories');
		
		$categoryIDArray = array();
		foreach (self::$categories as $category) {
			$result = true;
			foreach ($permissions as $permission) {
				$result = $result && $category->getPermission($permission);
			}
			
			if ($result) {
				$categoryIDArray[] = $category->categoryID;
			}
		}
		
		return $categoryIDArray;
	}
	
	/**
	 * Returns a list of moderated categories for the active user.
	 * 
	 * @param	array		$permissions
	 * @return	string
	 */
	public static function getModeratedCategories($permissions) {
		return implode(',', self::getModeratedCategoryIDArray($permissions));
	}
	
	/**
	 * Returns an array of moderated categories for the active user.
	 * 
	 * @param	array		$permissions
	 * @return	array<integer>
	 */
	public static function getModeratedCategoryIDArray($permissions) {
		if (self::$categories === null) self::$categories = WCF::getCache()->get('category', 'categories');
		
		$categoryIDArray = array();
		foreach (self::$categories as $category) {
			$result = true;
			foreach ($permissions as $permission) {
				$result = $result && $category->getModeratorPermission($permission);
			}
			
			if ($result) {
				$categoryIDArray[] = $category->categoryID;
			}
		}
		
		return $categoryIDArray;
	}
	
	/** 
	 * Inherits category permissions.
	 *
	 * @param 	integer 	$parentID
	 * @param 	array 		$permissions
	 */
	public static function inheritPermissions($parentID = 0, &$permissions) {
		if (self::$categories === null) self::$categories = WCF::getCache()->get('category', 'categories');
		if (self::$categoryStructure === null) self::$categoryStructure = WCF::getCache()->get('category', 'categoryStructure');
		
		if (isset(self::$categoryStructure[$parentID]) && is_array(self::$categoryStructure[$parentID])) {
			foreach (self::$categoryStructure[$parentID] as $categoryID) {
				$category = self::$categories[$categoryID];
					
				// inherit permissions from parent category
				if ($category->parentID) {
					if (isset($permissions[$category->parentID]) && !isset($permissions[$categoryID])) {
						$permissions[$categoryID] = $permissions[$category->parentID];
					}
				}
				
				self::inheritPermissions($categoryID, $permissions);
			}
		}
	}
	
	/**
	 * Searches for a category in the child tree of another category.
	 *
	 * @param	integer		$parentID
	 * @param	integer		$searchedCategoryID
	 */
	public static function searchChildren($parentID, $searchedCategoryID) {
		if (self::$categoryStructure === null) self::$categoryStructure = WCF::getCache()->get('category', 'categoryStructure');
		if (isset(self::$categoryStructure[$parentID])) {
			foreach (self::$categoryStructure[$parentID] as $categoryID) {
				if ($categoryID == $searchedCategoryID) return true;
				if (self::searchChildren($categoryID, $searchedCategoryID)) return true;
			}
		}		
		return false;
	}
	
	/**
	 * Returns available permission settings.
	 */
	public static function getPermissionSettings() {
		$sql = "SHOW COLUMNS FROM wsif".WSIF_N."_category_to_group";
		$result = WCF::getDB()->sendQuery($sql);
		$settings = array();
		while ($row = WCF::getDB()->fetchArray($result)) {
			if ($row['Field'] != 'categoryID' && $row['Field'] != 'groupID') {
				// check modules
				switch ($row['Field']) {
					case 'canCommentEntry':
					case 'canEditOwnEntryComment':
					case 'canDeleteOwnEntryComment':
						if (!MODULE_COMMENT) continue 2;
						break;
					case 'canDownloadEntryCommentAttachment':
					case 'canViewEntryCommentAttachmentPreview':
					case 'canUploadEntryCommentAttachment':
						if (!MODULE_COMMENT || !MODULE_ATTACHMENT) continue 2;
						break;
				}
				
				$settings[] = $row['Field'];
			}
		}
		return $settings;
	}
	
	/**
	 * Returns available moderator settings.
	 */
	public static function getModeratorSettings() {
		$sql = "SHOW COLUMNS FROM wsif".WSIF_N."_category_moderator";
		$result = WCF::getDB()->sendQuery($sql);
		$settings = array();
		while ($row = WCF::getDB()->fetchArray($result)) {
			if ($row['Field'] != 'categoryID' && $row['Field'] != 'userID' && $row['Field'] != 'groupID') {
				// check modules
				switch ($row['Field']) {
					case 'canEditEntryComment':
					case 'canDeleteEntryComment':
						if (!MODULE_COMMENT) continue 2;
						break;
				}
				
				$settings[] = $row['Field'];
			}
		}
		return $settings;
	}
	
	/**
	 * Resets the category cache after changes.
	 */
	public static function resetCache() {
		// reset cache
		WCF::getCache()->clearResource('category');
		WCF::getCache()->clearResource('categoryData');
		
		// reset permissions cache
		WCF::getCache()->clear(WSIF_DIR.'cache/', 'cache.categoryPermissions-*', true);
		
		self::$categories = self::$categoryStructure = self::$categorySelect = null;
	}
}
?>