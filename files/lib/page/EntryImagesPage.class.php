<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/entry/EntryFrame.class.php');
require_once(WSIF_DIR.'lib/data/entry/image/EntryImageList.class.php');

// wcf imports
require_once(WCF_DIR.'lib/page/MultipleLinkPage.class.php');

/**
 * Shows a list of entry images.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	page
 * @category	Infinite Filebase
 */
class EntryImagesPage extends MultipleLinkPage {
	// system
	public $templateName = 'entryImages';
	
	/**
	 * list of entry images
	 *
	 * @var EntryImageList
	 */
	public $imageList = null;
	
	/**
	 * entry frame object
	 * 
	 * @var EntryFrame
	 */
	public $frame = null;
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get entry frame
		$this->frame = new EntryFrame($this);
		
		// init image list
		$this->imageList = new EntryImageList();
		$this->imageList->sqlConditions .= 'entry_image.entryID = '.$this->frame->getEntryID();
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		// read objects
		$this->imageList->sqlOffset = ($this->pageNo - 1) * $this->itemsPerPage;
		$this->imageList->sqlLimit = $this->itemsPerPage;
		$this->imageList->sqlOrderBy = 'entry_image.isDefault DESC, entry_image.uploadTime DESC';
		$this->imageList->readObjects();
	}
	
	/**
	 * @see MultipleLinkPage::countItems()
	 */
	public function countItems() {
		parent::countItems();
		
		return $this->imageList->countObjects();
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		$this->frame->assignVariables();
		WCF::getTPL()->assign(array(
			'images' => $this->imageList->getObjects(),
			'allowSpidersToIndexThisPage' => true
		));
	}
}
?>