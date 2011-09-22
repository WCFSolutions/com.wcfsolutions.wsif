<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/category/Category.class.php');
require_once(WSIF_DIR.'lib/data/entry/ViewableEntry.class.php');
require_once(WSIF_DIR.'lib/data/entry/file/EntryFile.class.php');

// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');

/**
 * Shows an entry file download.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	page
 * @category	Infinite Filebase
 */
class EntryFileDownloadPage extends AbstractPage {
	/**
	 * file id
	 *
	 * @var	integer
	 */
	public $fileID = 0;
	
	/**
	 * file object
	 * 
	 * @var	EntryFile
	 */
	public $file = null;
	
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
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get file
		if (isset($_REQUEST['fileID'])) $this->fileID = intval($_REQUEST['fileID']);
		$this->file = new EntryFile($this->fileID);
		if (!$this->file->fileID) {
			throw new IllegalLinkException();
		}
		
		// get entry
		$this->entry = new ViewableEntry($this->file->entryID);
		
		// get category
		$this->category = Category::getCategory($this->entry->categoryID);
		$this->entry->enter($this->category);
		
		// check download permission
		if (!$this->category->getPermission('canDownloadEntryFile')) {
			throw new PermissionDeniedException();
		}
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		parent::show();
		
		// update category download count
		$sql = "UPDATE	wsif".WSIF_N."_category
			SET	entryDownloads = entryDownloads + 1
			WHERE	categoryID = ".$this->entry->categoryID;
		WCF::getDB()->registerShutdownUpdate($sql);
		
		// update file download count
		$sql = "UPDATE	wsif".WSIF_N."_entry_file
			SET	downloads = downloads + 1,
				lastDownloadTime = ".TIME_NOW."
			WHERE	fileID = ".$this->fileID;
		WCF::getDB()->registerShutdownUpdate($sql);
		
		// update entry download count
		$sql = "UPDATE	wsif".WSIF_N."_entry
			SET	downloads = downloads + 1
			WHERE	entryID = ".$this->file->entryID;
		WCF::getDB()->registerShutdownUpdate($sql);
		
		// save downloader
		if (WCF::getUser()->userID && !WCF::getUser()->invisible) {
			$sql = "INSERT INTO			wsif".WSIF_N."_entry_file_downloader
								(fileID, userID, time)
				VALUES				(".$this->file->fileID.", ".WCF::getUser()->userID.", ".TIME_NOW.")
				ON DUPLICATE KEY UPDATE		time = VALUES(time)";
			WCF::getDB()->registerShutdownUpdate($sql);
		}
		
		// reset cache
		WCF::getCache()->clearResource('categoryData');
		WCF::getCache()->clearResource('stat');
		
		// forward to external url
		if ($this->file->isExternalLink()) {
			WCF::getTPL()->assign(array(
				'url' => $this->file->externalURL,
				'message' => WCF::getLanguage()->getDynamicVariable('wsif.entry.file.externalURL.redirect'),
				'wait' => 5
			));
			WCF::getTPL()->display('redirect');
			exit;
		}
			
		// send headers
		// mime type
		$mimeType = $this->file->mimeType;
		if ($mimeType == 'image/x-png') $mimeType = 'image/png';
		@header('Content-Type: '.$mimeType);
			
		// filename
		@header('Content-disposition: attachment; filename="'.$this->file->filename.'"');
			
		// filesize
		@header('Content-Length: '.$this->file->filesize);
			
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
			
		// show file
		readfile(WSIF_DIR.'storage/files/'.$this->file->fileID);
		exit;
	}
}
?>