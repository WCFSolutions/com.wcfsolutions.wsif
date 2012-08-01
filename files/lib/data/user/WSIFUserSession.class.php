<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/user/AbstractWSIFUserSession.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/moderation/Moderation.class.php');
require_once(WCF_DIR.'lib/data/user/avatar/Gravatar.class.php');
require_once(WCF_DIR.'lib/data/user/avatar/Avatar.class.php');

/**
 * Represents a user session in the filebase.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	data.user
 * @category	Infinite Filebase
 */
class WSIFUserSession extends AbstractWSIFUserSession {
	protected $outstandingModerations = null;
	protected $invitations = null;

	/**
	 * displayable avatar object.
	 *
	 * @var DisplayableAvatar
	 */
	protected $avatar = null;

	/**
	 * @see UserSession::__construct()
	 */
	public function __construct($userID = null, $row = null, $username = null) {
		$this->sqlSelects .= "	wsif_user.*, avatar.*, wsif_user.userID AS wsifUserID,
					(SELECT COUNT(*) FROM wcf".WCF_N."_user_whitelist WHERE whiteUserID = user.userID AND confirmed = 0 AND notified = 0) AS numberOfInvitations,";
		$this->sqlJoins .= " 	LEFT JOIN wsif".WSIF_N."_user wsif_user ON (wsif_user.userID = user.userID)
					LEFT JOIN wcf".WCF_N."_avatar avatar ON (avatar.avatarID = user.avatarID) ";
		parent::__construct($userID, $row, $username);
	}

	/**
	 * @see User::handleData()
	 */
	protected function handleData($data) {
		parent::handleData($data);

		if (MODULE_AVATAR == 1 && !$this->disableAvatar && $this->showAvatar) {
			if (MODULE_GRAVATAR == 1 && $this->gravatar) {
				$this->avatar = new Gravatar($this->gravatar);
			}
			else if ($this->avatarID) {
				$this->avatar = new Avatar(null, $data);
			}
		}
	}

	/**
	 * Updates the user session.
	 */
	public function update() {
		// update global last activity timestamp
		WSIFUserSession::updateLastActivityTime($this->userID);

		if (!$this->wsifUserID) {
			$sql = "INSERT IGNORE INTO	wsif".WSIF_N."_user
							(userID)
				VALUES			(".$this->userID.")";
			WCF::getDB()->registerShutdownUpdate($sql);
		}
	}

	/**
	 * Initialises the user session.
	 */
	public function init() {
		parent::init();

		$this->invitations = $this->outstandingModerations = null;
	}

	/**
	 * @see UserSession::getGroupData()
	 */
	protected function getGroupData() {
		parent::getGroupData();

		// get user permissions
		$userPermissions = array();
		$sql = "SELECT		*
			FROM		wsif".WSIF_N."_category_to_user
			WHERE		userID = ".$this->userID;
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$categoryID = $row['categoryID'];
			unset($row['categoryID'], $row['userID']);
			$userPermissions[$categoryID] = $row;
		}

		if (count($userPermissions)) {
			require_once(WSIF_DIR.'lib/data/category/Category.class.php');
			Category::inheritPermissions(0, $userPermissions);

			foreach ($userPermissions as $categoryID => $row) {
				foreach ($row as $key => $val) {
					if ($val != -1) {
						$this->categoryPermissions[$categoryID][$key] = $val;
					}
				}
			}
		}
	}

	/**
	 * Updates the global last activity timestamp in user database.
	 *
	 * @param	integer		$userID
	 * @param	integer		$timestamp
	 */
	public static function updateLastActivityTime($userID, $timestamp = TIME_NOW) {
		$sql = "UPDATE	wcf".WCF_N."_user
			SET	lastActivityTime = ".$timestamp."
			WHERE	userID = ".$userID;
		WCF::getDB()->registerShutdownUpdate($sql);
	}

	/**
	 * Returns true, if the user is a moderator.
	 *
	 * @return	integer
	 */
	public function isModerator() {
		return Moderation::isModerator();
	}

	/**
	 * Returns the number of outstanding moderations.
	 *
	 * @return	integer
	 */
	public function getOutstandingModerations() {
		if ($this->outstandingModerations === null) {
			$this->outstandingModerations = WCF::getSession()->getVar('outstandingModerations-'.PACKAGE_ID);
			if ($this->outstandingModerations === null) {
				$this->outstandingModerations = Moderation::getOutstandingModerations();
				WCF::getSession()->register('outstandingModerations-'.PACKAGE_ID, $this->outstandingModerations);
			}
		}
		return $this->outstandingModerations;
	}

	/**
	 * Returns the outstanding invitations.
	 */
	public function getInvitations() {
		if ($this->invitations === null) {
			$this->invitations = array();
			$sql = "SELECT		user_table.userID, user_table.username
				FROM		wcf".WCF_N."_user_whitelist whitelist
				LEFT JOIN	wcf".WCF_N."_user user_table
				ON		(user_table.userID = whitelist.userID)
				WHERE		whitelist.whiteUserID = ".$this->userID."
						AND whitelist.confirmed = 0
						AND whitelist.notified = 0
				ORDER BY	whitelist.time";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				$this->invitations[] = new User(null, $row);
			}
		}
		return $this->invitations;
	}

	/**
	 * Returns the avatar of this user.
	 *
	 * @return	DisplayableAvatar
	 */
	public function getAvatar() {
		return $this->avatar;
	}
}
?>