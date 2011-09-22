<?php
// wcf imports
require_once(WCF_DIR.'lib/system/cache/CacheBuilder.class.php');

/**
 * Caches last entries and category stats.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	system.cache
 * @category	Infinite Filebase
 */
class CacheBuilderCategoryData implements CacheBuilder {
	/**
	 * @see CacheBuilder::getData()
	 */
	public function getData($cacheResource) {
		$data = array('lastEntries' => array(), 'stats' => array());
		
		// last category entries
		$sql = "SELECT		entry.prefixID, entry.subject, entry.time, entry.userID, entry.username, last_entry.*
			FROM 		wsif".WSIF_N."_category_last_entry last_entry
			LEFT JOIN	wsif".WSIF_N."_entry entry
			ON		(entry.entryID = last_entry.entryID)
			ORDER BY	last_entry.categoryID,
					entry.time DESC";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$data['lastEntries'][$row['categoryID']][$row['languageID']] = $row;
		}
		
		// stats
		$sql = "SELECT	categoryID, clicks, entries, entryImages,
				entryFiles, entryDownloads, time
			FROM 	wsif".WSIF_N."_category";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			// downloads per day
			$row['entryDownloadsPerDay'] = 0;
			if ($row['time']) {
				$days = ceil((TIME_NOW - $row['time']) / 86400);
				if ($days <= 0) $days = 1;
				$row['entryDownloadsPerDay'] = $row['entryDownloads'] / $days;
			}
			unset($row['time']);
			// category stats
			$data['stats'][$row['categoryID']] = $row;
		}
		
		return $data;
	}
}
?>