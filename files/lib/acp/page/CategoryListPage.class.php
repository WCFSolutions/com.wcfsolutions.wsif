<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/category/Category.class.php');

// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');

/**
 * Shows a list of all categories.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	acp.page
 * @category	Infinite Filebase
 */
class CategoryListPage extends AbstractPage {
	// system
	public $templateName = 'categoryList';
	public $neededPermissions = array('admin.filebase.canEditCategory', 'admin.filebase.canDeleteCategory');

	/**
	 * list of categories
	 *
	 * @var	array
	 */
	public $categories = null;

	/**
	 * category structure
	 *
	 * @var	array
	 */
	public $categoryStructure = null;

	/**
	 * structured list of categories
	 *
	 * @var	array
	 */
	public $categoryList = array();

	/**
	 * category id
	 *
	 * @var	integer
	 */
	public $deletedCategoryID = 0;

	/**
	 * If the list was sorted successfully
	 *
	 * @var boolean
	 */
	public $successfulSorting = false;

	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		if (isset($_REQUEST['deletedCategoryID'])) $this->deletedCategoryID = intval($_REQUEST['deletedCategoryID']);
		if (isset($_REQUEST['successfulSorting'])) $this->successfulSorting = true;
	}

	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();

		// render categories
		$this->renderCategories();
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'categories' => $this->categoryList,
			'deletedCategoryID' => $this->deletedCategoryID,
			'successfulSorting' => $this->successfulSorting
		));
	}

	/**
	 * @see Page::show()
	 */
	public function show() {
		// set active menu item
		WCFACP::getMenu()->setActiveMenuItem('wsif.acp.menu.link.content.category.view');

		parent::show();
	}

	/**
	 * Renders the structured category list.
	 */
	public function renderCategories() {
		$this->categories = WCF::getCache()->get('category', 'categories');
		$this->categoryStructure = WCF::getCache()->get('category', 'categoryStructure');
		$this->makeCategoryList();
	}

	/**
	 * Renders one level of the category structure.
	 *
	 * @param	integer		$parentID
	 * @param	integer		$depth
	 * @param	integer		$openParents
	 */
	protected function makeCategoryList($parentID = 0, $depth = 1, $openParents = 0) {
		if (!isset($this->categoryStructure[$parentID])) return;

		$i = 0;
		$children = count($this->categoryStructure[$parentID]);
		foreach ($this->categoryStructure[$parentID] as $categoryID) {
			$category = $this->categories[$categoryID];

			// add category to list
			$childrenOpenParents = $openParents + 1;
			$hasChildren = isset($this->categoryStructure[$categoryID]);
			$last = $i == $children - 1;
			if ($hasChildren && !$last) $childrenOpenParents = 1;
			$this->categoryList[] = array(
				'depth' => $depth,
				'hasChildren' => $hasChildren,
				'openParents' => ((!$hasChildren && $last) ? $openParents : 0),
				'category' => $category,
				'parentID' => $parentID,
				'position' => $i + 1,
				'maxPosition' => $children
			);

			// make next level
			$this->makeCategoryList($categoryID, $depth + 1, $childrenOpenParents);

			$i++;
		}
	}
}
?>