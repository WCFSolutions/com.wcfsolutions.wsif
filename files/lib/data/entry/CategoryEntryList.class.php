<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/entry/ViewableEntryList.class.php');

/**
 * Represents a viewable list of entries in a category.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	data.entry
 * @category	Infinite Filebase
 */
class CategoryEntryList extends ViewableEntryList {
	/**
	 * Creates a new CategoryEntryList object.
	 */
	public function __construct(Category $category, $daysPrune = 100, $prefixID = 0, $languageID = 0, $enableRating = ENTRY_ENABLE_RATING) {
		$this->sqlConditions = "entry.categoryID = ".$category->categoryID;

		// days prune
		if ($daysPrune != 1000) {
			$this->sqlConditions .= " AND entry.time >= ".(TIME_NOW - ($daysPrune * 86400));
		}

		// visible status
		if (!$category->getModeratorPermission('canViewDeletedEntry')) {
			$this->sqlConditions .= ' AND entry.isDeleted = 0';
		}
		if (!$category->getModeratorPermission('canEnableEntry')) {
			$this->sqlConditions .= ' AND entry.isDisabled = 0';
		}

		// prefix
		if ($prefixID != -1) {
			$this->sqlConditions .= " AND entry.prefixID = ".$prefixID;
		}

		// language
		if ($languageID != 0) {
			$this->sqlConditions .= " AND entry.languageID = ".$languageID;
		}
		else if (count(WCF::getSession()->getVisibleLanguageIDArray()) && (CATEGORY_ENTRIES_ENABLE_LANGUAGE_FILTER_FOR_GUESTS == 1 || WCF::getUser()->userID != 0)) {
			$this->sqlConditions .= " AND entry.languageID IN (".implode(',', WCF::getSession()->getVisibleLanguageIDArray()).")";
		}
	}

	/**
	 * Gets the object ids.
	 */
	protected function readObjectIDArray() {
		$sql = "SELECT		entry.entryID
			FROM		wsif".WSIF_N."_entry entry
			".(!empty($this->sqlConditions) ? "WHERE ".$this->sqlConditions : '')."
			".(!empty($this->sqlOrderBy) ? "ORDER BY ".$this->sqlOrderBy : '');
		$result = WCF::getDB()->sendQuery($sql, $this->sqlLimit, $this->sqlOffset);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$this->objectIDArray[] = $row['entryID'];
		}
	}
}
?>