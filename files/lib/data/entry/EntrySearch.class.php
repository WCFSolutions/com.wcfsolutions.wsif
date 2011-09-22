<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/entry/EntrySearchResult.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/message/search/AbstractSearchableMessageType.class.php');

/**
 * An implementation of SearchableMessageType for searching in entries.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	data.entry
 * @category	Infinite Filebase
 */
class EntrySearch extends AbstractSearchableMessageType {
	public $messageCache = array();
	public $categoryIDs = array();
	
	public $categories = array();
	public $categoryStructure = array();
	public $selectedCategories = array();
	
	/**
	 * @see SearchableMessageType::cacheMessageData()
	 */
	public function cacheMessageData($messageIDs, $additionalData = null) {
		// get entries
		$sql = "SELECT		entry.*,
					entry_image.imageID, entry_image.hasThumbnail
			FROM		wsif".WSIF_N."_entry entry
			LEFT JOIN	wsif".WSIF_N."_entry_image entry_image
			ON		(entry_image.imageID = entry.defaultImageID)
			WHERE		entry.entryID IN (".$messageIDs.")";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$entry = new EntrySearchResult(null, $row);
			$this->messageCache[$row['entryID']] = array('type' => 'entry', 'message' => $entry);
		}
	}
	
	/**
	 * @see SearchableMessageType::getMessageData()
	 */
	public function getMessageData($messageID, $additionalData = null) {
		if (isset($this->messageCache[$messageID])) return $this->messageCache[$messageID];
		return null;
	}
	
	/**
	 * @see SearchableMessageType::show()
	 */
	public function show($form = null) {		
		// get existing values
		if ($form !== null && isset($form->searchData['additionalData']['entry'])) {
			$this->categoryIDs = $form->searchData['additionalData']['entry']['categoryIDs'];
		}
		
		WCF::getTPL()->assign(array(
			'categoryOptions' => Category::getCategorySelect(array('canViewCategory', 'canEnterCategory', 'canViewEntry')),
			'categoryIDs' => $this->categoryIDs,
			'selectAllCategories' => count($this->categoryIDs) == 0 || $this->categoryIDs[0] == '*'
		));
	}
	
	/**
	 * Reads the given form parameters.
	 *
	 * @param	Form		$form
	 */
	protected function readFormParameters($form = null) {
		// get existing values
		if ($form !== null && isset($form->searchData['additionalData']['entry'])) {
			$this->categoryIDs = $form->searchData['additionalData']['entry']['categoryIDs'];
		}
		
		// get new values
		if (isset($_POST['categoryIDs']) && is_array($_POST['categoryIDs'])) {
			$this->categoryIDs = ArrayUtil::toIntegerArray($_POST['categoryIDs']);
		}
	}
	
	/**
	 * @see SearchableMessageType::getConditions()
	 */
	public function getConditions($form = null) {
		$this->readFormParameters($form);
		
		$categoryIDs = $this->categoryIDs;
		if (count($categoryIDs) && $categoryIDs[0] == '*') $categoryIDs = array();
		
		// remove empty elements
		foreach ($categoryIDs as $key => $categoryID) {
			if ($categoryID == '-') unset($categoryIDs[$key]);
		}
		
		// get categories
		require_once(WSIF_DIR.'lib/data/category/Category.class.php');
		$this->categories = WCF::getCache()->get('category', 'categories');
		$this->categoryStructure = WCF::getCache()->get('category', 'categoryStructure');
		$this->selectedCategories = array();
		
		// check whether the selected category does exist
		foreach ($categoryIDs as $categoryID) {
			if (!isset($this->categories[$categoryID])) {
				throw new UserInputException('categoryIDs', 'notValid');
			}
			
			if (!isset($this->selectedCategories[$categoryID])) {
				$this->selectedCategories[$categoryID] = $this->categories[$categoryID];
				
				// include children
				$this->includeSubCategories($categoryID);
			}
		}
		if (count($this->selectedCategories) == 0) $this->selectedCategories = $this->categories;
		
		// check permission of the active user
		foreach ($this->selectedCategories as $category) {
			if (!$category->getPermission() || !$category->getPermission('canEnterCategory') || !$category->getPermission('canViewEntry')) {
				unset($this->selectedCategories[$category->categoryID]);
			}
		}
		
		if (count($this->selectedCategories) == 0) {
			throw new PermissionDeniedException();
		}
		
		// get selected category ids
		$selectedCategoryIDs = '';
		if (count($this->selectedCategories) != count($this->categories)) {
			foreach ($this->selectedCategories as $category) {
				if (!empty($selectedCategoryIDs)) $selectedCategoryIDs .= ',';
				$selectedCategoryIDs .= $category->categoryID;
			}
		}
		
		// build final condition
		require_once(WCF_DIR.'lib/system/database/ConditionBuilder.class.php');
		$condition = new ConditionBuilder(false);
		
		// category ids
		if (!empty($selectedCategoryIDs)) {
			$condition->add('messageTable.categoryID IN ('.$selectedCategoryIDs.')');
		}
		$condition->add('messageTable.isDeleted = 0');
		$condition->add('messageTable.isDisabled = 0');
		
		// language
		if (count(WCF::getSession()->getVisibleLanguageIDArray())) $condition->add('messageTable.languageID IN ('.implode(',', WCF::getSession()->getVisibleLanguageIDArray()).')');
		
		// return sql condition
		return $condition->get();
	}
	
	/**
	 * Includes the sub categories of the given category id to the selected category list.
	 *
	 * @param	integer		$categoryID
	 */
	private function includeSubCategories($categoryID) {
		if (isset($this->categoryStructure[$categoryID])) {
			foreach ($this->categoryStructure[$categoryID] as $childCategoryID) {
				if (!isset($this->selectedCategories[$childCategoryID])) {
					$this->selectedCategories[$childCategoryID] = $this->categories[$childCategoryID];
					
					// include children
					$this->includeSubCategories($childCategoryID);
				}
			}
		}
	}
	
	/**
	 * Returns the database table name for this search type.
	 */
	public function getTableName() {
		return 'wsif'.WSIF_N.'_entry';
	}
	
	/**
	 * Returns the message id field name for this search type.
	 */
	public function getIDFieldName() {
		return 'entryID';
	}
	
	/**
	 * @see SearchableMessageType::getAdditionalData()
	 */
	public function getAdditionalData() {
		return array(
			'categoryIDs' => $this->categoryIDs
		);
	}
	
	/**
	 * @see SearchableMessageType::isAccessible()
	 */
	public function isAccessible() {
		return count(Category::getCategorySelect(array('canViewCategory', 'canEnterCategory', 'canViewEntry'))) > 0;
	}
	
	/**
	 * @see SearchableMessageType::getFormTemplateName()
	 */
	public function getFormTemplateName() {
		return 'searchEntry';
	}
	
	/**
	 * @see SearchableMessageType::getResultTemplateName()
	 */
	public function getResultTemplateName() {
		return 'searchResultEntry';
	}
}
?>