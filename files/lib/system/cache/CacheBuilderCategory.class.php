<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/category/Category.class.php');

// wcf imports
require_once(WCF_DIR.'lib/system/cache/CacheBuilder.class.php');

/**
 * Caches all categories and the category structure.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	system.cache
 * @category	Infinite Filebase
 */
class CacheBuilderCategory implements CacheBuilder {
	/**
	 * @see CacheBuilder::getData()
	 */
	public function getData($cacheResource) {
		$data = array('categories' => array(), 'categoryStructure' => array());
				
		// categories
		$sql = "SELECT	* 
			FROM	wsif".WSIF_N."_category";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$data['categories'][$row['categoryID']] = new Category(null, $row);
		}
			
		// category structure
		$sql = "SELECT		* 
			FROM		wsif".WSIF_N."_category_structure
			ORDER BY 	parentID, position";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$data['categoryStructure'][$row['parentID']][] = $row['categoryID'];		
		}
		
		return $data;
	}
}
?>