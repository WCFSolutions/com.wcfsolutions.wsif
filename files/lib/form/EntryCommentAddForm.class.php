<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/entry/EntryFrame.class.php');
require_once(WSIF_DIR.'lib/data/entry/comment/EntryCommentEditor.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/attachment/MessageAttachmentListEditor.class.php');
require_once(WCF_DIR.'lib/form/MessageForm.class.php');
require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');
require_once(WCF_DIR.'lib/system/language/Language.class.php');

/**
 * Shows the entry comment add form.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	form
 * @category	Infinite Filebase
 */
class EntryCommentAddForm extends MessageForm {
	// system
	public $templateName = 'entryCommentAdd';
	public $showSignatureSetting = false;
	public $showPoll = false;
	
	/**
	 * entry frame object
	 * 
	 * @var EntryFrame
	 */
	public $frame = null;
	
	/**
	 * comment editor object
	 * 
	 * @var	EntryCommentEditor
	 */
	public $comment = null;
	
	/**
	 * attachment list editor object
	 * 
	 * @var	MessageAttachmentListEditor
	 */
	public $attachmentListEditor = null;

	// form parameters
	public $username = '';
	public $preview, $send;
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get entry frame
		$this->frame = new EntryFrame($this);
		
		// check permissions
		if (!$this->frame->getEntry()->isCommentable($this->frame->getCategory())) {
			throw new PermissionDeniedException();
		}
		
		// flood control
		$this->messageTable = "wsif".WSIF_N."_entry_comment";
	}

	/**
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_POST['username'])) $this->username = StringUtil::trim($_POST['username']);
		if (isset($_POST['preview'])) $this->preview = (boolean) $_POST['preview'];
		if (isset($_POST['send'])) $this->send = (boolean) $_POST['send'];
	}
	
	/**
	 * @see Form::submit()
	 */
	public function submit() {
		// call submit event
		EventHandler::fireAction($this, 'submit');
		
		$this->readFormParameters();
		
		try {
			// attachment handling
			if ($this->showAttachments) {
				$this->attachmentListEditor->handleRequest();
			}
			
			// preview
			if ($this->preview) {
				require_once(WCF_DIR.'lib/data/message/bbcode/AttachmentBBCode.class.php');
				AttachmentBBCode::setAttachments($this->attachmentListEditor->getSortedAttachments());
				WCF::getTPL()->assign('preview', EntryCommentEditor::createPreview($this->text, $this->enableSmilies, $this->enableHtml, $this->enableBBCodes));
			}
			
			// send message
			if ($this->send) {
				$this->validate();
				// no errors
				$this->save();
			}
		}
		catch (UserInputException $e) {
			$this->errorField = $e->getField();
			$this->errorType = $e->getType();
		}
	}
	
	/**
	 * @see Form::validate()
	 */
	public function validate() {
		parent::validate();
		
		// username
		$this->validateUsername();
		
		// subject
		if (empty($this->subject)) {
			throw new UserInputException('subject');
		}
	}
	
	/**
	 * Does nothing.
	 */
	protected function validateSubject() {}
	
	/**
	 * Validates the username.
	 */
	protected function validateUsername() {
		// only for guests
		if (WCF::getUser()->userID == 0) {
			// username
			if (empty($this->username)) {
				throw new UserInputException('username');
			}
			if (!UserUtil::isValidUsername($this->username)) {
				throw new UserInputException('username', 'notValid');
			}
			if (!UserUtil::isAvailableUsername($this->username)) {
				throw new UserInputException('username', 'notAvailable');
			}
			
			WCF::getSession()->setUsername($this->username);
		}
		else {
			$this->username = WCF::getUser()->username;
		}
	}
	
	/**
	 * @see Form::save()
	 */
	public function save() {		
		parent::save();
		
		// save comment
		$this->comment = EntryCommentEditor::create($this->frame->getEntryID(), WCF::getUser()->userID, $this->username, $this->subject, $this->text, $this->getOptions(), $this->attachmentListEditor);
		$this->saved();
			
		// forward to comment
		HeaderUtil::redirect('index.php?page=EntryComments&commentID='.$this->comment->commentID.SID_ARG_2ND_NOT_ENCODED.'#comment'.$this->comment->commentID);
		exit;
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		// get username
		if (!count($_POST)) {
			$this->username = WCF::getSession()->username;
		}
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		$this->frame->assignVariables();
		WCF::getTPL()->assign(array(
			'action' => 'add',
			'username' => $this->username
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
		
		// check upload permission
		if (MODULE_ATTACHMENT != 1 || !$this->frame->getCategory()->getPermission('canUploadEntryCommentAttachment')) {
			$this->showAttachments = false;
		}
		
		// get attachments editor
		if ($this->attachmentListEditor == null) {
			$this->attachmentListEditor = new MessageAttachmentListEditor(array(), 'entryComment', PACKAGE_ID, WCF::getUser()->getPermission('user.filebase.maxEntryCommentAttachmentSize'), WCF::getUser()->getPermission('user.filebase.allowedEntryCommentAttachmentExtensions'), WCF::getUser()->getPermission('user.filebase.maxEntryCommentAttachmentCount'));
		}

		// show form
		parent::show();
	}
}
?>