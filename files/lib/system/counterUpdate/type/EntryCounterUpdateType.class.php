<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/entry/EntryEditor.class.php');

// wcf imports
require_once(WCF_DIR.'lib/system/counterUpdate/type/AbstractCounterUpdateType.class.php');

/**
 * Updates the entry counters.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	system.counterUpdate.type
 * @category	Infinite Filebase
 */
class EntryCounterUpdateType extends AbstractCounterUpdateType {
	/**
	 * @see	CounterUpdateType::getDefaultLimit()
	 */
	public function getDefaultLimit() {
		return 500;
	}
	
	/**
	 * @see	CounterUpdateType::countItems()
	 */
	public function countItems() {
		$sql = "SELECT	COUNT(*) AS count
			FROM	wsif".WSIF_N."_entry";
		$row = WCF::getDB()->getFirstRow($sql);
		return $row['count'];
	}
	
	/**
	 * @see	CounterUpdateType::update()
	 */
	public function update($offset, $limit) {
		parent::update($offset, $limit);
		
		// get entry ids
		$entryIDs = '';
		$sql = "SELECT		entryID
			FROM		wsif".WSIF_N."_entry
			ORDER BY	entryID";
		$result = WCF::getDB()->sendQuery($sql, $limit, $offset);
		while ($row = WCF::getDB()->fetchArray($result)) {
			if (!empty($entryIDs)) $entryIDs .= ',';
			$entryIDs .= $row['entryID'];
		}
		if (empty($entryIDs)) {
			$this->finished = true;
			return;
		}
		
		// refresh entries
		EntryEditor::refreshAll($entryIDs);
		$this->updated();
	}
}
?>