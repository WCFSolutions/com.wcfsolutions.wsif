<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/entry/prefix/EntryPrefixEditor.class.php');

// wcf imports
require_once(WCF_DIR.'lib/action/AbstractAction.class.php');

/**
 * Sorts the structure of entry prefixes.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	acp.action
 * @category	Infinite Filebase
 */
class EntryPrefixSortAction extends AbstractAction {
	/**
	 * positions
	 *
	 * @var	array
	 */
	public $positions = array();

	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		// get positions
		if (isset($_POST['entryPrefixListPositions']) && is_array($_POST['entryPrefixListPositions'])) $this->positions = ArrayUtil::toIntegerArray($_POST['entryPrefixListPositions']);
	}

	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();

		// check permissions
		WCF::getUser()->checkPermission('admin.filebase.canEditEntryPrefix');

		// update postions
		foreach ($this->positions as $prefixID => $showOrder) {
			EntryPrefixEditor::updateShowOrder(intval($prefixID), $showOrder);
		}

		// reset cache
		WCF::getCache()->clearResource('entryPrefix');
		$this->executed();

		// forward to list page
		HeaderUtil::redirect('index.php?page=EntryPrefixList&successfullSorting=1&packageID='.PACKAGE_ID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
}
?>