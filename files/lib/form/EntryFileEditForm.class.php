<?php
// wsif imports
require_once(WSIF_DIR.'lib/form/EntryFileAddForm.class.php');

/**
 * Shows the entry file edit form.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	form
 * @category	Infinite Filebase
 */
class EntryFileEditForm extends EntryFileAddForm {
	/**
	 * file id
	 *
	 * @var	integer
	 */
	public $fileID = 0;

	/**
	 * file editor object
	 *
	 * @var	EntryFileEditor
	 */
	public $file = null;

	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		AbstractForm::readParameters();

		// get file
		if (isset($_REQUEST['fileID'])) $this->fileID = intval($_REQUEST['fileID']);
		$this->file = new EntryFileEditor($this->fileID);
		if (!$this->file->fileID) {
			throw new IllegalLinkException();
		}

		// get entry frame
		$this->frame = new EntryFrame($this, $this->file->entryID);

		// check permission
		if (!$this->file->isEditable($this->frame->getCategory())) {
			throw new PermissionDeniedException();
		}
	}

	/**
	 * @see	Form::validate()
	 */
	public function validate() {
		AbstractForm::validate();

		// upload
		if ($this->file->isUpload()) {
			$this->validateUpload();
		}
		// external link
		else {
			$this->validateExternalLink();
		}

		if (empty($this->title)) {
			throw new UserInputException('title');
		}
	}

	/**
	 * Validates the uploaded files.
	 */
	protected function validateUpload() {
		if (isset($this->upload['name'][0])) {
			$errors = array();

			if (!empty($this->upload['name'][0])) {
				try {
					// check upload
					if ($this->upload['error'][0] != 0) {
						throw new UserInputException('upload', 'uploadFailed');
					}

					// replace file
					$this->file->replacePhysicalFile('upload', $this->upload['tmp_name'][0], $this->upload['name'][0], $this->upload['type'][0]);
				}
				catch (UserInputException $e) {
					$errors[] = array('errorType' => $e->getType(), 'filename' => $this->upload['name'][0]);
				}
			}

			// show error message
			if (count($errors)) {
				throw new UserInputException('upload', $errors);
			}
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
	}

	/**
	 * @see Form::save()
	 */
	public function save() {
		AbstractForm::save();

		// update file
		$this->file->update($this->title, $this->description, ($this->file->isExternalLink() ? $this->externalURL : $this->file->externalURL));
		$this->saved();

		// forward to file
		HeaderUtil::redirect('index.php?page=EntryFile&fileID='.$this->file->fileID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}

	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();

		if (!count($_POST)) {
			$this->title = $this->file->title;
			$this->description = $this->file->description;
			$this->externalURL = $this->file->externalURL;
		}
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'action' => 'edit',
			'fileID' =>  $this->fileID,
			'file' => $this->file
		));
	}
}
?>