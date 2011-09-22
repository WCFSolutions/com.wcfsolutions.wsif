<?php
// wcf imports
require_once(WCF_DIR.'lib/system/counterUpdate/type/AbstractCounterUpdateType.class.php');

/**
 * Updates the installation date timestamp.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	system.counterUpdate.type
 * @category	Infinite Filebase
 */
class InstallationDateCounterUpdateType extends AbstractCounterUpdateType {
	/**
	 * @see	CounterUpdateType::getDefaultLimit()
	 */
	public function getDefaultLimit() {
		return 1;
	}
	
	/**
	 * @see	CounterUpdateType::update()
	 */
	public function update($offset, $limit) {
		parent::update($offset, $limit);
		
		// set installation date
		$sql = "UPDATE	wcf".WCF_N."_option
			SET	optionValue = IFNULL((
					SELECT	MIN(time)
					FROM	wsif".WSIF_N."_entry
					WHERE	time > 0
				), optionValue)
			WHERE	optionName = 'install_date'
				AND packageID = ".PACKAGE_ID;
		WCF::getDB()->sendQuery($sql);
		
		// delete options file
		@unlink(WSIF_DIR.'options.inc.php');		
		$this->finished = true;
		$this->updated();		
	}
}
?>