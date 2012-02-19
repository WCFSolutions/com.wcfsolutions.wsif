<?php
// wsif imports
require_once(WSIF_DIR.'lib/page/ModerationEntriesPage.class.php');

/**
 * Shows the marked entries of the active user.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	page
 * @category	Infinite Filebase
 */
class ModerationMarkedEntriesPage extends ModerationEntriesPage {
	public $action = 'markedEntries';
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		$markedEntries = WCF::getSession()->getVar('markedEntries');
		$this->entryList->sqlConditions .= 'entry.entryID IN ('.($markedEntries ? implode(',', $markedEntries) : 0).')';
	}
}
?>