<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/entry/EntryFrame.class.php');
require_once(WSIF_DIR.'lib/data/entry/comment/ViewableEntryCommentList.class.php');

// wcf imports
require_once(WCF_DIR.'lib/page/MultipleLinkPage.class.php');
require_once(WCF_DIR.'lib/data/message/sidebar/MessageSidebarFactory.class.php');

/**
 * Shows a list of entry comments.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
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
		
		// set active menu item
		require_once(WSIF_DIR.'lib/page/util/menu/EntryMenu.class.php');
		EntryMenu::getInstance()->setActiveMenuItem('wsif.entry.menu.link.entryComments');
		
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