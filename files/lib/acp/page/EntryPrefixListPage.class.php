<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/entry/prefix/EntryPrefixList.class.php');

// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');

/**
 * Shows a list of all entry prefixes.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	acp.page
 * @category	Infinite Filebase
 */
class EntryPrefixListPage extends AbstractPage {
	// system
	public $templateName = 'entryPrefixList';
	public $neededPermissions = array('admin.filebase.canEditEntryPrefix', 'admin.filebase.canDeleteEntryPrefix');
	public $deletedPrefixID = 0;

	// data
	public $successfullSorting = false;
	public $prefixList = null;

	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		if (isset($_REQUEST['deletedPrefixID'])) $this->deletedPrefixID = intval($_REQUEST['deletedPrefixID']);
		if (isset($_REQUEST['successfullSorting'])) $this->successfullSorting = true;
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function readData() {
		parent::readData();

		// get prefix list
		$this->prefixList = new EntryPrefixList;
		$this->prefixList->sqlLimit = 0;
		$this->prefixList->sqlOrderBy = 'showOrder';
		$this->prefixList->readObjects();
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'prefixes' => $this->prefixList->getObjects(),
			'deletedPrefixID' => $this->deletedPrefixID,
			'successfullSorting' => $this->successfullSorting
		));
	}

	/**
	 * @see Page::show()
	 */
	public function show() {
		// enable menu entry
		WCFACP::getMenu()->setActiveMenuItem('wsif.acp.menu.link.content.entry.prefix.view');

		parent::show();
	}
}
?>