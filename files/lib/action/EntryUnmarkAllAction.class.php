<?php
// wcf imports
require_once(WCF_DIR.'lib/action/AbstractSecureAction.class.php');

/**
 * Unmarks all entries.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	action
 * @category	Infinite Filebase
 */
class EntryUnmarkAllAction extends AbstractSecureAction {
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();

		// unmark
		WCF::getSession()->unregister('markedEntries');
		$this->executed();
	}
}
?>