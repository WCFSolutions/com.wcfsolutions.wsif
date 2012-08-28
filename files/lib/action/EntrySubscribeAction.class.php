<?php
// wsif imports
require_once(WSIF_DIR.'lib/action/AbstractEntryAction.class.php');

/**
 * Subscribes to an entry.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	action
 * @category	Infinite Filebase
 */
class EntrySubscribeAction extends AbstractEntryAction {
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();

		if (!WCF::getUser()->userID) {
			throw new PermissionDeniedException();
		}

		if (!$this->entry->isCommentable($this->category)) {
			throw new IllegalLinkException();
		}

		// subscribe entry
		if (!$this->entry->subscribed) {
			$sql = "INSERT INTO	wsif".WSIF_N."_entry_subscription
						(userID, entryID)
				VALUES 		(".WCF::getUser()->userID.", ".$this->entryID.")";
			WCF::getDB()->sendQuery($sql);
		}
		$this->executed();

		// forward
		HeaderUtil::redirect('index.php?page=EntryComments&entryID='.$this->entryID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
}
?>