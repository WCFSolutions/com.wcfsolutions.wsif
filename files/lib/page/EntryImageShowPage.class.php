<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/category/Category.class.php');
require_once(WSIF_DIR.'lib/data/entry/ViewableEntry.class.php');
require_once(WSIF_DIR.'lib/data/entry/image/EntryImage.class.php');

// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');

/**
 * Shows an entry image.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	page
 * @category	Infinite Filebase
 */
class EntryImageShowPage extends AbstractPage {
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
	 * entry object
	 *
	 * @var ViewableEntry
	 */
	public $entry = null;
	
	/**
	 * category object
	 *
	 * @var Category
	 */
	public $category = null;
	
	public $thumbnail = 0;
	
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
		
		// get entry
		$this->entry = new ViewableEntry($this->image->entryID);
		
		// get category
		$this->category = Category::getCategory($this->entry->categoryID);
		$this->entry->enter($this->category);
		
		// get thumbnail
		if (isset($_REQUEST['thumbnail'])) $this->thumbnail = intval($_REQUEST['thumbnail']);
		
		// check thumbnail status
		if ($this->thumbnail && !$this->image->hasThumbnail) {
			throw new IllegalLinkException();
		}
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		parent::show();
		
		// reset URI in session
		//if ($this->thumbnail && WCF::getSession()->lastRequestURI) {
		if (WCF::getSession()->lastRequestURI) {
			WCF::getSession()->setRequestURI(WCF::getSession()->lastRequestURI);
		}
			
		// send headers
		// mime type
		$mimeType = $this->image->mimeType;
		if ($mimeType == 'image/x-png') $mimeType = 'image/png';
		@header('Content-Type: '.$mimeType);
			
		// filename
		@header('Content-disposition: inline; filename="'.$this->image->filename.'"');
			
		// filesize
		@header('Content-Length: '.($this->thumbnail ? $this->image->thumbnailFilesize : $this->image->filesize));
			
		// no cache headers
		if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) {
			// internet explorer doesn't cache files downloaded from a https website, if 'Pragma: no-cache' was sent 
			// @see http://support.microsoft.com/kb/316431/en
			@header('Pragma: public');
		}
		else {
			@header('Pragma: no-cache');
		}
		@header('Expires: 0');
			
		// show image
		readfile(WSIF_DIR.'storage/images/'.($this->thumbnail ? 'thumbnails/' : '').$this->image->imageID);
		exit;
	}
}
?>