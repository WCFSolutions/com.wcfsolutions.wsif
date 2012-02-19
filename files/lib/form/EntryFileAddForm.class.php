<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/entry/EntryFrame.class.php');
require_once(WSIF_DIR.'lib/data/entry/file/EntryFileEditor.class.php');

// wcf imports
require_once(WCF_DIR.'lib/form/AbstractForm.class.php');

/**
 * Shows the entry file add form.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	form
 * @category	Infinite Filebase
 */
class EntryFileAddForm extends AbstractForm {
	// system
	public $templateName = 'entryFileAdd';
	
	/**
	 * entry frame
	 * 
	 * @var EntryFrame
	 */
	public $frame = null;
	
	/**
	 * number of possible files
	 * 
	 * @var	integer
	 */
	public $freeFiles = 0;
	
	/**
	 * max file size
	 *
	 * @var	integer
	 */
	public $maxFileSize = 0;
	
	/**
	 * number of max files
	 * 
	 * @var	integer
	 */
	public $maxFiles = 0;
	
	/**
	 * list of files
	 * 
	 * @var	array<EntryFileEditor>
	 */
	public $files = array();
	
	// form parameters
	public $fileType = 0;
	public $title = '';
	public $description = '';
	public $upload = null;
	public $externalURL = '';
	
	/**
	 * @see	Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get entry frame
		$this->frame = new EntryFrame($this);
		
		// check permission
		if (!$this->frame->getEntry()->isEditable($this->frame->getCategory())) {
			throw new PermissionDeniedException();
		}
		
		// get number of possible files
		$this->freeFiles = WCF::getUser()->getPermission('user.filebase.maxFilesPerEntry') - $this->frame->getEntry()->files;
		if ($this->freeFiles <= 0) {
			throw new NamedUserException(WCF::getLanguage()->get('wsif.entry.file.error.tooManyFiles'));
		}
		
		// get quota
		$this->maxFileSize = WCF::getUser()->getPermission('user.filebase.maxEntryFileSize');
		$this->maxFiles = WCF::getUser()->getPermission('user.filebase.maxFilesPerEntry');
	}
	
	/**
	 * @see	Form::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_POST['fileType'])) $this->fileType = intval($_POST['fileType']);
		if (isset($_POST['title'])) $this->title = StringUtil::trim($_POST['title']);
		if (isset($_POST['description'])) $this->description = StringUtil::trim($_POST['description']);
		if (isset($_POST['externalURL'])) $this->externalURL = StringUtil::trim($_POST['externalURL']);
		if (isset($_FILES['upload'])) $this->upload = $_FILES['upload'];
	}
	
	/**
	 * @see	Form::validate()
	 */
	public function validate() {
		parent::validate();
		
		// upload
		if ($this->fileType == EntryFile::TYPE_UPLOAD) {
			$this->validateUpload();
		}
		// external link
		else if ($this->fileType == EntryFile::TYPE_LINK) {
			$this->validateExternalLink();
		}
		// invalid file type
		else {
			throw new UserInputException('fileType', 'invalid');
		}
	}
	
	/**
	 * Validates the uploaded files.
	 */
	protected function validateUpload() {
		if (isset($this->upload['name']) && count($this->upload['name'])) {
			$errors = array();
			for ($x = 0, $y = count($this->upload['name']); $x < $y; $x++) {
				if (!empty($this->upload['name'][$x])) {
					try {
						// check free files
						if ($this->freeFiles <= 0) {
							throw new UserInputException('upload', 'tooManyFiles');
						}
						
						// check upload
						if ($this->upload['error'][$x] != 0) {
							throw new UserInputException('upload', 'uploadFailed');
						}
						
						// save file
						$this->files[] = EntryFileEditor::create('upload', $this->frame->getEntryID(), WCF::getUser()->userID, WCF::getUser()->username, $this->upload['tmp_name'][$x], $this->upload['name'][$x], $this->upload['type'][$x], $this->title, $this->description, $this->fileType);
						
						// update free files
						$this->freeFiles--;
					}
					catch (UserInputException $e) {
						$errors[] = array('errorType' => $e->getType(), 'filename' => $this->upload['name'][$x]);
					}
				}
			}
			
			// show success message
			if (count($this->files) > 0) WCF::getTPL()->assign('success', true);
			
			// show error message
			if (count($errors)) {
				throw new UserInputException('upload', $errors);
			}
			
		}
		else {
			throw new UserInputException('upload');
		}
	}
	
	/**
	 * Validates the external link.
	 */
	protected function validateExternalLink() {
		if (empty($this->externalURL)) {
			throw new UserInputException('externalURL');
		}
		
		if (!FileUtil::isURL($this->externalURL)) {
			throw new UserInputException('externalURL', 'illegalURL');
		}
		
		// save file
		$this->files[] = EntryFileEditor::create('externalURL', $this->frame->getEntryID(), WCF::getUser()->userID, WCF::getUser()->username, '', '', '', $this->title, $this->description, $this->fileType, $this->externalURL);
		
		// show success message
		WCF::getTPL()->assign('success', true);
	}
	
	/**
	 * @see	Form::save()
	 */
	public function save() {
		parent::save();
		
		// update file count
		$files = count($this->files);
		$this->frame->getEntry()->getEditor()->updateFiles($files);
		$this->frame->getCategory()->getEditor()->updateEntryFiles($files);
			
		// reset cache
		WCF::getCache()->clearResource('categoryData');
		WCF::getCache()->clearResource('stat');
		$this->saved();
		
		// reset values
		$this->title = $this->description = $this->externalURL = '';
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		$this->frame->assignVariables();
		WCF::getTPL()->assign(array(
			'action' => 'add',
			'title' => $this->title,
			'description' => $this->description,
			'fileType' => $this->fileType,
			'externalURL' => $this->externalURL,
			'files' => $this->files,
			'freeFiles' => $this->freeFiles,
			'maxFiles' => $this->maxFiles,
			'maxFileSize' => $this->maxFileSize,
			'allowedFileExtensions' => EntryFileEditor::getAllowedFileExtensionsDesc()
		));
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		// set active menu item
		require_once(WSIF_DIR.'lib/page/util/menu/EntryMenu.class.php');
		EntryMenu::getInstance()->setActiveMenuItem('wsif.entry.menu.link.entryFiles');
		
		parent::show();
	}
}
?>