<?php
// wsif imports
require_once(WSIF_DIR.'lib/page/ModerationEntriesPage.class.php');

/**
 * Shows the hidden entries.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	page
 * @category	Infinite Filebase
 */
class ModerationHiddenEntriesPage extends ModerationEntriesPage {
	public $action = 'hiddenEntries';
	public $neededPermissions = 'mod.filebase.canEnableEntry';
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		$categoryIDs = Category::getModeratedCategories(array('canEnableEntry'));
		$this->entryList->sqlConditions .= 'entry.isDisabled = 1 AND entry.categoryID IN ('.(!empty($categoryIDs) ? $categoryIDs : 0).')';
	}
}
?>