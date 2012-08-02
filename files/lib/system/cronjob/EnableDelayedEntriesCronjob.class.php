<?php
// wcf imports
require_once(WCF_DIR.'lib/data/cronjobs/Cronjob.class.php');

/**
 * Cronjob enables delayed entries.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	system.cache
 * @category	Infinite Filebase
 */
class EnableDelayedEntriesCronjob implements Cronjob {
	/**
	 * @see Cronjob::execute()
	 */
	public function execute($data) {
		$entryIDs = '';
		$sql = "SELECT	entryID, userID, publishingTime
			FROM	wsif".WSIF_N."_entry
			WHERE	publishingTime <> 0
				AND publishingTime <= ".TIME_NOW;
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			if (!empty($entryIDs)) $entryIDs .= ',';
			$entryIDs .= $row['entryID'];
		}
		if (!empty($entryIDs)) {
			require_once(WSIF_DIR.'lib/data/category/CategoryEditor.class.php');
			require_once(WSIF_DIR.'lib/data/entry/EntryEditor.class.php');

			// enable entries
			EntryEditor::enableAll($entryIDs);

			// update entries
			$sql = "UPDATE 	wsip".WSIP_N."_entry
				SET	time = publishingTime
				WHERE 	entryID IN (".$entryIDs.")";
			WCF::getDB()->sendQuery($sql);

			// get categories
			list($categories, $categoryIDs) = EntryEditor::getCategoriesByEntryIDs($entryIDs);

			// refresh categories
			CategoryEditor::refreshAll($categoryIDs);

			// set last entries
			foreach ($categories as $category) {
				$category->setLastEntries();
			}

			// reset cache
			WCF::getCache()->clearResource('categoryData', true);
			WCF::getCache()->clearResource('stat');
		}
	}
}
?>