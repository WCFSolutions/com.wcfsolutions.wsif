<?php
// wsif imports
require_once(WSIF_DIR.'lib/action/AbstractEntryAction.class.php');

/**
 * Unsubscribes from an entry.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	action
 * @category	Infinite Filebase
 */
class EntryUnsubscribeAction extends AbstractEntryAction {
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();

		if (!WCF::getUser()->userID) {
			throw new PermissionDeniedException();
		}

		if (!$this->entry->isCommentable()) {
			throw new IllegalLinkException();
		}

		// unsubscribe entry
		if ($this->entry->subscribed) {
			$sql = "DELETE FROM 	wsif".WSIF_N."_entry_subscription
				WHERE 		userID = ".WCF::getUser()->userID."
						AND entryID = ".$this->entryID;
			WCF::getDB()->sendQuery($sql);
		}
		$this->executed();

		// forward
		HeaderUtil::redirect('index.php?page=Entry&entryID='.$this->entryID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
}
?>