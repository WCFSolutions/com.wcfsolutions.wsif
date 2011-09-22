<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/entry/TaggedEntryList.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/DatabaseObjectList.class.php');

/**
 * TaggedCategoryEntryList provides extended functions for displaying a list of entries of a specific tag in a category.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	data.entry
 * @category	Infinite Filebase
 */
class TaggedCategoryEntryList extends TaggedEntryList {
	public $sqlSelectRating = '';

	/**
	 * Creates a new TaggedCategoryEntryList object.
	 */
	public function __construct($tagID, Category $category, $daysPrune = 100, $prefixID = 0, $languageID = 0, $enableRating = ENTRY_ENABLE_RATING) {
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
		
		// ratings
		if ($enableRating) {
			$this->sqlSelectRating = $this->sqlSelects = "if (ratings>0 AND ratings>=".ENTRY_MIN_RATINGS.",rating/ratings,0) AS ratingResult";
		}
		parent::__construct($tagID);
	}
	
	/**
	 * @see ViewableEntryList::readObjectIDArray()
	 */
	protected function readObjectIDArray() {
		$sql = "SELECT		entry.entryID, ".$this->sqlSelectRating."
			FROM		wcf".WCF_N."_tag_to_object tag_to_object,
					wsif".WSIF_N."_entry entry
			WHERE		tag_to_object.tagID = ".$this->tagID."
					AND tag_to_object.taggableID = ".$this->taggable->getTaggableID()."
					AND entry.entryID = tag_to_object.objectID
					".(!empty($this->sqlConditions) ? "AND ".$this->sqlConditions : '')."
			".(!empty($this->sqlOrderBy) ? "ORDER BY ".$this->sqlOrderBy : '');
		$result = WCF::getDB()->sendQuery($sql, $this->sqlLimit, $this->sqlOffset);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$this->objectIDArray[] = $row['entryID'];
		}
	}
}
?>