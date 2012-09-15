<?php
// wsif imports
require_once(WSIF_DIR.'lib/page/ModerationEntriesPage.class.php');

/**
 * Shows the hidden entries.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	page
 * @category	Infinite Filebase
 */
class ModerationHiddenEntriesPage extends ModerationEntriesPage {
	public $neededPermissions = 'mod.filebase.canEnableEntry';
	public $action = 'hiddenEntries';

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