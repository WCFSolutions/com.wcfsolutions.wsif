<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/category/Category.class.php');
require_once(WSIF_DIR.'lib/data/entry/Entry.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/user/group/Group.class.php');

/**
 * Shows a list of categories.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	data.category
 * @category	Infinite Filebase
 */
class CategoryList {
	protected $categoryID = 0;
	protected $categoryStructure;
	protected $categories;
	protected $categoryList = array();
	protected $lastEntries = array();
	protected $subCategories = array();
	protected $categoryStats = array();

	/**
	 * Creates a new CategoryList object.
	 *
	 * @param 	integer		$categoryID		id of the parent category
	 */
	public function __construct($categoryID = 0) {
		$this->categoryID = $categoryID;
	}

	/**
	 * Reads the categories.
	 */
	public function readCategories() {
		// get category structure
		$this->categoryStructure = WCF::getCache()->get('category', 'categoryStructure');

		if (!isset($this->categoryStructure[$this->categoryID])) return;

		// get categories
		$this->categories = WCF::getCache()->get('category', 'categories');

		// last entries
		if (CATEGORY_LIST_ENABLE_LAST_ENTRY) {
			$lastEntries = WCF::getCache()->get('categoryData', 'lastEntries');

			if (is_array($lastEntries)) {
				$visibleLanguages = false;
				if (count(WCF::getSession()->getVisibleLanguageIDArray())) {
					$visibleLanguages = WCF::getSession()->getVisibleLanguageIDArray();
				}

				foreach ($lastEntries as $categoryID => $languages) {
					foreach ($languages as $languageID => $row) {
						if (!$languageID || !$visibleLanguages || in_array($languageID, $visibleLanguages)) {
							$this->lastEntries[$row['categoryID']] = new Entry(null, $row);
							continue 2;
						}
					}
				}
			}
		}

		// stats
		if (CATEGORY_LIST_ENABLE_STATS) {
			$this->categoryStats = WCF::getCache()->get('categoryData', 'stats');
		}

		// clear and update category list
		$this->clearCategoryList($this->categoryID);
		$this->updateCategoryList($this->categoryID);

		// categories
		if (isset($this->categoryStructure[$this->categoryID])) {
			foreach ($this->categoryStructure[$this->categoryID] as $categoryID) {
				$this->categoryList[$categoryID] = $this->categories[$categoryID];

				// sub categories
				if (CATEGORY_LIST_ENABLE_SUB_CATEGORIES) {
					if (!isset($this->categoryStructure[$categoryID])) continue;
					foreach ($this->categoryStructure[$categoryID] as $subCategoryID) {
						$this->subCategories[$categoryID][] = $this->categories[$subCategoryID];
					}
				}
			}
		}
	}

	/**
	 * Removes invisible categories from category list.
	 *
	 * @param	integer		parentID
	 */
	protected function clearCategoryList($parentID = 0) {
		if (!isset($this->categoryStructure[$parentID])) return;

		// remove invisible categories
		foreach ($this->categoryStructure[$parentID] as $key => $categoryID) {
			$category = $this->categories[$categoryID];
			if (!$category->getPermission()) {
				unset($this->categoryStructure[$parentID][$key]);
				continue;
			}
			$this->clearCategoryList($categoryID);
		}

		if (!count($this->categoryStructure[$parentID])) {
			unset($this->categoryStructure[$parentID]);
		}
	}

	/**
	 * Updates one level of the category structure.
	 *
	 * @param	integer		$parentID
	 */
	protected function updateCategoryList($parentID = 0) {
		if (!isset($this->categoryStructure[$parentID])) return;

		foreach ($this->categoryStructure[$parentID] as $categoryID) {
			$category = $this->categories[$categoryID];

			// update next level of the category list
			$this->updateCategoryList($categoryID);

			// user can not enter category - unset last entry
			if (!$category->getPermission('canEnterCategory') && isset($this->lastEntries[$categoryID])) {
				unset($this->lastEntries[$categoryID]);
			}

			// update last entries
			if ($parentID != 0 && CATEGORY_LIST_ENABLE_LAST_ENTRY) {
				if (isset($this->lastEntries[$categoryID])) {
					if (!isset($this->lastEntries[$parentID]) || $this->lastEntries[$categoryID]->time > $this->lastEntries[$parentID]->time) {
						$this->lastEntries[$parentID] = $this->lastEntries[$categoryID];
					}
				}
			}

			// update parent stats
			if ($parentID != 0 && CATEGORY_LIST_ENABLE_STATS) {
				if (isset($this->categoryStats[$parentID]) && isset($this->categoryStats[$categoryID])) {
					$this->categoryStats[$parentID]['entries'] += $this->categoryStats[$categoryID]['entries'];
					$this->categoryStats[$parentID]['entryComments'] += $this->categoryStats[$categoryID]['entryComments'];
					$this->categoryStats[$parentID]['entryImages'] += $this->categoryStats[$categoryID]['entryImages'];
					$this->categoryStats[$parentID]['entryFiles'] += $this->categoryStats[$categoryID]['entryFiles'];
					$this->categoryStats[$parentID]['entryDownloads'] += $this->categoryStats[$categoryID]['entryDownloads'];
				}
			}
		}
	}

	/**
	 * Returns the category list.
	 *
	 * @return array
	 */
	public function getCategoryList() {
		return $this->categoryList;
	}

	/**
	 * Assigns the variables to the template engine.
	 */
	public function assignVariables() {
		// categories
		WCF::getTPL()->assign('categories', $this->categoryList);
		// last entries
		if (CATEGORY_LIST_ENABLE_LAST_ENTRY) {
			WCF::getTPL()->assign('lastEntries', $this->lastEntries);
		}
		// sub categories
		if (CATEGORY_LIST_ENABLE_SUB_CATEGORIES) {
			WCF::getTPL()->assign('subCategories', $this->subCategories);
		}
		// stats
		if (CATEGORY_LIST_ENABLE_STATS) {
			WCF::getTPL()->assign('categoryStats', $this->categoryStats);
		}
	}
}
?>