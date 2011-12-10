<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/category/CategoryEditor.class.php');

// wcf imports
require_once(WCF_DIR.'lib/action/AbstractAction.class.php');

/**
 * Provides default implementations for category actions.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	acp.action
 * @category	Infinite Filebase
 */
abstract class AbstractCategoryAction extends AbstractAction {
	/**
	 * category id
	 *
	 * @var	integer
	 */
	public $categoryID = 0;
	
	/**
	 * category editor object
	 *
	 * @var	CategoryEditor
	 */
	public $category = null;
	
	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get category
		if (isset($_REQUEST['categoryID'])) $this->categoryID = intval($_REQUEST['categoryID']);
		$this->category = new CategoryEditor($this->categoryID);
	}
}
?>