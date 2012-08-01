<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/category/CategoryEditor.class.php');
require_once(WSIF_DIR.'lib/data/entry/EntryEditor.class.php');

// wcf imports
require_once(WCF_DIR.'lib/action/AbstractSecureAction.class.php');

/**
 * Moves all marked entries.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	action
 * @category	Infinite Filebase
 */
class EntryMoveMarkedAction extends AbstractSecureAction {
	/**
	 * category id
	 *
	 * @var integer
	 */
	public $categoryID = 0;

	/**
	 * category object
	 *
	 * @var Category
	 */
	public $category = null;

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

		// get category
		if (isset($_REQUEST['categoryID'])) $this->categoryID = intval($_REQUEST['categoryID']);
		$this->category = new CategoryEditor($this->categoryID);
		$this->category->enter();

		// get url
		if (isset($_REQUEST['url'])) $this->url = $_REQUEST['url'];
	}

	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();

		// check permission
		$this->category->checkModeratorPermission('canMoveEntry');

		// get marked entries
		$markedEntryIDArray = WCF::getSession()->getVar('markedEntries');
		if ($markedEntryIDArray === null) throw new IllegalLinkException();
		$markedEntryIDs = implode(',', $markedEntryIDArray);

		// get categories
		list($categories, $categoryIDs) = EntryEditor::getCategoriesByEntryIDs($markedEntryIDs);

		// check permissions
		foreach ($categories as $category) {
			$category->checkModeratorPermission('canMoveEntry');
		}

		EntryEditor::moveAll($markedEntryIDs, $this->categoryID);
		EntryEditor::unmarkAll();

		// refresh stats
		CategoryEditor::refreshAll($categoryIDs.','.$this->category->categoryID);

		// set last entries
		$this->category->setLastEntries();
		foreach ($categories as $category) {
			$category->setLastEntries();
		}

		// reset cache
		WCF::getCache()->clearResource('stat');
		WCF::getCache()->clearResource('categoryData', true);
		$this->executed();

		// forward to page
		HeaderUtil::redirect($this->url);
		exit;
	}
}
?>