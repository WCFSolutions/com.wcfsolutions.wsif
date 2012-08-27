<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/category/CategoryEditor.class.php');
require_once(WSIF_DIR.'lib/data/entry/EntryEditor.class.php');
require_once(WSIF_DIR.'lib/data/entry/file/EntryFileEditor.class.php');
require_once(WSIF_DIR.'lib/data/entry/image/EntryImageEditor.class.php');

// wcf imports
require_once(WCF_DIR.'lib/form/MessageForm.class.php');
require_once(WCF_DIR.'lib/system/language/Language.class.php');

/**
 * Shows the entry add form.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	form
 * @category	Infinite Filebase
 */
class EntryAddForm extends MessageForm {
	// system
	public $templateName = 'entryAdd';
	public $useCaptcha = ENTRY_ADD_USE_CAPTCHA;
	public $showSignatureSetting = false;
	public $showAttachments = false;
	public $showPoll = false;

	/**
	 * category id
	 *
	 * @var	integer
	 */
	public $categoryID = 0;

	/**
	 * category editor object
	 *
	 * @var	CategoryEditor
	 */
	public $category = null;

	/**
	 * entry editor object
	 *
	 * @var	EntryEditor
	 */
	public $entry = null;

	/**
	 * image id
	 *
	 * @var	integer
	 */
	public $imageID = 0;

	/**
	 * image editor object
	 *
	 * @var	ImageEditor
	 */
	public $image = null;

	/**
	 * file id
	 *
	 * @var	integer
	 */
	public $fileID = 0;

	/**
	 * file editor object
	 *
	 * @var	FileEditor
	 */
	public $file = null;

	/**
	 * list of available languages
	 *
	 * @var	array
	 */
	public $availableLanguages = array();

	// form parameters
	public $prefixID = 0;
	public $username = '';
	public $teaser = '';
	public $preview, $send;
	public $languageID = 0;
	public $tags = '';

	// image
	public $imageUpload = null;

	// file
	public $fileType = 0;
	public $fileUpload = null;
	public $externalURL = '';

	protected $maxFileSize = 0;
	protected $maxFiles = 0;

	protected $maxImageSize = 0;
	protected $maxImages = 0;

	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		// get category
		if (isset($_REQUEST['categoryID'])) $this->categoryID = intval($_REQUEST['categoryID']);
		$this->category = new CategoryEditor($this->categoryID);
		$this->category->enter();

		// check permission
		if (!$this->category->canAddEntry()) {
			throw new PermissionDeniedException();
		}

		// flood control
		$this->messageTable = "wsif".WSIF_N."_entry";

		// get file quota
		$this->maxFileSize = WCF::getUser()->getPermission('user.filebase.maxEntryFileSize');
		$this->maxFiles = WCF::getUser()->getPermission('user.filebase.maxFilesPerEntry');

		// get image quota
		$this->maxImageSize = WCF::getUser()->getPermission('user.filebase.maxEntryImageSize');
		$this->maxImages = WCF::getUser()->getPermission('user.filebase.maxImagesPerEntry');
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
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();

		if (isset($_POST['username'])) $this->username = StringUtil::trim($_POST['username']);
		if (isset($_POST['prefixID']) && $this->category->getPermission('canSetEntryPrefix')) {
			$this->prefixID = intval($_POST['prefixID']);
		}
		if (isset($_POST['teaser'])) $this->teaser = StringUtil::trim($_POST['teaser']);
		if (isset($_POST['preview'])) $this->preview = (boolean) $_POST['preview'];
		if (isset($_POST['send'])) $this->send = (boolean) $_POST['send'];
		if (isset($_POST['languageID'])) $this->languageID = intval($_POST['languageID']);
		if (isset($_POST['tags'])) $this->tags = StringUtil::trim($_POST['tags']);

		// get image
		if (isset($_POST['imageID'])) $this->imageID = intval($_POST['imageID']);
		if ($this->imageID) {
			$this->image = new EntryImageEditor($this->imageID);
			if (!$this->image->imageID || $this->image->entryID != 0) {
				throw new IllegalLinkException();
			}
		}
		else {
			if (isset($_FILES['imageUpload'])) $this->imageUpload = $_FILES['imageUpload'];
		}

		// get file
		if (isset($_POST['fileID'])) $this->fileID = intval($_POST['fileID']);
		if ($this->fileID) {
			$this->file = new EntryFileEditor($this->fileID);
			if (!$this->file->fileID || $this->file->entryID != 0) {
				throw new IllegalLinkException();
			}
		}
		else {
			if (isset($_POST['fileType'])) $this->fileType = intval($_POST['fileType']);
			if (isset($_POST['externalURL'])) $this->externalURL = StringUtil::trim($_POST['externalURL']);
			if (isset($_FILES['fileUpload'])) $this->fileUpload = $_FILES['fileUpload'];
		}
	}

	/**
	 * @see Form::submit()
	 */
	public function submit() {
		// call submit event
		EventHandler::fireAction($this, 'submit');

		$this->readFormParameters();

		try {
			// preview
			if ($this->preview) {
				WCF::getTPL()->assign('preview', EntryEditor::createPreview($this->subject, $this->text, $this->enableSmilies, $this->enableHtml, $this->enableBBCodes));
			}

			// validate image
			if (isset($this->imageUpload['name']) && $this->imageUpload['name']) {
				$this->validateUsername();
				$this->validateImageUpload();
			}

			// upload file
			if ($this->file === null) {
				$this->validateUsername();
				if ($this->fileType == EntryFile::TYPE_UPLOAD) {
					if (isset($this->fileUpload['name']) && $this->fileUpload['name']) $this->validateFileUpload();
				}
				// add external file link
				else if ($this->fileType == EntryFile::TYPE_LINK) {
					$this->validateExternalFileLink();
				}
				// invalid file type
				else {
					throw new UserInputException('fileType', 'invalid');
				}
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
		// prefix id
		$this->validatePrefixID();

		// username
		$this->validateUsername();

		// subject, text, captcha
		parent::validate();

		// teaser
		$this->validateTeaser();

		// language
		$this->validateLanguage();

		// file
		$this->validateFile();
	}

	/**
	 * Validates the prefix id.
	 */
	protected function validatePrefixID() {
		$prefixes = $this->category->getPrefixes();
		if ($this->prefixID != 0 && !isset($prefixes[$this->prefixID])) {
			throw new UserInputException('prefixID', 'invalid');
		}
	}

	/**
	 * Validates the username.
	 */
	protected function validateUsername() {
		if (WCF::getUser()->userID == 0) {
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
	 * Validates the teaser.
	 */
	protected function validateTeaser() {
		if (empty($this->teaser)) {
			throw new UserInputException('teaser');
		}

		// check teaser length
		if (StringUtil::length($this->teaser) > 255) {
			throw new UserInputException('teaser', 'tooLong');
		}
	}

	/**
	 * Validates the language.
	 */
	protected function validateLanguage() {
		// language
		$availableLanguages = Language::getAvailableContentLanguages(PACKAGE_ID);
		if (count($availableLanguages) > 0) {
			if (!isset($availableLanguages[$this->languageID])) {
				$this->languageID = WCF::getLanguage()->getLanguageID();
				if (!isset($availableLanguages[$this->languageID])) {
					$languageIDs = array_keys($availableLanguages);
					$this->languageID = array_shift($languageIDs);
				}
			}
		}
		else {
			$this->languageID = 0;
		}
	}

	/**
	 * Validates the file.
	 */
	protected function validateFile() {
		if ($this->file === null) {
			throw new UserInputException('fileUpload');
		}
	}

	/**
	 * Validates the uploaded image.
	 */
	protected function validateImageUpload() {
		// check upload
		if ($this->imageUpload['error'] != 0) {
			throw new UserInputException('imageUpload', 'uploadFailed');
		}

		// create image
		$this->image = EntryImageEditor::create('imageUpload', 0, WCF::getUser()->userID, $this->username, $this->imageUpload['tmp_name'], $this->imageUpload['name'], '', '');
		$this->imageID = $this->image->imageID;
	}

	/**
	 * Validates the uploaded file.
	 */
	protected function validateFileUpload() {
		// check upload
		if ($this->fileUpload['error'] != 0) {
			throw new UserInputException('fileUpload', 'uploadFailed');
		}

		// create file
		$this->file = EntryFileEditor::create('fileUpload', 0, WCF::getUser()->userID, $this->username, $this->fileUpload['tmp_name'], $this->fileUpload['name'], $this->fileUpload['type'], '', '', $this->fileType);
		$this->fileID = $this->file->fileID;
	}

	/**
	 * Validates the external file link.
	 */
	protected function validateExternalFileLink() {
		if (empty($this->externalURL)) {
			throw new UserInputException('externalURL');
		}

		if (!FileUtil::isURL($this->externalURL)) {
			throw new UserInputException('externalURL', 'illegalURL');
		}

		// save file
		$this->file = EntryFileEditor::create('externalURL', 0, WCF::getUser()->userID, $this->username, '', '', '', '', '', $this->fileType, $this->externalURL);
		$this->fileID = $this->file->fileID;
	}

	/**
	 * @see Form::save()
	 */
	public function save() {
		parent::save();

		// save entry
		$this->entry = EntryEditor::create($this->category->categoryID, $this->languageID, $this->prefixID, $this->subject, $this->text, $this->teaser, WCF::getUser()->userID, $this->username, $this->getOptions(), null, intval(!$this->category->getPermission('canAddEntryWithoutModeration')));

		// save tags
		if (MODULE_TAGGING && ENTRY_ENABLE_TAGS && $this->category->getPermission('canSetEntryTags')) {
			$tagArray = TaggingUtil::splitString($this->tags);
			if (count($tagArray)) $this->entry->updateTags($tagArray);
		}

		// add image
		if ($this->image !== null) {
			$this->image->setEntryID($this->entry->entryID);
			$this->image->setAsDefault();
			$this->entry->updateImages(1);
		}

		// add file
		$this->file->setEntryID($this->entry->entryID);
		$this->file->setAsDefault();
		$this->entry->updateFiles(1);

		if ($this->category->getPermission('canAddEntryWithoutModeration')) {
			// update user entries
			if (WCF::getUser()->userID) {
				require_once(WSIF_DIR.'lib/data/user/WSIFUser.class.php');
				WSIFUser::updateUserEntries(WCF::getUser()->userID, 1);
				if (ACTIVITY_POINTS_PER_ENTRY) {
					require_once(WCF_DIR.'lib/data/user/rank/UserRank.class.php');
					UserRank::updateActivityPoints(ACTIVITY_POINTS_PER_ENTRY);
				}
			}

			// update category counter
			$this->category->updateEntries();

			// set last entry
			$this->category->setLastEntry($this->entry);

			// reset cache
			WCF::getCache()->clearResource('categoryData');
			WCF::getCache()->clearResource('stat');
			$this->saved();

			// forward to entry
			HeaderUtil::redirect('index.php?page=Entry&entryID='.$this->entry->entryID.SID_ARG_2ND_NOT_ENCODED);
		}
		else {
			$this->saved();
			WCF::getTPL()->assign(array(
				'url' => 'index.php?page=Category&categoryID='.$this->categoryID.SID_ARG_2ND_NOT_ENCODED,
				'message' => WCF::getLanguage()->get('wsif.entry.add.moderation.redirect'),
				'wait' => 5
			));
			WCF::getTPL()->display('redirect');
		}
		exit;
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'username' => $this->username,
			'teaser' => $this->teaser,
			'categoryID' => $this->category->categoryID,
			'category' => $this->category,
			'prefixID' => $this->prefixID,
			'languageID' => $this->languageID,
			'availableLanguages' => $this->availableLanguages,
			'tags' => $this->tags,
			'imageID' => $this->imageID,
			'image' => $this->image,
			'fileID' => $this->fileID,
			'file' => $this->file,
			'fileType' => $this->fileType,
			'externalURL' => $this->externalURL,
			'freeFiles' => $this->maxFiles,
			'maxFiles' => $this->maxFiles,
			'maxFileSize' => $this->maxFileSize,
			'allowedFileExtensions' => EntryFileEditor::getAllowedFileExtensionsDesc(),
			'freeImages' => $this->maxImages,
			'maxImages' => $this->maxImages,
			'maxImageSize' => $this->maxImageSize,
			'allowedImageExtensions' => EntryImageEditor::getAllowedImageExtensionsDesc()
		));
	}

	/**
	 * @see Page::show()
	 */
	public function show() {
		$this->loadAvailableLanguages();

		// show form
		parent::show();
	}

	/**
	 * Gets the available content languages.
	 */
	protected function loadAvailableLanguages() {
		if ($this->languageID == 0) $this->languageID = WCF::getLanguage()->getLanguageID();
		$this->availableLanguages = $this->getAvailableLanguages();

		if (!isset($this->availableLanguages[$this->languageID]) && count($this->availableLanguages) > 0) {
			$languageIDs = array_keys($this->availableLanguages);
			$this->languageID = array_shift($languageIDs);
		}
	}

	/**
	 * Returns a list of available languages.
	 *
	 * @return	array
	 */
	protected function getAvailableLanguages() {
		$visibleLanguages = explode(',', WCF::getUser()->languageIDs);
		$availableLanguages = Language::getAvailableContentLanguages(PACKAGE_ID);
		foreach ($availableLanguages as $key => $language) {
			if (!in_array($language['languageID'], $visibleLanguages)) {
				unset($availableLanguages[$key]);
			}
		}

		return $availableLanguages;
	}
}
?>