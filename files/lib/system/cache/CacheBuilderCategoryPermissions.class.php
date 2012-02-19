<?php
// wcf imports
require_once(WCF_DIR.'lib/system/cache/CacheBuilder.class.php');

/**
 * Caches the category group permissions.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	system.cache
 * @category	Infinite Filebase
 */
class CacheBuilderCategoryPermissions implements CacheBuilder {
	/**
	 * @see CacheBuilder::getData()
	 */
	public function getData($cacheResource) {
		list($cache, $groupIDs) = explode('-', $cacheResource['cache']);
		$data = array();
		
		$sql = "SELECT		*
			FROM		wsif".WSIF_N."_category_to_group
			WHERE		groupID IN (".$groupIDs.")";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$categoryID = $row['categoryID'];
			unset($row['categoryID'], $row['groupID']);
			
			foreach ($row as $permission => $value) {
				if ($value == -1) continue;
				
				if (!isset($data[$categoryID][$permission])) $data[$categoryID][$permission] = $value;
				else $data[$categoryID][$permission] = $value || $data[$categoryID][$permission];
			}
		}
		
		// inherit category group permissions
		if (count($data)) {
			require_once(WSIF_DIR.'lib/data/category/Category.class.php');
			Category::inheritPermissions(0, $data);
		}
		
		$data['groupIDs'] = $groupIDs;
		return $data;
	}
}
?>