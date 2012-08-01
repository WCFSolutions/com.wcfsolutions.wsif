<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/entry/EntryFrame.class.php');
require_once(WSIF_DIR.'lib/data/entry/file/EntryFileList.class.php');

// wcf imports
require_once(WCF_DIR.'lib/page/MultipleLinkPage.class.php');

/**
 * Shows a list of entry files.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	page
 * @category	Infinite Filebase
 */
class EntryFilesPage extends MultipleLinkPage {
	// system
	public $templateName = 'entryFiles';
	public $itemsPerPage = ENTRY_FILES_PER_PAGE;

	/**
	 * list of entry files
	 *
	 * @var EntryFileList
	 */
	public $fileList = null;

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

		// init file list
		$this->fileList = new EntryFileList();
		$this->fileList->sqlConditions = "entry_file.entryID = ".$this->frame->getEntryID();
	}

	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();

		// read objects
		$this->fileList->sqlOffset = ($this->pageNo - 1) * $this->itemsPerPage;
		$this->fileList->sqlLimit = $this->itemsPerPage;
		$this->fileList->sqlOrderBy = 'entry_file.isDefault DESC, entry_file.uploadTime DESC';
		$this->fileList->readObjects();
	}

	/**
	 * @see MultipleLinkPage::countItems()
	 */
	public function countItems() {
		parent::countItems();

		return $this->fileList->countObjects();
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		$this->frame->assignVariables();
		WCF::getTPL()->assign(array(
			'files' => $this->fileList->getObjects(),
			'allowSpidersToIndexThisPage' => true
		));
	}
}
?>