<?php
// wsif imports
require_once(WSIF_DIR.'lib/page/ModerationEntriesPage.class.php');

/**
 * Shows the deleted entries in the recycle bin.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	page
 * @category	Infinite Filebase
 */
class ModerationDeletedEntriesPage extends ModerationEntriesPage {
	public $action = 'deletedEntries';
	public $neededPermissions = 'mod.filebase.canViewDeletedEntry';
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		$categoryIDs = Category::getModeratedCategories(array('canViewDeletedEntry'));
		$this->entryList->sqlConditions .= 'entry.isDeleted = 1 AND entry.categoryID IN ('.(!empty($categoryIDs) ? $categoryIDs : 0).')';
	}
}
?>