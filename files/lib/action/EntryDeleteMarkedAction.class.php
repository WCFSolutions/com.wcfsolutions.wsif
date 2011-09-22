<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/category/CategoryEditor.class.php');
require_once(WSIF_DIR.'lib/data/entry/EntryEditor.class.php');

// wcf imports
require_once(WCF_DIR.'lib/action/AbstractSecureAction.class.php');

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
class EntryDeleteMarkedAction extends AbstractSecureAction {
	/**
	 * delete reason
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
		
		// delete marked entries
		$markedEntries = implode(',', WCF::getSession()->getVar('markedEntries'));
		if (!empty($markedEntries)) {
			list($categories, $categoryIDs) = EntryEditor::getCategoriesByEntryIDs($markedEntries);
			
			// check permissions
			$sql = "SELECT 	entryID, isDeleted, categoryID
				FROM 	wsif".WSIF_N."_entry
				WHERE 	entryID IN (".$markedEntries.")";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				if ($row['isDeleted'] || !ENTRY_ENABLE_RECYCLE_BIN) {
					$categories[$row['categoryID']]->checkModeratorPermission('canDeleteEntryCompletely');
				}
				else {
					$categories[$row['categoryID']]->checkModeratorPermission('canDeleteEntry');
				}
			}
			
			// delete / trash entries
			EntryEditor::deleteAll($markedEntries, $this->reason);
			EntryEditor::unmarkAll();
			
			// refresh stats
			CategoryEditor::refreshAll($categoryIDs);
			
			// set last entry
			foreach ($categories as $category) {
				$category->setLastEntries();
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