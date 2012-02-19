<?php
// wcf imports
require_once(WCF_DIR.'lib/system/cache/CacheBuilder.class.php');
require_once(WCF_DIR.'lib/data/user/User.class.php');

/**
 * Caches filebase stats.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	system.cache
 * @category	Infinite Filebase
 */
class CacheBuilderStat implements CacheBuilder {
	/**
	 * @see CacheBuilder::getData()
	 */
	public function getData($cacheResource) {
		$data = array();
		
		// amount of members
		$sql = "SELECT	COUNT(*) AS amount
			FROM	wcf".WCF_N."_user";
		$result = WCF::getDB()->getFirstRow($sql);
		$data['members'] = $result['amount'];
		
		// amount of categories
		$sql = "SELECT	COUNT(*) AS amount
			FROM	wsif".WSIF_N."_category";
		$result = WCF::getDB()->getFirstRow($sql);
		$data['categories'] = $result['amount'];
		
		// amount of entries
		$sql = "SELECT	COUNT(*) AS amount
			FROM	wsif".WSIF_N."_entry";
		$result = WCF::getDB()->getFirstRow($sql);
		$data['entries'] = $result['amount'];
		
		// amount of entry images
		$sql = "SELECT	COUNT(*) AS amount
			FROM	wsif".WSIF_N."_entry_image";
		$result = WCF::getDB()->getFirstRow($sql);
		$data['entryImages'] = $result['amount'];
		
		// amount of entry files
		$sql = "SELECT	COUNT(*) AS amount
			FROM	wsif".WSIF_N."_entry_file";
		$result = WCF::getDB()->getFirstRow($sql);
		$data['entryFiles'] = $result['amount'];
		
		// amount of entry downloads
		$sql = "SELECT	IFNULL(SUM(downloads), 0) AS amount
			FROM 	wsif".WSIF_N."_entry";
		$result = WCF::getDB()->getFirstRow($sql);
		$data['entryDownloads'] = $result['amount'];
		
		// entry downloads per day
		$days = ceil((TIME_NOW - INSTALL_DATE) / 86400);
		if ($days <= 0) $days = 1;
		$data['entryDownloadsPerDay'] = $data['entryDownloads'] / $days;
		
		// newest member
		$sql = "SELECT 		*
			FROM 		wcf".WCF_N."_user
			ORDER BY 	userID DESC";
		$result = WCF::getDB()->getFirstRow($sql);
		$data['newestMember'] = new User(null, $result);
		
		return $data;
	}
}
?>