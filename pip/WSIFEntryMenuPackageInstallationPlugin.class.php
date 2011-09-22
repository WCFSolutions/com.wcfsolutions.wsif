<?php
// wcf imports
require_once(WCF_DIR.'lib/acp/package/plugin/AbstractXMLPackageInstallationPlugin.class.php');

/**
 * This PIP installs, updates or deletes entry menu items.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.php>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	acp.package.plugin
 * @category 	Community Filebase
 */
class WSIFEntryMenuPackageInstallationPlugin extends AbstractXMLPackageInstallationPlugin {
	public $tagName = 'entrymenu';
	public $tableName = 'entry_menu_item';
	
	/** 
	 * @see PackageInstallationPlugin::install()
	 */
	public function install() {
		parent::install();
		
		if (!$xml = $this->getXML()) {
			return;
		}
		
		// get instance no
		$instanceNo = WCF_N.'_'.$this->getApplicationPackage()->getInstanceNo();
		
		// Create an array with the data blocks (import or delete) from the xml file.
		$entryMenuXML = $xml->getElementTree('data');
		
		// Loop through the array and install or uninstall entry-menu items.
		foreach ($entryMenuXML['children'] as $key => $block) {
			if (count($block['children'])) {
				// Handle the import instructions
				if ($block['name'] == 'import') {
					// Loop through entry-menu items and create or update them.
					foreach ($block['children'] as $entryMenuItem) {
						// Extract item properties.
						foreach ($entryMenuItem['children'] as $child) {
							if (!isset($child['cdata'])) continue;
							$entryMenuItem[$child['name']] = $child['cdata'];
						}
					
						// check required attributes
						if (!isset($entryMenuItem['attrs']['name'])) {
							throw new SystemException("Required 'name' attribute for user entry menu item is missing", 13023);
						}
						
						// default values
						$menuItemLink = $parentMenuItem = $menuItemIcon = $permissions = $options = '';
						$showOrder = null;
						
						// get values
						$menuItem = $entryMenuItem['attrs']['name'];
						if (isset($entryMenuItem['link'])) $menuItemLink = $entryMenuItem['link'];
						if (isset($entryMenuItem['parent'])) $parentMenuItem = $entryMenuItem['parent'];
						if (isset($entryMenuItem['icon'])) $menuItemIcon = $entryMenuItem['icon'];
						if (isset($entryMenuItem['showorder'])) $showOrder = intval($entryMenuItem['showorder']);
						$showOrder = $this->getShowOrder($showOrder, $parentMenuItem, 'parentMenuItem');
						if (isset($entryMenuItem['permissions'])) $permissions = $entryMenuItem['permissions'];
						if (isset($entryMenuItem['options'])) $options = $entryMenuItem['options'];
						
						// If a parent link was set and this parent is not in database 
						// or it is a link from a package from other package environment: don't install further.
						if (!empty($parentMenuItem)) {
							$sql = "SELECT	COUNT(*) AS count
								FROM 	wsif".$instanceNo."_entry_menu_item
								WHERE	menuItem = '".escapeString($parentMenuItem)."'";
							$menuItemCount = WCF::getDB()->getFirstRow($sql);
							if ($menuItemCount['count'] == 0) {
								throw new SystemException("For the menu item '".$menuItem."' no parent item '".$parentMenuItem."' exists.", 13011);
							}
						}
						
						// Insert or update items. 
						// Update through the mysql "ON DUPLICATE KEY"-syntax. 
						$sql = "INSERT INTO			wsif".$instanceNo."_entry_menu_item
											(packageID, menuItem, parentMenuItem, menuItemLink, menuItemIcon, showOrder, permissions, options)
							VALUES				(".$this->installation->getPackageID().",
											'".escapeString($menuItem)."',
											'".escapeString($parentMenuItem)."',
											'".escapeString($menuItemLink)."',
											'".escapeString($menuItemIcon)."',
											".$showOrder.",
											'".escapeString($permissions)."',
											'".escapeString($options)."')
							ON DUPLICATE KEY UPDATE 	parentMenuItem = VALUES(parentMenuItem),
											menuItemLink = VALUES(menuItemLink),
											menuItemIcon = VALUES(menuItemIcon),
											showOrder = VALUES(showOrder),
											permissions = VALUES(permissions),
											options = VALUES(options)";
						WCF::getDB()->sendQuery($sql);
					}
				}
				// Handle the delete instructions.
				else if ($block['name'] == 'delete') {
					if ($this->installation->getAction() == 'update') {
						// Loop through entry-menu items and delete them.
						$itemNames = '';
						foreach ($block['children'] as $entryMenuItem) {
							// check required attributes
							if (!isset($entryMenuItem['attrs']['name'])) {
								throw new SystemException("Required 'name' attribute for 'entrymenuitem'-tag is missing.", 13023);
							}
							// Create a string with all item names which should be deleted (comma seperated).
							if (!empty($itemNames)) $itemNames .= ',';
							$itemNames .= "'".escapeString($entryMenuItem['attrs']['name'])."'";
						}
						// Delete items.
						if (!empty($itemNames)) {
							$sql = "DELETE FROM	wsif".$instanceNo."_entry_menu_item
								WHERE		menuItem IN (".$itemNames.")
										AND packageID = ".$this->installation->getPackageID();
							WCF::getDB()->sendQuery($sql);
						}
					}
				}
			}
		}
	}
	
	/**
	 * @see	 PackageInstallationPlugin::hasUninstall()
	 */
	public function hasUninstall() {
		if (($package = $this->getApplicationPackage()) !== null && $package->getPackage() == 'com.wcfsolutions.wsif') {
			try {				
				$instanceNo = WCF_N.'_'.$package->getInstanceNo();
				$sql = "SELECT	COUNT(*) AS count
					FROM	wsif".$instanceNo."_".$this->tableName."
					WHERE	packageID = ".$this->installation->getPackageID();
				$installationCount = WCF::getDB()->getFirstRow($sql);
				return $installationCount['count'];
			}
			catch (Exception $e) {
				return false;	
			}
		}
		else return false;
	}
	
	/**
	 * @see	 PackageInstallationPlugin::uninstall()
	 */
	public function uninstall() {
		if (($package = $this->getApplicationPackage()) !== null && $package->getPackage() == 'com.wcfsolutions.wsif') {		
			$instanceNo = WCF_N.'_'.$package->getInstanceNo();
			$sql = "DELETE FROM	wsif".$instanceNo."_".$this->tableName."
				WHERE		packageID = ".$this->installation->getPackageID();
			WCF::getDB()->sendQuery($sql);
		}
	}
	
	/**
	 * Returns the show order value.
	 * 
	 * @param	integer		$showOrder
	 * @param	string		$parentName
	 * @param	string		$columnName
	 * @param	string		$tableNameExtension
	 * @return	integer 	new show order
	 */
	protected function getShowOrder($showOrder, $parentName = null, $columnName = null, $tableNameExtension = '') {
		$instanceNo = WCF_N.'_'.$this->getApplicationPackage()->getInstanceNo();

		if ($showOrder === null) {
	        	// get greatest showOrder value
	          	$sql = "SELECT	MAX(showOrder) AS showOrder
				FROM	wsif".$instanceNo."_".$this->tableName.$tableNameExtension." 
				".($columnName !== null ? "WHERE ".$columnName." = '".escapeString($parentName)."'" : "");
			$maxShowOrder = WCF::getDB()->getFirstRow($sql);
			if (is_array($maxShowOrder) && isset($maxShowOrder['showOrder'])) {
				return $maxShowOrder['showOrder'] + 1;
			}
			else {
				return 1;
			}
	    }
	    else {
			// increase all showOrder values which are >= $showOrder
			$sql = "UPDATE	wsif".$instanceNo."_".$this->tableName.$tableNameExtension."
				SET	showOrder = showOrder+1
				WHERE	showOrder >= ".$showOrder." 
				".($columnName !== null ? "AND ".$columnName." = '".escapeString($parentName)."'" : "");
			WCF::getDB()->sendQuery($sql);
			// return the wanted showOrder level
			return $showOrder;     
		}
	}
	
	/**
	 * Returns the application package instance.
	 */
	protected function getApplicationPackage() {
		if ($this->installation->getPackage()->getParentPackage() === null) {
			// installation or update of mfcfb
			return $this->installation->getPackage();
		}
		// installation or update of a plugin
		return $this->installation->getPackage()->getParentPackage();
	}
}
?>