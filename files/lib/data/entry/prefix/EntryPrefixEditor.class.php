<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/entry/prefix/EntryPrefix.class.php');

// wcf imports
require_once(WCF_DIR.'lib/system/language/LanguageEditor.class.php');

/**
 * Provides functions to manage entry prefixes.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	data.entry.prefix
 * @category	Infinite Filebase
 */
class EntryPrefixEditor extends EntryPrefix {
	/**
	 * Creates a new EntryPrefixEditor object.
	 */
	public function __construct($prefixID, $row = null, $cacheObject = null, $useCache = true) {
		if ($useCache) parent::__construct($prefixID, $row, $cacheObject);
		else {
			$sql = "SELECT	*
				FROM	wsif".WSIF_N."_entry_prefix
				WHERE	prefixID = ".$prefixID;
			$row = WCF::getDB()->getFirstRow($sql);
			parent::__construct(null, $row);
		}
	}
	
	/**
	 * Assigns this prefix to the given categories.
	 * 
	 * @param	array		$categoryIDs
	 */
	public function assignCategories($categoryIDs) {
		$categoryIDs = array_unique($categoryIDs);
	
		$inserts = '';
		foreach ($categoryIDs as $categoryID) {
			if (!empty($inserts)) $inserts .= ',';
			$inserts .= '('.$categoryID.', '.$this->prefixID.')';
		}
	
		// insert new categories
		$sql = "INSERT IGNORE INTO 	wsif".WSIF_N."_entry_prefix_to_category
						(categoryID, prefixID)
			VALUES			".$inserts;
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Removes assigned categories.
	 */
	public function removeAssignedCategories() {
		$sql = "DELETE FROM 	wsif".WSIF_N."_entry_prefix_to_category
			WHERE		prefixID = ".$this->prefixID;
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Returns the list of assigned categories.
	 * 
	 * @return	array		list of category ids
	 */
	public function getAssignedCategories() {
		$categoryIDs = array();
		$sql = "SELECT	categoryID
			FROM	wsif".WSIF_N."_entry_prefix_to_category
			WHERE	prefixID = ".$this->prefixID;
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$categoryIDs[] = $row['categoryID'];
		}
		return $categoryIDs;
	}

	/**
	 * Updates this prefix.
	 * 
	 * @param	string		$prefixName
	 * @param	string		$prefixMarking
	 * @param	integer		$prefixType
	 * @param	integer		$showOrder
	 */
	public function update($prefixName, $prefixMarking = '%s', $prefixType = 0, $showOrder = 0, $languageID = 0) {
		// update show order
		if ($this->showOrder != $showOrder) {
			if ($showOrder < $this->showOrder) {
				$sql = "UPDATE	wsif".WSIF_N."_entry_prefix
					SET 	showOrder = showOrder + 1
					WHERE 	showOrder >= ".$showOrder."
						AND showOrder < ".$this->showOrder;
				WCF::getDB()->sendQuery($sql);
			}
			else if ($showOrder > $this->showOrder) {
				$sql = "UPDATE	wsif".WSIF_N."_entry_prefix
					SET	showOrder = showOrder - 1
					WHERE	showOrder <= ".$showOrder."
						AND showOrder > ".$this->showOrder;
				WCF::getDB()->sendQuery($sql);
			}
		}
		
		// update prefix
		$sql = "UPDATE	wsif".WSIF_N."_entry_prefix
			SET	".($languageID == 0 ? "prefix = '".escapeString($prefixName)."'," : '')."
				prefixMarking = '".escapeString($prefixMarking)."',
				prefixType = ".$prefixType.",
				showOrder = ".$showOrder."
			WHERE	prefixID = ".$this->prefixID;
		WCF::getDB()->sendQuery($sql);
		
		// update language items
		if ($languageID != 0) {
			// save language variables
			$language = new LanguageEditor($languageID);
			$language->updateItems(array('wsif.entry.prefix.'.$this->prefix => $prefixName), 0, PACKAGE_ID, array('wsif.entry.prefix.'.$this->prefix => 1));
			LanguageEditor::deleteLanguageFiles($languageID, 'wsif.entry.prefix', PACKAGE_ID);
			//$language->deleteCompiledTemplates();
		}
	}
	
	/**
	 * Deletes this prefix.
	 */
	public function delete() {
		// update show order
		$sql = "UPDATE	wsif".WSIF_N."_entry_prefix
			SET	showOrder = showOrder - 1
			WHERE	showOrder >= ".$this->showOrder;
		WCF::getDB()->sendQuery($sql);
		
		// delete prefix
		$sql = "DELETE FROM	wsif".WSIF_N."_entry_prefix
			WHERE		prefixID = ".$this->prefixID;
		WCF::getDB()->sendQuery($sql);
		
		// delete prefix to category
		$sql = "DELETE FROM	wsif".WSIF_N."_entry_prefix_to_category
			WHERE		prefixID = ".$this->prefixID;
		WCF::getDB()->sendQuery($sql);
		
		// update entries
		$sql = "UPDATE	wsif".WSIF_N."_entry
			SET	prefixID = 0
			WHERE	prefixID = ".$this->prefixID;
		WCF::getDB()->sendQuery($sql);
			
		// delete language variables
		LanguageEditor::deleteVariable('wsif.entry.prefix.'.$this->prefix);
		LanguageEditor::deleteVariable('wsif.entry.prefix.'.$this->prefix.'.description');		
	}
	
	/**
	 * Creates a new entry prefix.
	 * 
	 * @param	string			$prefixName
	 * @param	string			$prefixMarking
	 * @param	integer			$prefixType
	 * @param	integer			$showOrder
	 * @return	EntryPrefixEditor
	 */
	public static function create($prefixName, $prefixMarking = '%s', $prefixType = 0, $showOrder = 0, $languageID = 0) {
		// get show order
		if ($showOrder == 0) {
			// get next number in row
			$sql = "SELECT	MAX(showOrder) AS showOrder
				FROM	wsif".WSIF_N."_entry_prefix";
			$row = WCF::getDB()->getFirstRow($sql);
			if (!empty($row)) $showOrder = intval($row['showOrder']) + 1;
			else $showOrder = 1;
		}
		else {
			$sql = "UPDATE	wsif".WSIF_N."_entry_prefix
				SET 	showOrder = showOrder + 1
				WHERE 	showOrder >= ".$showOrder;
			WCF::getDB()->sendQuery($sql);
		}
		
		// get prefix name
		$prefix = '';
		if ($languageID == 0) $prefix = $prefixName;

		// save prefix
		$sql = "INSERT INTO	wsif".WSIF_N."_entry_prefix
					(prefix, prefixMarking, prefixType, showOrder)
			VALUES		('".escapeString($prefix)."', '".escapeString($prefixMarking)."', ".$prefixType.", ".$showOrder.")";
		WCF::getDB()->sendQuery($sql);
		
		// get id
		$prefixID = WCF::getDB()->getInsertID("wsif".WSIF_N."_entry_prefix", 'prefixID');
		
		// update language items
		if ($languageID != 0) {
			// set name
			$prefix = "prefix".$prefixID;
			$sql = "UPDATE	wsif".WSIF_N."_entry_prefix
				SET	prefix = '".escapeString($prefix)."'
				WHERE 	prefixID = ".$prefixID;
			WCF::getDB()->sendQuery($sql);
			
			// save language variables
			$language = new LanguageEditor($languageID);
			$language->updateItems(array('wsif.entry.prefix.'.$prefix => $prefixName));
		}
		
		// get new object
		$prefix = new EntryPrefixEditor($prefixID, null, null, false);
		
		// return object
		return $prefix;
	}
	
	/**
	 * Updates the positions of an entry prefix directly.
	 *
	 * @param	integer		$prefixID
	 * @param	integer		$showOrder
	 */
	public static function updateShowOrder($prefixID, $showOrder) {
		$sql = "UPDATE	wsif".WSIF_N."_entry_prefix
			SET	showOrder = ".$showOrder."
			WHERE 	prefixID = ".$prefixID;
		WCF::getDB()->sendQuery($sql);
	}
}
?>