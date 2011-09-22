<?php
// wcf imports
require_once(WCF_DIR.'lib/system/cache/CacheBuilder.class.php');

/**
 * Caches the acp stats.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	system.cache
 * @category	Infinite Filebase
 */
class CacheBuilderACPStat implements CacheBuilder {
	/**
	 * @see CacheBuilder::getData()
	 */
	public function getData($cacheResource) {
		$data = array();
		
		// get installation age
		$installationAge = (TIME_NOW - INSTALL_DATE) / 86400;
		if ($installationAge < 1) $installationAge = 1;
		
		// members
		$sql = "SELECT	COUNT(*) AS members
			FROM	wcf".WCF_N."_user";
		$row = WCF::getDB()->getFirstRow($sql);
		$data['members'] = $row['members'];
		
		// categories
		$sql = "SELECT	COUNT(*) AS categories
			FROM	wsif".WSIF_N."_category";
		$row = WCF::getDB()->getFirstRow($sql);
		$data['categories'] = $row['categories'];
		
		// entries
		$sql = "SELECT	COUNT(*) AS entries
			FROM	wsif".WSIF_N."_entry";
		$row = WCF::getDB()->getFirstRow($sql);
		$data['entries'] = $row['entries'];
		$data['entriesPerDay'] = $row['entries'] / $installationAge;
		
		// entry images
		$sql = "SELECT	COUNT(*) AS entryImages
			FROM	wsif".WSIF_N."_entry_image";
		$row = WCF::getDB()->getFirstRow($sql);
		$data['entryImages'] = $row['entryImages'];
		$data['entryImagesPerDay'] = $row['entryImages'] / $installationAge;
		
		// entry files
		$sql = "SELECT	COUNT(*) AS entryFiles
			FROM	wsif".WSIF_N."_entry_file";
		$row = WCF::getDB()->getFirstRow($sql);
		$data['entryFiles'] = $row['entryFiles'];
		$data['entryFilesPerDay'] = $row['entryFiles'] / $installationAge;
		
		// entry downloads
		$sql = "SELECT	IFNULL((SUM(downloads)), 0) AS entryDownloads
			FROM	wsif".WSIF_N."_entry";
		$row = WCF::getDB()->getFirstRow($sql);
		$data['entryDownloads'] = $row['entryDownloads'];
		$data['entryDownloadsPerDay'] = $row['entryDownloads'] / $installationAge;

		// database entries and size
		$data['databaseSize'] = 0;
		$data['databaseEntries'] = 0;
		$sql = "SHOW TABLE STATUS";
		$result = WCF::getDB()->sendQuery($sql);
		while($row = WCF::getDB()->fetchArray($result)) {
			$data['databaseSize'] += $row['Data_length'] + $row['Index_length'];
			$data['databaseEntries'] += $row['Rows'];
		}
		
		return $data;
	}
}
?>