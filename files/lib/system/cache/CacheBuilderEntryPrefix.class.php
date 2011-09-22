<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/entry/prefix/EntryPrefix.class.php');

// wcf imports
require_once(WCF_DIR.'lib/system/cache/CacheBuilder.class.php');

/**
 * Builds the entry prefix cache.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	system.cache
 * @category	Infinite Filebase
 */
class CacheBuilderEntryPrefix implements CacheBuilder {
	/**
	 * @see CacheBuilder::getData()
	 */
	public function getData($cacheResource) {
		$data = array('prefixes' => array(), 'categories' => array());
		
		// get all prefixes
		$sql = "SELECT	*
			FROM	wsif".WSIF_N."_entry_prefix";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {			
			$data['prefixes'][$row['prefixID']] = new EntryPrefix(null, $row);
		}

		// get prefix to categories
		$sql = "SELECT		category.categoryID, entry_prefix.prefixID
			FROM		wsif".WSIF_N."_category category,
					wsif".WSIF_N."_entry_prefix entry_prefix
			WHERE		entry_prefix.prefixType = 0 
					OR entry_prefix.prefixID IN (
						SELECT	prefixID
						FROM	wsif".WSIF_N."_entry_prefix_to_category
						WHERE	categoryID = category.categoryID
					)
			ORDER BY	entry_prefix.showOrder";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$data['categories'][$row['categoryID']][] = $row['prefixID'];
		}
		
		return $data;
	}
}
?>