<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/entry/EntryFrame.class.php');
require_once(WSIF_DIR.'lib/data/entry/image/EntryImage.class.php');
require_once(WSIF_DIR.'lib/data/user/WSIFUser.class.php');

// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');

/**
 * Shows the entry image page.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	page
 * @category	Infinite Filebase
 */
class EntryImagePage extends AbstractPage {
	// system
	public $templateName = 'entryImage';	
	public $itemsPerPage = ENTRY_IMAGES_PER_PAGE;
	
	/**
	 * image id
	 *
	 * @var	integer
	 */
	public $imageID = 0;
	
	/**
	 * image object
	 * 
	 * @var	EntryImage
	 */
	public $image = null;
	
	/**
	 * entry frame object
	 * 
	 * @var EntryFrame
	 */
	public $frame = null;
	
	/**
	 * previous image
	 * 
	 * @var	EntryImage
	 */
	public $previousImage = null;
	
	/**
	 * next image
	 * 
	 * @var	EntryImage
	 */
	public $nextImage = null;

	/**
	 * @see Page::readParameters()
	 */	
	public function readParameters() {
		parent::readParameters();
		
		// get image
		if (isset($_REQUEST['imageID'])) $this->imageID = intval($_REQUEST['imageID']);
		$this->image = new EntryImage($this->imageID);
		if (!$this->image->imageID) {
			throw new IllegalLinkException();
		}
		
		// get entry frame
		$this->frame = new EntryFrame($this, $this->image->entryID);

		// update views
		if (!WCF::getSession()->spiderID) {
			$this->updateViews();
		}
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
	
		// get previous image
		if (!$this->image->isDefault) {
			$sql = "SELECT		imageID, title, uploadTime
				FROM		wsif".WSIF_N."_entry_image
				WHERE		entryID = ".$this->frame->getEntryID()."
						AND (
							isDefault = 1
							OR (
								uploadTime > ".$this->image->uploadTime."
								OR (uploadTime = ".$this->image->uploadTime." AND imageID < ".$this->image->imageID.")
							)
						)
				ORDER BY	isDefault ASC, uploadTime ASC, imageID DESC";
			$this->previousImage = new EntryImage(null, WCF::getDB()->getFirstRow($sql));
			if (!$this->previousImage->imageID) $this->previousImage = null;
		}
		
		// get next image
		$sql = "SELECT		imageID, title, uploadTime
			FROM		wsif".WSIF_N."_entry_image
			WHERE		entryID = ".$this->frame->getEntryID()."
					AND isDefault = 0
					".(!$this->image->isDefault ? "AND (
						uploadTime < ".$this->image->uploadTime."
						OR (uploadTime = ".$this->image->uploadTime." AND imageID > ".$this->image->imageID.")
					)": '')."
			ORDER BY	uploadTime DESC, imageID ASC";
		$this->nextImage = new EntryImage(null, WCF::getDB()->getFirstRow($sql));
		if (!$this->nextImage->imageID) $this->nextImage = null;
	}
	
	/**
	* @see Page::assignVariables();
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		$this->frame->assignVariables();
		WCF::getTPL()->assign(array(
			'imageID' => $this->imageID,
			'image' => $this->image,
			'previousImage' => $this->previousImage,
			'nextImage' => $this->nextImage,
			'allowSpidersToIndexThisPage' => true
		));
	}
	
	/**
	 * Updates the views of this entry image.
	 */
	public function updateViews() {
		$sql = "UPDATE	wsif".WSIF_N."_entry_image
			SET	views = views + 1
			WHERE	imageID = ".$this->imageID;
		WCF::getDB()->registerShutdownUpdate($sql);
	}
}
?>