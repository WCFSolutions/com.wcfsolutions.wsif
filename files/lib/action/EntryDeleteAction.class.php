<?php
// wsif imports
require_once(WSIF_DIR.'lib/action/AbstractEntryAction.class.php');

/**
 * Trashes an entry.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	action
 * @category	Infinite Filebase
 */
class EntryDeleteAction extends AbstractEntryAction {
	/**
	 * redirection url
	 *
	 * @var	string
	 */
	public $url = '';

	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get url
		if (isset($_REQUEST['url'])) $this->url = $_REQUEST['url'];
	}

	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();
		
		// check permission
		$this->category->checkModeratorPermission('canDeleteEntryCompletely');
		
		// delete entry
		$this->entry->unmark();
		$this->entry->delete();
		if (!$this->entry->isDeleted || !ENTRY_ENABLE_RECYCLE_BIN) {
			// refresh last category entry
			$this->category->refresh();
			if ($this->entry->time == $this->category->getLastEntryTime($this->entry->languageID)) {
				$this->category->setLastEntries();
			}
			
			// reset cache
			WCF::getCache()->clearResource('categoryData', true);
			WCF::getCache()->clearResource('stat');
		}
		$this->executed();
		
		// forward to page
		if (strpos($this->url, 'page=Entry') !== false) HeaderUtil::redirect('index.php?page=Category&categoryID='.$this->entry->categoryID.SID_ARG_2ND_NOT_ENCODED);
		else HeaderUtil::redirect($this->url);
	}
}
?>