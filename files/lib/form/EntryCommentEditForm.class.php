<?php
// wsif imports
require_once(WSIF_DIR.'lib/form/EntryCommentAddForm.class.php');

/**
 * Shows the entry comment edit form.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	form
 * @category	Infinite Filebase
 */
class EntryCommentEditForm extends EntryCommentAddForm {
	/**
	 * comment id
	 * 
	 * @var	integer
	 */
	public $commentID = 0;
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		MessageForm::readParameters();

		// get comment
		if (isset($_REQUEST['commentID'])) $this->commentID = intval($_REQUEST['commentID']);		
		$this->comment = new EntryCommentEditor($this->commentID);
		if (!$this->comment->commentID) {
			throw new IllegalLinkException();
		}
		
		// get entry frame
		$this->frame = new EntryFrame($this, $this->comment->entryID);
		
		// check permission
		if (!$this->comment->isEditable($this->frame->getCategory())) {
			throw new PermissionDeniedException();
		}
	}
	
	/**
	 * @see Form::save()
	 */
	public function save() {
		MessageForm::save();
		
		// update comment
		$this->comment->update($this->subject, $this->text, $this->getOptions(), $this->attachmentListEditor);
		$this->saved();
		
		// forward to entry
		HeaderUtil::redirect('index.php?page=EntryComments&commentID='.$this->comment->commentID.SID_ARG_2ND_NOT_ENCODED.'#comment'.$this->comment->commentID);
		exit;
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();

		if (!count($_POST)) {
			$this->subject = $this->comment->subject;
			$this->text = $this->comment->message;
			$this->enableSmilies =  $this->comment->enableSmilies;
			$this->enableHtml = $this->comment->enableHtml;
			$this->enableBBCodes = $this->comment->enableBBCodes;
		}
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'action' => 'edit',
			'commentID' => $this->commentID,
			'comment' => $this->comment
		));
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		// get attachments editor		
		$this->attachmentListEditor = new MessageAttachmentListEditor(array($this->commentID), 'entryComment', PACKAGE_ID, WCF::getUser()->getPermission('user.filebase.maxEntryCommentAttachmentSize'), WCF::getUser()->getPermission('user.filebase.allowedEntryCommentAttachmentExtensions'), WCF::getUser()->getPermission('user.filebase.maxEntryCommentAttachmentCount'));
		
		parent::show();
	}
}
?>