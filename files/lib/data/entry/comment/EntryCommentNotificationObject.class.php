<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/publication/object/comment/ViewablePublicationObjectComment.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/user/notification/object/NotificationObject.class.php');

/**
 * An implementation of NotificationObject to support the usage of a comment as a notification object.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	data.entry.comment
 * @category	Infinite Filebase
 */
class EntryCommentNotificationObject extends ViewableEntryComment implements NotificationObject {
	/**
	 * Creates a new EntryCommentNotificationObject object.
	 *
	 * @param	integer		$commentID
	 * @param 	array<mixed>	$row
	 */
	public function __construct($commentID, $row = null) {
		if (is_object($row)) {
			$row = $row->data;
		}
		parent::__construct($commentID, $row);
	}

	/**
	 * @see NotificationObject::getObjectID()
	 */
	public function getObjectID() {
		return $this->commentID;
	}

	/**
	 * @see NotificationObject::getTitle()
	 */
	public function getTitle() {
		return $this->getExcerpt();
	}

	/**
	 * @see NotificationObject::getURL()
	 */
	public function getURL() {
		return 'index.php?page=EntryComments&commentID='.$this->commentID;
	}

	/**
	 * @see NotificationObject::getIcon()
	 */
	public function getIcon() {
		return 'message';
	}

	/**
	 * @see ViewableEntryComment::getFormattedMessage()
	 */
	public function getFormattedMessage($outputType = 'text/html') {
		$comment = parent::getFormattedComment();
		if ($outputType == 'text/plain') {
			$comment = StringUtil::stripHTML($comment);
		}
		return $comment;
	}
}
?>