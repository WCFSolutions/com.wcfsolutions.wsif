<?php
// wsif imports
require_once(WSIF_DIR.'lib/action/AbstractEntryAction.class.php');

/**
 * Recovers an entry.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	action
 * @category	Infinite Filebase
 */
class EntryRecoverAction extends AbstractEntryAction {
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();

		if (!ENTRY_ENABLE_RECYCLE_BIN) {
			throw new IllegalLinkException();
		}

		$this->category->checkModeratorPermission('canDeleteEntry');
		
		// recover entry
		if ($this->entry->isDeleted) {
			$this->entry->restore();

			// refresh last entry
			$this->category->refresh();
			if ($this->entry->time > $this->category->getLastEntryTime($this->entry->languageID)) {
				$this->category->setLastEntry($this->entry);
			}
			
			// reset cache
			WCF::getCache()->clearResource('categoryData', true);
			WCF::getCache()->clearResource('stat');
		}
		$this->executed();
	}
}
?>