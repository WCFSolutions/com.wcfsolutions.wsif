<?php
/**
 * The core class of this application implements this interface.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	page.util.menu
 * @category	Infinite Filebase
 */
interface EntryMenuContainer {
	/**
	 * Returns the active object of the entry menu.
	 * 
	 * @return	EntryMenu
	 */
	public static function getEntryMenu();
}
?>