<?php
// wcf imports
require_once(WCF_DIR.'lib/page/util/menu/TreeMenu.class.php');

/**
 * Builds the entry menu.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	page.util.menu
 * @category	Infinite Filebase
 */
class EntryMenu extends TreeMenu {
	protected static $instance = null;
	public $entryID = 0;
	
	/**
	 * Returns an instance of the EntryMenu class.
	 * 
	 * @return	EntryMenu
	 */
	public static function getInstance() {
		if (self::$instance == null) {
			self::$instance = new EntryMenu();
		}
		return self::$instance;
	}
	
	/**
	 * @see TreeMenu::loadCache()
	 */
	protected function loadCache() {
		parent::loadCache();
		
		WCF::getCache()->addResource('entryMenu', WSIF_DIR.'cache/cache.entryMenu.php', WSIF_DIR.'lib/system/cache/CacheBuilderEntryMenu.class.php');
		$this->menuItems = WCF::getCache()->get('entryMenu');
	}
	
	/**
	 * @see TreeMenu::parseMenuItemLink()
	 */
	protected function parseMenuItemLink($link, $path) {
		if (preg_match('~\.php$~', $link)) {
			$link .= SID_ARG_1ST; 
		}
		else {
			$link .= SID_ARG_2ND_NOT_ENCODED;
		}

		// insert entry id
		$link = StringUtil::replace('%s', $this->entryID, $link);
		
		return $link;
	}
	
	/**
	 * @see TreeMenu::parseMenuItemIcon()
	 */
	protected function parseMenuItemIcon($icon, $path) {
		return StyleManager::getStyle()->getIconPath($icon);
	}
}
?>