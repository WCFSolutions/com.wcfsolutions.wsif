<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/entry/comment/EntryComment.class.php');
require_once(WSIF_DIR.'lib/data/user/WSIFUser.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/message/bbcode/MessageParser.class.php');
require_once(WCF_DIR.'lib/data/message/bbcode/AttachmentBBCode.class.php');
require_once(WCF_DIR.'lib/data/message/sidebar/MessageSidebarObject.class.php');

/**
 * Represents a viewable entry comment in the filebase.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	data.entry.comment
 * @category	Infinite Filebase
 */
class ViewableEntryComment extends EntryComment implements MessageSidebarObject {
	protected $user;
	protected $entry;
	
	/**
	 * Creates a new ViewablePost object.
	 *
	 * @param 	integer 	$commentID
	 * @param 	array 		$row
	 * @param 	Entry		$entry
	 */
	public function __construct($commentID, $row = null, $entry = null) {
		parent::__construct($commentID, $row);
		$this->entry = $entry;
	}
	
	/**
	 * @see DatabaseObject::handleData()
	 */
	protected function handleData($data) {
		parent::handleData($data);
		$this->user = new WSIFUser(null, $data);
	}
	
	/**
	 * Returns the formatted message.
	 *
	 * @return 	string
	 */
	public function getFormattedMessage() {
		$parser = MessageParser::getInstance();
		$parser->setOutputType('text/html');
		AttachmentBBCode::setMessageID($this->commentID);
		return $parser->parse($this->message, $this->enableSmilies, $this->enableHtml, $this->enableBBCodes, !$this->messagePreview);
	}
	
	// MessageSidebarObject implementation
	/**
	 * @see MessageSidebarObject::getUser()
	 */
	public function getUser() {
		return $this->user;
	}
	
	/**
	 * @see MessageSidebarObject::getMessageID()
	 */
	public function getMessageID() {
		return $this->commentID;
	}
	
	/**
	 * @see MessageSidebarObject::getMessageType()
	 */
	public function getMessageType() {
		return 'entryComment';
	}
}
?>