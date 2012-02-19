<?php
// wcf imports
require_once(WCF_DIR.'lib/data/user/UserProfile.class.php');

/**
 * Represents a user in the filebase.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	data.user
 * @category	Infinite Filebase
 */
class WSIFUser extends UserProfile {
	protected $avatar = null;
	
	/**
	 * @see UserProfile::__construct()
	 */
	public function __construct($userID = null, $row = null, $username = null, $email = null) {
		$this->sqlJoins .= ' LEFT JOIN wsif'.WSIF_N.'_user wsif_user ON (wsif_user.userID = user.userID) ';
		parent::__construct($userID, $row, $username, $email);
	}
	
	/**
	 * Updates the amount of entries of an user.
	 * 
	 * @param	integer		$userID
	 * @param	integer		$entries
	 */
	public static function updateUserEntries($userID, $entries) {
		$sql = "UPDATE	wsif".WSIF_N."_user
			SET	entries = IF(".$entries." > 0 OR entries > ABS(".$entries."), entries + ".$entries.", 0)
			WHERE	userID = ".$userID;
		WCF::getDB()->registerShutdownUpdate($sql);
	}
}
?>