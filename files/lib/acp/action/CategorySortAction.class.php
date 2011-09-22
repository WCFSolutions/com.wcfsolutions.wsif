<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/category/CategoryEditor.class.php');

// wcf imports
require_once(WCF_DIR.'lib/action/AbstractAction.class.php');

/**
 * Sorts the structure of categories.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	acp.action
 * @category	Infinite Filebase
 */
class CategorySortAction extends AbstractAction {
	/**
	 * new positions
	 *
	 * @var array
	 */
	public $positions = array();
	
	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_POST['categoryListPositions']) && is_array($_POST['categoryListPositions'])) $this->positions = ArrayUtil::toIntegerArray($_POST['categoryListPositions']);
	}
	
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();
		
		// check permission
		WCF::getUser()->checkPermission('admin.filebase.canEditCategory');

		// delete old positions
		$sql = "TRUNCATE wsif".WSIF_N."_category_structure";
		WCF::getDB()->sendQuery($sql);
		
		// update postions
		foreach ($this->positions as $categoryID => $data) {
			foreach ($data as $parentID => $position) {
				CategoryEditor::updatePosition(intval($categoryID), intval($parentID), $position);
			}
		}
		
		// insert default values
		$sql = "INSERT IGNORE INTO	wsif".WSIF_N."_category_structure
						(parentID, categoryID)
			SELECT			parentID, categoryID
			FROM			wsif".WSIF_N."_category";
		WCF::getDB()->sendQuery($sql);
		
		// reset cache
		WCF::getCache()->clearResource('category');
		$this->executed();
		
		// forward to list page
		HeaderUtil::redirect('index.php?page=CategoryList&successfulSorting=1&packageID='.PACKAGE_ID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
}
?>