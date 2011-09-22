<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/category/CategoryEditor.class.php');
require_once(WSIF_DIR.'lib/data/entry/EntryEditor.class.php');

// wcf imports
require_once(WCF_DIR.'lib/action/AbstractSecureAction.class.php');

/**
 * Marks / unmarks entries.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	action
 * @category	Infinite Filebase
 */
class EntryMarkAction extends AbstractSecureAction {
	public $entryIDArray = array();
	public $action = '';

	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		if (isset($_REQUEST['entryID'])) {
			$this->entryIDArray = ArrayUtil::toIntegerArray($_REQUEST['entryID']);
			if (!is_array($this->entryIDArray)) {
				$this->entryIDArray = array($this->entryIDArray);
			}
		}
		if (isset($_POST['action'])) $this->action = $_POST['action'];
	}
	
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();
		
		// get categories
		list($categories, $categoryIDs) = EntryEditor::getCategoriesByEntryIDs(implode(',', $this->entryIDArray));
			
		// check permissions
		foreach ($categories as $category) {
			$category->checkModeratorPermission(array('canDeleteEntry', 'canMoveEntry', 'canCopyEntry'));
		}

		// mark / unmark		
		foreach ($this->entryIDArray as $entryID) {
			$entry = new EntryEditor($entryID);
			$entry->enter();
			if ($this->action == 'mark') $entry->mark();
			else if ($this->action == 'unmark') $entry->unmark();
		}
		$this->executed();
	}
}
?>