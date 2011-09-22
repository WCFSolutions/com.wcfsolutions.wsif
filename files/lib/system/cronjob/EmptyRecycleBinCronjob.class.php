<?php
// wcf imports
require_once(WCF_DIR.'lib/data/cronjobs/Cronjob.class.php');

/**
 * Cronjob empties the recycle bin for entries.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	system.cache
 * @category	Infinite Filebase
 */
class EmptyRecycleBinCronjob implements Cronjob {
	/**
	 * @see Cronjob::execute()
	 */
	public function execute($data) {
		if (ENTRY_ENABLE_RECYCLE_BIN && ENTRY_EMPTY_RECYCLE_BIN_CYCLE > 0) {
			$sql = "SELECT	entryID
				FROM	wsif".WSIF_N."_entry
				WHERE	isDeleted = 1
					AND deleteTime < ".(TIME_NOW - ENTRY_EMPTY_RECYCLE_BIN_CYCLE * 86400);
			$result = WCF::getDB()->sendQuery($sql);
			if (WCF::getDB()->countRows($result) > 0) {
				require_once(WSIF_DIR.'lib/data/entry/EntryEditor.class.php');
				$entryIDs = '';
				while ($row = WCF::getDB()->fetchArray($result)) {
					if (!empty($entryIDs)) $entryIDs .= ',';
					$entryIDs .= $row['entryID'];
				}
				EntryEditor::deleteAllCompletely($entryIDs);
			}
		}
	}
}
?>