<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/entry/EntryFrame.class.php');
require_once(WSIF_DIR.'lib/data/entry/comment/ViewableEntryCommentList.class.php');

// wcf imports
require_once(WCF_DIR.'lib/page/MultipleLinkPage.class.php');
require_once(WCF_DIR.'lib/data/message/sidebar/MessageSidebarFactory.class.php');
require_once(WCF_DIR.'lib/data/user/notification/NotificationHandler.class.php');

/**
 * Shows a list of entry comments.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	page
 * @category	Infinite Filebase
 */
class EntryCommentsPage extends MultipleLinkPage {
	// system
	public $templateName = 'entryComments';

	/**
	 * comment id
	 *
	 * @var	integer
	 */
	public $commentID = 0;

	/**
	 * comment object
	 *
	 * @var	EntryComment
	 */
	public $comment = null;

	/**
	 * list of entry comments
	 *
	 * @var EntryCommentList
	 */
	public $commentList = null;

	/**
	 * entry frame object
	 *
	 * @var EntryFrame
	 */
	public $frame = null;

	/**
	 * sidebar factory object
	 *
	 * @var	MessageSidebarFactory
	 */
	public $sidebarFactory = null;

	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		// get comment
		if (isset($_REQUEST['commentID'])) {
			$this->commentID = intval($_REQUEST['commentID']);
			$this->comment = new EntryComment($this->commentID);
			if (!$this->comment->commentID) {
				throw new IllegalLinkException();
			}
		}

		// get entry frame
		$this->frame = new EntryFrame($this, ($this->commentID ? $this->comment->entryID : null));

		// check comment availability
		if (!$this->frame->getEntry()->enableComments) {
			throw new IllegalLinkException();
		}

		// init comment list
		$this->commentList = new ViewableEntryCommentList($this->frame->getEntry(), $this->frame->getCategory());

		// go to comment
		if ($this->commentID) $this->goToComment();
	}

	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();

		// read objects
		$this->commentList->sqlOffset = ($this->pageNo - 1) * $this->itemsPerPage;
		$this->commentList->sqlLimit = $this->itemsPerPage;
		$this->commentList->readObjects();

		// init sidebars
		$this->sidebarFactory = new MessageSidebarFactory($this);
		foreach ($this->commentList->getObjects() as $comment) {
			$this->sidebarFactory->create($comment);
		}
		$this->sidebarFactory->init();

		// confirm notifications
		$user = new NotificationUser(null, WCF::getUser(), false);
		$objectTypeObject = NotificationHandler::getNotificationObjectTypeObject('entryComment');
		$packageID = $objectTypeObject->getPackageID();
		if (isset($user->notificationFlags[$packageID]) && $user->notificationFlags[$packageID] > 0) {
			$commentIDArray = array();
			foreach ($this->commentList->getObjects() as $commentID => $comment) {
				$commentIDArray[] = $commentID;
			}

			$count = NotificationEditor::markConfirmedByObjectVisit($user->userID, array('newEntryComment', 'entrySubscription'), 'entryComment', $commentIDArray);
			$user->removeOutstandingNotification($packageID, $count);
		}
	}

	/**
	 * @see MultipleLinkPage::countItems()
	 */
	public function countItems() {
		parent::countItems();

		return $this->commentList->countObjects();
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		$this->frame->assignVariables();
		WCF::getTPL()->assign(array(
			'comments' => $this->commentList->getObjects(),
			'attachments' => $this->commentList->getAttachments(),
			'sidebarFactory' => $this->sidebarFactory,
			'allowSpidersToIndexThisPage' => true
		));
	}

	/**
	 * @see Page::show()
	 */
	public function show() {
		// check module
		if (MODULE_COMMENT != 1) {
			throw new IllegalLinkException();
		}

		parent::show();
	}

	/**
	 * Calculates the position of a specific comment.
	 */
	protected function goToComment() {
		$sql = "SELECT	COUNT(*) AS entries
			FROM 	wsif".WSIF_N."_entry_comment entry_comment
			WHERE 	".$this->commentList->sqlConditions."
				".(!empty($this->commentList->sqlConditions) ? 'AND ' : '')."time >= ".$this->comment->time;
		$result = WCF::getDB()->getFirstRow($sql);
		$this->pageNo = intval(ceil($result['entries'] / $this->itemsPerPage));
	}
}
?>