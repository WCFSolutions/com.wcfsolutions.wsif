<?php
// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');
require_once(WCF_DIR.'lib/acp/package/PackageInstallationQueue.class.php');
require_once(WCF_DIR.'lib/data/feed/FeedReaderSource.class.php');

/**
 * Shows the welcome page in the filebase admin control panel.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	acp.page
 * @category	Infinite Filebase
 */
class IndexPage extends AbstractPage {
	// system
	public $templateName = 'index';

	// data
	public $os = '', $webserver = '', $sqlVersion = '', $sqlType = '', $load = '';
	public $stat = array();
	public $news = array();
	public $minorUpdates = array();
	public $majorUpdates = array();

	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();

		$this->os = PHP_OS;
		if (isset($_SERVER['SERVER_SOFTWARE'])) $this->webserver = $_SERVER['SERVER_SOFTWARE'];
		$this->sqlVersion = WCF::getDB()->getVersion();
		$this->sqlType = WCF::getDB()->getDBType();
		if (WCF::getUser()->getPermission('admin.system.package.canUpdatePackage')) {
			$this->readUpdates();
		}
		$this->readNews();
		$this->readLoad();
		$this->readStat();
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'os' => $this->os,
			'webserver' => $this->webserver,
			'sqlVersion' => $this->sqlVersion,
			'sqlType' => $this->sqlType,
			'load' => $this->load,
			'news' => $this->news,
			'minorUpdates' => $this->minorUpdates,
			'majorUpdates' => $this->majorUpdates,
			'dbName' => WCF::getDB()->getDatabaseName(),
			'cacheSource' => get_class(WCF::getCache()->getCacheSource())
		));
		WCF::getTPL()->assign($this->stat);
	}

	/**
	 * Gets a list of available updates.
	 */
	protected function readUpdates() {
		require_once(WCF_DIR.'lib/acp/package/update/PackageUpdate.class.php');
		$updates = PackageUpdate::getAvailableUpdates();

		foreach ($updates as $update) {
			$versions = array_reverse($update['versions']);

			// find newest minor update
			$i = 0;
			$currentVersionStatus = (preg_match('/(alpha|beta|RC)/i', $update['packageVersion']) ? 'unstable' : 'stable');
			foreach ($versions as $version) {
				$newVersionStatus = (preg_match('/(alpha|beta|RC)/i', $version['packageVersion']) ? 'unstable' : 'stable');
				if ($currentVersionStatus == $newVersionStatus && preg_match('/^(\d\.\d)/', $update['packageVersion'], $match1) && preg_match('/^(\d\.\d)/', $version['packageVersion'], $match2)) {
					if ($match1[1] == $match2[1]) {
						$minorUpdate = $update;
						$minorUpdate['version'] = $version;
						$this->minorUpdates[] = $minorUpdate;
						if ($i != 0) {
							$this->majorUpdates[] = $update;
						}
						continue 2;
					}
				}

				$i++;
			}

			$this->majorUpdates[] = $update;
		}
	}

	/**
	 * Gets a list of available news.
	 */
	protected function readNews() {
		$this->news = FeedReaderSource::getEntries(5);
		foreach ($this->news as $key => $news) {
			$this->news[$key]['description'] = preg_replace('/href="(.*?)"/e', '\'href="'.RELATIVE_WCF_DIR.'acp/dereferrer.php?url=\'.rawurlencode(\'$1\').\'" class="externalURL"\'', $news['description']);
		}
	}

	/**
	 * Gets a list of simple statistics.
	 */
	protected function readStat() {
		WCF::getCache()->addResource('acpStat', WSIF_DIR.'cache/cache.acpStat.php', WSIF_DIR.'lib/system/cache/CacheBuilderACPStat.class.php', 0, 3600 * 12);
		$this->stat = WCF::getCache()->get('acpStat');

		// users online
		$sql = "SELECT	COUNT(*) AS usersOnline
			FROM	wcf".WCF_N."_session
			WHERE	packageID = ".PACKAGE_ID."
				AND lastActivityTime > ".(TIME_NOW - USER_ONLINE_TIMEOUT);
		$row = WCF::getDB()->getFirstRow($sql);
		$this->stat['usersOnline'] = $row['usersOnline'];
	}

	/**
	 * Gets the current server load.
	 */
	protected function readLoad() {
		if ($uptime = @exec("uptime")) {
			if (preg_match("/averages?: ([0-9\.]+,?[\s]+[0-9\.]+,?[\s]+[0-9\.]+)/", $uptime, $match)) {
				$this->load = $match[1];
			}
		}
	}
}
?>