<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/category/CategoryEditor.class.php');
require_once(WSIF_DIR.'lib/data/entry/EntryEditor.class.php');
require_once(WSIF_DIR.'lib/data/entry/file/EntryFileEditor.class.php');
require_once(WSIF_DIR.'lib/data/entry/image/EntryImageEditor.class.php');

// wcf imports
require_once(WCF_DIR.'lib/form/MessageForm.class.php');
require_once(WCF_DIR.'lib/page/util/InlineCalendar.class.php');
require_once(WCF_DIR.'lib/system/language/Language.class.php');

/**
 * Shows the entry add form.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
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

	/**
	 * publishing time
	 *
	 * @var	integer
	 */
	public $publishingTime = 0;

	/**
	 * max image size
	 *
	 * @var integer
	 */
	protected $maxImageSize = 0;

	/**
	 * max file size
	 *
	 * @var integer
	 */
	protected $maxFileSize = 0;

	// form parameters
	public $prefixID = 0;
	public $username = '';
	public $teaser = '';
	public $preview, $send;
	public $languageID = 0;
	public $tags = '';
	public $publishingTimeDay = '';
	public $publishingTimeMonth = '';
	public $publishingTimeYear = '';
	public $publishingTimeHour = '';
	public $disableEntry = 0;
	public $enableComments = 1;
	public $imageUpload = null;
	public $fileType = 0;
	public $fileUpload = null;
	public $externalURL = '';

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

		// get image quota
		$this->maxImageSize = WCF::getUser()->getPermission('user.filebase.maxEntryImageSize');
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

		$this->enableComments = 0;

		if (isset($_POST['username'])) $this->username = StringUtil::trim($_POST['username']);
		if (isset($_POST['prefixID']) && $this->category->getPermission('canSetEntryPrefix')) {
			$this->prefixID = intval($_POST['prefixID']);
		}
		if (isset($_POST['teaser'])) $this->teaser = StringUtil::trim($_POST['teaser']);
		if (isset($_POST['preview'])) $this->preview = (boolean) $_POST['preview'];
		if (isset($_POST['send'])) $this->send = (boolean) $_POST['send'];
		if (isset($_POST['languageID'])) $this->languageID = intval($_POST['languageID']);
		if (isset($_POST['tags'])) $this->tags = StringUtil::trim($_POST['tags']);
		if ($this->category->getModeratorPermission('canEnableEntry')) {
			if (isset($_POST['publishingTimeDay'])) $this->publishingTimeDay = intval($_POST['publishingTimeDay']);
			if (isset($_POST['publishingTimeMonth'])) $this->publishingTimeMonth = intval($_POST['publishingTimeMonth']);
			if (!empty($_POST['publishingTimeYear'])) $this->publishingTimeYear = intval($_POST['publishingTimeYear']);
			if (isset($_POST['publishingTimeHour'])) $this->publishingTimeHour = intval($_POST['publishingTimeHour']);
			if (isset($_POST['disableEntry'])) $this->disableEntry = intval($_POST['disableEntry']);
		}
		if (isset($_POST['enableComments'])) $this->enableComments = intval($_POST['enableComments']);

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
					throw new UserInputException('fileType');
				}
			}

			// send message
			$this->validate();
			// no errors
			$this->save();
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

		// publishing time
		$this->validatePublishingTime();

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
	 * Validates the publishing time.
	 */
	protected function validatePublishingTime() {
		if ($this->publishingTimeDay || $this->publishingTimeMonth || $this->publishingTimeYear || $this->publishingTimeHour) {
			$time = @gmmktime($this->publishingTimeHour, 0, 0, $this->publishingTimeMonth, $this->publishingTimeDay, $this->publishingTimeYear);
			// since php5.1.0 mktime returns false on failure
			if ($time === false || $time === -1) {
				throw new UserInputException('publishingTime', 'invalid');
			}

			// get utc time
			$time = DateUtil::getUTC($time);
			if ($time <= TIME_NOW) {
				throw new UserInputException('publishingTime', 'invalid');
			}

			$this->publishingTime = $time;
			$this->disableEntry = 1;
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
		try {
			$this->image = EntryImageEditor::create('imageUpload', 0, WCF::getUser()->userID, $this->username, $this->imageUpload['tmp_name'], $this->imageUpload['name'], '', '');
		}
		catch (UserInputException $e) {
			throw new UserInputException('imageUpload', array('errorType' => $e->getType(), 'filename' => $this->imageUpload['name']));
		}
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
		try {
			$this->file = EntryFileEditor::create('fileUpload', 0, WCF::getUser()->userID, $this->username, $this->fileUpload['tmp_name'], $this->fileUpload['name'], $this->fileUpload['type'], '', '', $this->fileType);
		}
		catch (UserInputException $e) {
			throw new UserInputException('fileUpload', array('errorType' => $e->getType(), 'filename' => $this->fileUpload['name']));
		}
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
		$this->entry = EntryEditor::create($this->category->categoryID, $this->languageID, $this->prefixID, $this->subject, $this->text, $this->teaser, WCF::getUser()->userID, $this->username, $this->publishingTime, $this->enableComments, $this->getOptions(), null, intval(($this->disableEntry || !$this->category->getPermission('canAddEntryWithoutModeration'))));

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

		if (!$this->disableEntry && $this->category->getPermission('canAddEntryWithoutModeration')) {
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
			if ($this->disableEntry) {
				// forward to entry
				HeaderUtil::redirect('index.php?page=Entry&entryID='.$this->entry->entryID.SID_ARG_2ND_NOT_ENCODED);
			}
			else {
				WCF::getTPL()->assign(array(
					'url' => 'index.php?page=Category&categoryID='.$this->categoryID.SID_ARG_2ND_NOT_ENCODED,
					'message' => WCF::getLanguage()->get('wsif.entry.add.moderation.redirect'),
					'wait' => 5
				));
				WCF::getTPL()->display('redirect');
			}
		}
		exit;
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		InlineCalendar::assignVariables();

		WCF::getTPL()->assign(array(
			'username' => $this->username,
			'teaser' => $this->teaser,
			'categoryID' => $this->category->categoryID,
			'category' => $this->category,
			'prefixID' => $this->prefixID,
			'languageID' => $this->languageID,
			'availableLanguages' => $this->availableLanguages,
			'tags' => $this->tags,
			'publishingTimeDay' => $this->publishingTimeDay,
			'publishingTimeMonth' => $this->publishingTimeMonth,
			'publishingTimeYear' => $this->publishingTimeYear,
			'publishingTimeHour' => $this->publishingTimeHour,
			'disableEntry' => $this->disableEntry,
			'enableComments' => $this->enableComments,
			'imageID' => $this->imageID,
			'image' => $this->image,
			'fileID' => $this->fileID,
			'file' => $this->file,
			'fileType' => $this->fileType,
			'externalURL' => $this->externalURL,
			'maxFileSize' => $this->maxFileSize,
			'allowedFileExtensions' => EntryFileEditor::getAllowedFileExtensionsDesc(),
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