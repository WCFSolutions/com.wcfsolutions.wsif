<?php
// wsif imports
require_once(WSIF_DIR.'lib/action/AbstractEntryAction.class.php');

/**
 * Trashes an entry.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	action
 * @category	Infinite Filebase
 */
class EntryTrashAction extends AbstractEntryAction {
	/**
	 * trash reason
	 *
	 * @var	string
	 */
	public $reason = '';

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

		// get reason
		if (isset($_REQUEST['reason'])) {
			$this->reason = StringUtil::trim($_REQUEST['reason']);
			if (CHARSET != 'UTF-8') $this->reason = StringUtil::convertEncoding('UTF-8', CHARSET, $this->reason);
		}

		// get url
		if (isset($_REQUEST['url'])) $this->url = $_REQUEST['url'];
	}

	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();

		if (!ENTRY_ENABLE_RECYCLE_BIN) {
			throw new IllegalLinkException();
		}

		// check permission
		$this->category->checkModeratorPermission('canDeleteEntry');

		// trash entry
		if ($this->entry != null && !$this->entry->isDeleted) {
			$this->entry->trash($this->reason);

			// refresh last category entry
			$this->category->refresh();
			if ($this->entry->entryID == $this->category->getLastEntryID($this->entry->languageID)) {
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