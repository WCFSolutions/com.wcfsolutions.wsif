<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/entry/comment/EntryComment.class.php');

/**
 * Provides functions to manage entry comments.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	data.entry.comment
 * @category	Infinite Filebase
 */
class EntryCommentEditor extends EntryComment {	
	/**
	 * Updates this comment.
	 * 
	 * @param	string				$subject
	 * @param	string				$message
	 * @param	array				$options 
	 * @param	MessageAttachmentListEditor	$attachmentList 
	 */
	public function update($subject, $message, $options = array(), $attachmentList = null) {
		// get number of attachments
		$attachmentsAmount = ($attachmentList !== null ? count($attachmentList->getAttachments($this->commentID)) : 0);
		
		// update data
		$sql = "UPDATE	wsif".WSIF_N."_entry_comment
			SET	subject = '".escapeString($subject)."',
				message = '".escapeString($message)."',
				attachments = ".$attachmentsAmount.",
				enableSmilies = ".(isset($options['enableSmilies']) ? $options['enableSmilies'] : 1).",
				enableHtml = ".(isset($options['enableHtml']) ? $options['enableHtml'] : 0).",
				enableBBCodes = ".(isset($options['enableBBCodes']) ? $options['enableBBCodes'] : 1)."
			WHERE	commentID = ".$this->commentID;
		WCF::getDB()->sendQuery($sql);
		
		// update attachments
		if ($attachmentList != null) {
			$attachmentList->findEmbeddedAttachments($message);
		}
	}
	
	/**
	 * Deletes this comment.
	 */
	public function delete() {
		self::deleteAll($this->commentID);
	}
	
	/**
	 * Creates a new entry comment.
	 * 
	 * @param	integer				$userID
	 * @param	string				$username
	 * @param	string				$subject
	 * @param	string				$message
	 * @param	array				$options
	 * @param	MessageAttachmentListEditor	$attachmentList
	 * @return	UserBlogEntryEditor
	 */
	public static function create($entryID, $userID, $username, $subject, $message, $options = array(), $attachmentList = null) {
		// get number of attachments
		$attachmentsAmount = ($attachmentList !== null ? count($attachmentList->getAttachments()) : 0);
		
		// save entry
		$sql = "INSERT INTO	wsif".WSIF_N."_entry_comment
					(entryID, userID, username, subject, message, time, attachments, enableSmilies, enableHtml, enableBBCodes)
			VALUES		(".$entryID.", ".$userID.", '".escapeString($username)."', '".escapeString($subject)."', '".escapeString($message)."', ".TIME_NOW.", ".$attachmentsAmount.",
					".(isset($options['enableSmilies']) ? $options['enableSmilies'] : 1).",
					".(isset($options['enableHtml']) ? $options['enableHtml'] : 0).",
					".(isset($options['enableBBCodes']) ? $options['enableBBCodes'] : 1).")";
		WCF::getDB()->sendQuery($sql);
		
		// get id
		$commentID = WCF::getDB()->getInsertID("wsif".WSIF_N."_entry_comment", 'commentID');
		
		// get new object
		$comment = new EntryCommentEditor($commentID);
		
		// update attachments
		if ($attachmentList !== null) {
			$attachmentList->updateContainerID($commentID);
			$attachmentList->findEmbeddedAttachments($message);
		}
		
		// return object
		return $comment;
	}
	
	/**
	 * Creates a preview of an entry comment.
	 *
	 * @param 	string		$message
	 * @param 	boolean		$enableSmilies
	 * @param 	boolean		$enableHtml
	 * @param 	boolean		$enableBBCodes
	 * @return	string
	 */
	public static function createPreview($message, $enableSmilies = 1, $enableHtml = 0, $enableBBCodes = 1) {
		$row = array(
			'entryID' => 0,
			'message' => $message,
			'enableSmilies' => $enableSmilies,
			'enableHtml' => $enableHtml,
			'enableBBCodes' => $enableBBCodes,
			'messagePreview' => true
		);

		require_once(WSIF_DIR.'lib/data/entry/comment/ViewableEntryComment.class.php');
		$comment = new ViewableEntryComment(null, $row);
		return $comment->getFormattedMessage();
	}
	
	/**
	 * Deletes all comments with the given comment ids.
	 *
	 * @param	string		$commentIDs
	 * @param	boolean		$deleteAttachments
	 */
	public static function deleteAll($commentIDs, $deleteAttachments = true) {
		if (empty($commentIDs)) return;
		
		// delete attachments
		if ($deleteAttachments) {
			require_once(WCF_DIR.'lib/data/attachment/MessageAttachmentListEditor.class.php');
			$attachment = new MessageAttachmentListEditor(explode(',', $commentIDs), 'entryComment');
			$attachment->deleteAll();
		}
		
		// delete comments
		$sql = "DELETE FROM	wsif".WSIF_N."_entry_comment
			WHERE		commentID IN (".$commentIDs.")";
		WCF::getDB()->sendQuery($sql);
	}
}
?>