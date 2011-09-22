<?php
// wsif imports
require_once(WSIF_DIR.'lib/form/EntryImageAddForm.class.php');

/**
 * Shows the entry image edit form.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	form
 * @category	Infinite Filebase
 */
class EntryImageEditForm extends EntryImageAddForm {
	/**
	 * image id
	 *
	 * @var	integer
	 */
	public $imageID = 0;
	
	/**
	 * image object
	 * 
	 * @var	EntryImageEditor
	 */
	public $image = null;
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		AbstractForm::readParameters();

		// get image
		if (isset($_REQUEST['imageID'])) $this->imageID = intval($_REQUEST['imageID']);
		$this->image = new EntryImageEditor($this->imageID);
		if (!$this->image->imageID) {
			throw new IllegalLinkException();
		}
		
		// get entry frame
		$this->frame = new EntryFrame($this, $this->image->entryID);
		
		// check permission
		if (!$this->image->isEditable($this->frame->getCategory())) {
			throw new PermissionDeniedException();
		}
	}
	
	/**
	 * @see	Form::validate()
	 */
	public function validate() {
		AbstractForm::validate();
		
		if (empty($this->title)) {
			throw new UserInputException('title');
		}
	}
	
	/**
	 * @see Form::save()
	 */
	public function save() {
		AbstractForm::save();
		
		// update image
		$this->image->update($this->title, $this->description);
		$this->saved();
		
		// forward to image
		HeaderUtil::redirect('index.php?page=EntryImage&imageID='.$this->image->imageID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();

		if (!count($_POST)) {
			$this->title = $this->image->title;
			$this->description = $this->image->description;
		}
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'action' => 'edit',
			'imageID' =>  $this->imageID,
			'image' => $this->image
		));
	}
}
?>