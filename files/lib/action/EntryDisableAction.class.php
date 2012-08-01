<?php
// wsif imports
require_once(WSIF_DIR.'lib/action/AbstractEntryAction.class.php');

/**
 * Disables an entry.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	action
 * @category	Infinite Filebase
 */
class EntryDisableAction extends AbstractEntryAction {
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();

		// check permission
		$this->category->checkModeratorPermission('canEnableEntry');

		// disable entry
		if (!$this->entry->isDisabled) {
			$this->entry->disable();

			// refresh category last entry
			$this->category->refresh();
			if ($this->entry->time == $this->category->getLastEntryTime($this->entry->languageID)) {
				$this->category->setLastEntries();
			}

			// reset cache
			WCF::getCache()->clearResource('categoryData', true);
			WCF::getCache()->clearResource('stat');
		}
		$this->executed();
	}
}
?>