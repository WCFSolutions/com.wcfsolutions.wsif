<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/entry/comment/EntryCommentNotificationObject.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/user/notification/object/AbstractNotificationObjectType.class.php');

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
class EntryCommentNotificationObjectType extends AbstractNotificationObjectType {
	/**
	 * @see NotificationObjectType::getObjectByID()
	 */
	public function getObjectByID($objectID) {
		// get object
		$comment = new EntryCommentNotificationObject($objectID);
		if (!$comment->commentID) return null;

		// return object
		return $comment;
	}

	/**
	 * @see NotificationObjectType::getObjectByObject()
	 */
	public function getObjectByObject($object) {
		// build object using its data array
		$comment = new EntryCommentNotificationObject(null, $object);
		if (!$comment->commentID) return null;

		// return object
		return $comment;
	}

	/**
	 * @see NotificationObjectType::getObjectsByIDArray()
	 */
	public function getObjectsByIDArray($objectIDArray) {
		$comments = array();
		$sql = "SELECT		*
			FROM 		wsif".WSIF_N."_entry_comment
			WHERE 		commentID IN (".implode(',', $objectIDArray).")";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$comments[$row['commentID']] = new EntryCommentNotificationObject(null, $row);
		}

		return $comments;
	}

	/**
	 * @see NotificationObjectType::getPackageID()
	 */
	public function getPackageID() {
		return PACKAGE_ID;
	}
}
?>