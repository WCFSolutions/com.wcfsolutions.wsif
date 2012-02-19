<?php
// wcf imports
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');

/**
 * Checks the download permission for entry comment attachments.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	system.event.listener
 * @category	Infinite Filebase
 */
class AttachmentPageEntryCommentPermissionListener implements EventListener {
	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		$attachment = $eventObj->attachment;
		if ($attachment['containerID'] && $attachment['containerType'] == 'entryComment') {
			// get comment
			require_once(WSIF_DIR.'lib/data/entry/comment/EntryComment.class.php');
			$comment = new EntryComment($attachment['containerID']);
			
			// get entry
			require_once(WSIF_DIR.'lib/data/entry/Entry.class.php');
			$entry = new Entry($comment->entryID);
			
			// get category
			require_once(WSIF_DIR.'lib/data/category/Category.class.php');
			$category = Category::getCategory($entry->categoryID);
			$entry->enter($category);
			
			// check download permission
			if (!$category->getPermission('canDownloadEntryCommentAttachment') && (!$eventObj->thumbnail || !$category->getPermission('canViewEntryCommentAttachmentPreview'))) {
				throw new PermissionDeniedException();
			}
		}
	}
}
?>