<?php
// wcf imports
require_once(WCF_DIR.'lib/system/cache/CacheBuilder.class.php');

/**
 * Builds the entry menu cache.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	system.cache
 * @category	Infinite Filebase
 */
class CacheBuilderEntryMenu implements CacheBuilder {
	/**
	 * @see CacheBuilder::getData()
	 */
	public function getData($cacheResource) {
		$data = array();
		
		$sql = "SELECT		menuItem, parentMenuItem, menuItemLink,
					menuItemIcon, permissions, options, packageDir
			FROM		wsif".WSIF_N."_entry_menu_item menu_item
			LEFT JOIN	wcf".WCF_N."_package package
			ON		(package.packageID = menu_item.packageID)
			ORDER BY	showOrder";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$data[$row['parentMenuItem']][] = $row;
		}
		
		return $data;
	}
}
?>