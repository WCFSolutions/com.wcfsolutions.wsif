<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/entry/EntryFrame.class.php');
require_once(WSIF_DIR.'lib/data/entry/image/EntryImageEditor.class.php');

// wcf imports
require_once(WCF_DIR.'lib/form/AbstractForm.class.php');

/**
 * Shows the entry image add form.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	form
 * @category	Infinite Filebase
 */
class EntryImageAddForm extends AbstractForm {
	// system
	public $templateName = 'entryImageAdd';
	
	/**
	 * entry frame
	 * 
	 * @var EntryFrame
	 */
	public $frame = null;
	
	/**
	 * number of possible images
	 * 
	 * @var	integer
	 */
	public $freeImages = 0;
	
	/**
	 * max image size
	 *
	 * @var	integer
	 */
	protected $maxImageSize = 0;
	
	/**
	 * number of max images
	 * 
	 * @var	integer
	 */
	protected $maxImages = 0;
	
	/**
	 * list of images
	 * 
	 * @var	array<EntryImageEditor>
	 */
	public $images = array();
	
	// form parameters
	public $title = '';
	public $description = '';
	public $upload = null;
	
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
		
		// get number of possible images
		$this->freeImages = WCF::getUser()->getPermission('user.filebase.maxImagesPerEntry') - $this->frame->getEntry()->images;
		if ($this->freeImages <= 0) {
			throw new NamedUserException(WCF::getLanguage()->get('wsif.entry.image.error.tooManyImages'));
		}
		
		// get quota
		$this->maxImageSize = WCF::getUser()->getPermission('user.filebase.maxEntryImageSize');
		$this->maxImages = WCF::getUser()->getPermission('user.filebase.maxImagesPerEntry');
	}
	
	/**
	 * @see	Form::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_POST['title'])) $this->title = StringUtil::trim($_POST['title']);
		if (isset($_POST['description'])) $this->description = StringUtil::trim($_POST['description']);
		if (isset($_FILES['upload'])) $this->upload = $_FILES['upload'];
	}
	
	/**
	 * @see	Form::validate()
	 */
	public function validate() {
		parent::validate();
		
		// upload
		$this->validateUpload();
	}
	
	/**
	 * Validates the uploaded images.
	 */
	protected function validateUpload() {
		if (isset($this->upload['name']) && count($this->upload['name'])) {
			$errors = array();
			for ($x = 0, $y = count($this->upload['name']); $x < $y; $x++) {
				if (!empty($this->upload['name'][$x])) {
					try {
						// check free images
						if ($this->freeImages <= 0) {
							throw new UserInputException('upload', 'tooManyImages');
						}
						
						// check upload
						if ($this->upload['error'][$x] != 0) {
							throw new UserInputException('upload', 'uploadFailed');
						}
						
						// save image
						$this->images[] = EntryImageEditor::create('upload', $this->frame->getEntryID(), WCF::getUser()->userID, WCF::getUser()->username, $this->upload['tmp_name'][$x], $this->upload['name'][$x], $this->title, $this->description);
						
						// update free images
						$this->freeImages--;
					}
					catch (UserInputException $e) {
						$errors[] = array('errorType' => $e->getType(), 'filename' => $this->upload['name'][$x]);
					}
				}
			}
			
			// show success message
			if (count($this->images) > 0) WCF::getTPL()->assign('success', true);
			
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
	 * @see	Form::save()
	 */
	public function save() {
		parent::save();
		
		// update image count
		$images = count($this->images);
		$this->frame->getEntry()->getEditor()->updateImages($images);
		$this->frame->getCategory()->getEditor()->updateEntryImages($images);
			
		// reset cache
		WCF::getCache()->clearResource('categoryData');
		WCF::getCache()->clearResource('stat');
		$this->saved();
		
		// reset values
		$this->title = $this->description = '';
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
			'images' => $this->images,
			'freeImages' => $this->freeImages,
			'maxImages' => $this->maxImages,
			'maxImageSize' => $this->maxImageSize,
			'allowedImageExtensions' => EntryImageEditor::getAllowedImageExtensionsDesc()
		));
	}
}
?>