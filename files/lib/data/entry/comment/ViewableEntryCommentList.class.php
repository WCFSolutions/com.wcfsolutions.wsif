<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/entry/comment/EntryCommentList.class.php');
require_once(WSIF_DIR.'lib/data/entry/comment/ViewableEntryComment.class.php');

/**
 * Represents a viewable list of entry comments.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	data.entry.comment
 * @category	Infinite Filebase
 */
class ViewableEntryCommentList extends EntryCommentList {
	/**
	 * category object
	 *
	 * @var Category
	 */
	public $category = null;

	/**
	 * list of object ids
	 *
	 * @var	array<integer>
	 */
	public $objectIDArray = array();

	/**
	 * list of object ids
	 *
	 * @var	array<integer>
	 */
	public $attachmentCommentIDArray = array();

	/**
	 * attachment list object
	 *
	 * @var	MessageAttachmentList
	 */
	public $attachmentList = null;

	/**
	 * list of attachments
	 *
	 * @var	array
	 */
	public $attachments = array();

	/**
	 * Creates a new ViewableEntryCommentList object.
	 */
	public function __construct(Entry $entry, Category $category) {
		$this->category = $category;
		$this->sqlConditions = 'entry_comment.entryID = '.$entry->entryID;
		$this->sqlSelects = "user_option.*, wsif_user.*, user.*, rank.*, IFNULL(user.username, entry_comment.username) AS username";
		$this->sqlJoins = "	LEFT JOIN 	wcf".WCF_N."_user user
					ON 		(user.userID = entry_comment.userID)
					LEFT JOIN 	wsif".WSIF_N."_user wsif_user
					ON 		(wsif_user.userID = entry_comment.userID)
					LEFT JOIN 	wcf".WCF_N."_user_option_value user_option
					ON		(user_option.userID = entry_comment.userID)
					LEFT JOIN 	wcf".WCF_N."_user_rank rank
					ON		(rank.rankID = user.rankID)";

		if (MESSAGE_SIDEBAR_ENABLE_AVATAR) {
			$this->sqlSelects .= ', avatar.avatarID, avatar.avatarExtension, avatar.width, avatar.height';
			$this->sqlJoins .= ' LEFT JOIN wcf'.WCF_N.'_avatar avatar ON (avatar.avatarID = user.avatarID)';
		}
	}

	/**
	 * Gets the object ids.
	 */
	protected function readObjectIDArray() {
		$sql = "SELECT		entry_comment.commentID, entry_comment.attachments
			FROM		wsif".WSIF_N."_entry_comment entry_comment
			".(!empty($this->sqlConditions) ? "WHERE ".$this->sqlConditions : '')."
			".(!empty($this->sqlOrderBy) ? "ORDER BY ".$this->sqlOrderBy : '');
		$result = WCF::getDB()->sendQuery($sql, $this->sqlLimit, $this->sqlOffset);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$this->objectIDArray[] = $row['commentID'];
			if ($row['attachments']) $this->attachmentCommentIDArray[] = $row['commentID'];
		}
	}

	/**
	 * Gets a list of attachments.
	 */
	protected function readAttachments() {
		// read attachments
		if (MODULE_ATTACHMENT == 1 && count($this->attachmentCommentIDArray) > 0) {
			require_once(WCF_DIR.'lib/data/attachment/MessageAttachmentList.class.php');
			$this->attachmentList = new MessageAttachmentList($this->attachmentCommentIDArray, 'entryComment');
			$this->attachmentList->readObjects();
			$this->attachments = $this->attachmentList->getSortedAttachments($this->category->getPermission('canViewEntryCommentAttachmentPreview'));

			// set embedded attachments
			if ($this->category->getPermission('canViewEntryCommentAttachmentPreview')) {
				require_once(WCF_DIR.'lib/data/message/bbcode/AttachmentBBCode.class.php');
				AttachmentBBCode::setAttachments($this->attachments);
			}

			// remove embedded attachments from list
			if (count($this->attachments) > 0) {
				MessageAttachmentList::removeEmbeddedAttachments($this->attachments);
			}
		}
	}

	/**
	 * @see DatabaseObjectList::readObjects()
	 */
	public function readObjects() {
		// get comment ids
		$this->readObjectIDArray();

		// get comments
		if (count($this->objectIDArray)) {
			$this->readAttachments();

			$sql = "SELECT		".(!empty($this->sqlSelects) ? $this->sqlSelects.',' : '')."
						entry_comment.*
				FROM		wsif".WSIF_N."_entry_comment entry_comment
				".$this->sqlJoins."
				WHERE 		entry_comment.commentID IN (".implode(',', $this->objectIDArray).")
				".(!empty($this->sqlOrderBy) ? "ORDER BY ".$this->sqlOrderBy : '');
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				$this->comments[] = new ViewableEntryComment(null, $row);
			}
		}
	}

	/**
	 * Returns the list of attachments.
	 *
	 * @return	array
	 */
	public function getAttachments() {
		return $this->attachments;
	}
}
?>