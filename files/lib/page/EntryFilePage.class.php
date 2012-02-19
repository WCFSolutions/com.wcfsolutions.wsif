<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/entry/EntryFrame.class.php');
require_once(WSIF_DIR.'lib/data/entry/file/EntryFile.class.php');
require_once(WSIF_DIR.'lib/data/user/WSIFUser.class.php');

// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');

/**
 * Shows the entry file page.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	page
 * @category	Infinite Filebase
 */
class EntryFilePage extends AbstractPage {
	// system
	public $templateName = 'entryFile';	
	
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
	 * entry frame object
	 * 
	 * @var EntryFrame
	 */
	public $frame = null;

	/**
	 * list of file downloaders
	 *
	 * @var array<WSIFUser>
	 */
	public $fileDownloaders = array();

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
		
		// get entry frame
		$this->frame = new EntryFrame($this, $this->file->entryID);

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
		
		// get file downloaders
		$sql = "SELECT		avatar.*, user_table.*, downloader.*
			FROM		wsif".WSIF_N."_entry_file_downloader downloader
			LEFT JOIN 	wcf".WCF_N."_user user_table
			ON 		(user_table.userID = downloader.userID)
			LEFT JOIN 	wcf".WCF_N."_avatar avatar
			ON 		(avatar.avatarID = user_table.avatarID)
			WHERE		downloader.fileID = ".$this->fileID."
			ORDER BY	time DESC";
		$result = WCF::getDB()->sendQuery($sql, 5);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$this->fileDownloaders[] = new WSIFUser(null, $row);
		}
	}
	
	/**
	* @see Page::assignVariables();
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		$this->frame->assignVariables();
		WCF::getTPL()->assign(array(
			'fileID' => $this->fileID,
			'file' => $this->file,
			'fileDownloaders' => $this->fileDownloaders,
			'allowSpidersToIndexThisPage' => true
		));
	}
	
	/**
	 * Updates the views of this entry file.
	 */
	public function updateViews() {
		$sql = "UPDATE	wsif".WSIF_N."_entry_file
			SET	views = views + 1
			WHERE	fileID = ".$this->fileID;
		WCF::getDB()->registerShutdownUpdate($sql);
	}
}
?>