<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/category/Category.class.php');
require_once(WSIF_DIR.'lib/data/entry/EntryFrame.class.php');
require_once(WSIF_DIR.'lib/data/entry/comment/EntryComment.class.php');
require_once(WSIF_DIR.'lib/data/entry/file/EntryFile.class.php');
require_once(WSIF_DIR.'lib/data/entry/image/EntryImage.class.php');
require_once(WSIF_DIR.'lib/data/user/WSIFUser.class.php');

// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');

/**
 * Shows the entry page.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	page
 * @category	Infinite Filebase
 */
class EntryPage extends AbstractPage {
	// system
	public $templateName = 'entry';

	/**
	 * entry frame object
	 *
	 * @var EntryFrame
	 */
	public $frame = null;

	/**
	 * list of entry comments
	 *
	 * @var	array
	 */
	public $entryComments = array();

	/**
	 * list of entry images
	 *
	 * @var	array
	 */
	public $entryImages = array();

	/**
	 * list of entry files
	 *
	 * @var	array
	 */
	public $entryFiles = array();

	/**
	 * list of entry tags
	 *
	 * @var array
	 */
	public $tags = array();

	/**
	 * list of entry visitors
	 *
	 * @var array<WSIFUser>
	 */
	public $entryVisitors = array();

	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		// get entry frame
		$this->frame = new EntryFrame($this);

		// update views
		if (!WCF::getSession()->spiderID) {
			$this->updateViews();
		}
	}

	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();

		// get entry comments
		if (ENTRY_SHOW_LAST_COMMENTS) {
			$sql = "SELECT		commentID, userID, username, subject, time
				FROM		wsif".WSIF_N."_entry_comment
				WHERE		entryID = ".$this->frame->getEntryID()."
				ORDER BY	time DESC";
			$result = WCF::getDB()->sendQuery($sql, 5);
			while ($row = WCF::getDB()->fetchArray($result)) {
				$this->entryComments[] = new EntryComment(null, $row);
			}
		}

		// get entry images
		if (ENTRY_SHOW_LAST_IMAGES) {
			$sql = "SELECT		imageID, userID, username, title, uploadTime
				FROM		wsif".WSIF_N."_entry_image
				WHERE		entryID = ".$this->frame->getEntryID()."
				ORDER BY	isDefault, uploadTime DESC";
			$result = WCF::getDB()->sendQuery($sql, 5);
			while ($row = WCF::getDB()->fetchArray($result)) {
				$this->entryImages[] = new EntryImage(null, $row);
			}
		}

		// get entry files
		if (ENTRY_SHOW_LAST_FILES) {
			$sql = "SELECT		fileID, userID, username, title, uploadTime
				FROM		wsif".WSIF_N."_entry_file
				WHERE		entryID = ".$this->frame->getEntryID()."
				ORDER BY	isDefault, uploadTime DESC";
			$result = WCF::getDB()->sendQuery($sql, 5);
			while ($row = WCF::getDB()->fetchArray($result)) {
				$this->entryFiles[] = new EntryFile(null, $row);
			}
		}

		// get tags
		$this->tags = $this->frame->getEntry()->getTags(WCF::getSession()->getVisibleLanguageIDArray());

		// get entry visitors
		$sql = "SELECT		avatar.*, user_table.*, visitor.*
			FROM		wsif".WSIF_N."_entry_visitor visitor
			LEFT JOIN 	wcf".WCF_N."_user user_table
			ON 		(user_table.userID = visitor.userID)
			LEFT JOIN 	wcf".WCF_N."_avatar avatar
			ON 		(avatar.avatarID = user_table.avatarID)
			WHERE		visitor.entryID = ".$this->frame->getEntryID()."
			ORDER BY	time DESC";
		$result = WCF::getDB()->sendQuery($sql, 5);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$this->entryVisitors[] = new WSIFUser(null, $row);
		}
	}

	/**
	* @see Page::assignVariables();
	 */
	public function assignVariables() {
		parent::assignVariables();

		$this->frame->assignVariables();
		WCF::getTPL()->assign(array(
			'entryComments' => $this->entryComments,
			'entryImages' => $this->entryImages,
			'entryFiles' => $this->entryFiles,
			'tags' => $this->tags,
			'entryVisitors' => $this->entryVisitors,
			'allowSpidersToIndexThisPage' => true
		));
	}

	/**
	 * Updates the views of this entry.
	 */
	public function updateViews() {
		// update entry views
		$sql = "UPDATE	wsif".WSIF_N."_entry
			SET	views = views + 1
			WHERE	entryID = ".$this->frame->getEntryID();
		WCF::getDB()->registerShutdownUpdate($sql);

		// save visitor
		if (WCF::getUser()->userID && !WCF::getUser()->invisible) {
			$sql = "INSERT INTO			wsif".WSIF_N."_entry_visitor
								(entryID, userID, time)
				VALUES				(".$this->frame->getEntryID().", ".WCF::getUser()->userID.", ".TIME_NOW.")
				ON DUPLICATE KEY UPDATE		time = VALUES(time)";
			WCF::getDB()->registerShutdownUpdate($sql);
		}
	}
}
?>