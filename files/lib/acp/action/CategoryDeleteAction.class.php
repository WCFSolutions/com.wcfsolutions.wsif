<?php
// wsif imports
require_once(WSIF_DIR.'lib/acp/action/AbstractCategoryAction.class.php');

/**
 * Deletes a category.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	acp.action
 * @category	Infinite Filebase
 */
class CategoryDeleteAction extends AbstractCategoryAction {
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();
		
		// check permission
		WCF::getUser()->checkPermission('admin.filebase.canDeleteCategory');
		
		// delete category
		$this->category->delete();
		
		// reset cache
		WCF::getCache()->clearResource('category');
		$this->executed();
		
		// forward to list page
		HeaderUtil::redirect('index.php?page=CategoryList&deletedCategoryID='.$this->categoryID.'&packageID='.PACKAGE_ID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
}
?>