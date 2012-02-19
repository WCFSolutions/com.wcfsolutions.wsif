<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/category/CategoryEditor.class.php');
require_once(WSIF_DIR.'lib/data/entry/EntryEditor.class.php');

// wcf imports
require_once(WCF_DIR.'lib/action/AbstractSecureAction.class.php');

/**
 * Recovers all marked entries.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	action
 * @category	Infinite Filebase
 */
class EntryRecoverMarkedAction extends AbstractSecureAction {
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
		
		// delete marked entries
		$markedEntries = WCF::getSession()->getVar('markedEntries');
		if ($markedEntries !== null) {
			$markedEntries = implode(',', $markedEntries);
			list($categories, $categoryIDs) = EntryEditor::getCategoriesByEntryIDs($markedEntries);
			
			// check permissions
			foreach ($categories as $category) {
				$category->checkModeratorPermission('canDeleteEntryCompletely');
			}
			
			// delete / trash entries
			EntryEditor::restoreAll($markedEntries);
			EntryEditor::unmarkAll();
			
			// refresh stats
			CategoryEditor::refreshAll($categoryIDs);
			
			// set last entries
			foreach ($categories as $category) {
				$category->setLastEntries();
			}
			
			// reset cache
			WCF::getCache()->clearResource('categoryData', true);
			WCF::getCache()->clearResource('stat');
		}
		$this->executed();
		
		// forward to page
		HeaderUtil::redirect($this->url);
		exit;
	}
}
?>