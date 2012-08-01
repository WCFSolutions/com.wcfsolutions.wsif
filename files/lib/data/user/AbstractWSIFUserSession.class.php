<?php
// wcf imports
require_once(WCF_DIR.'lib/system/session/UserSession.class.php');

/**
 * Abstract class for wsif user and guest sessions.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	data.user
 * @category	Infinite Filebase
 */
abstract class AbstractWSIFUserSession extends UserSession {
	protected $categoryPermissions = array();
	protected $categoryModeratorPermissions = array();

	/**
	 * Checks whether this user has the permission with the given name on the category with the given category id.
	 *
	 * @param	string		$permission
	 * @param	integer		$categoryID
	 * @return	mixed
	 */
	public function getCategoryPermission($permission, $categoryID) {
		if (isset($this->categoryPermissions[$categoryID][$permission])) {
			return $this->categoryPermissions[$categoryID][$permission];
		}
		return $this->getPermission('user.filebase.'.$permission);
	}

	/**
	 * Checks whether this user has the moderator permission with the given name on the category with the given category id.
	 *
	 * @param	string		$permission
	 * @param	integer		$categoryID
	 * @return	mixed
	 */
	public function getCategoryModeratorPermission($permission, $categoryID) {
		if (isset($this->categoryModeratorPermissions[$categoryID][$permission])) {
			return $this->categoryModeratorPermissions[$categoryID][$permission];
		}
		return $this->getPermission('mod.filebase.'.$permission);
	}

	/**
	 * @see UserSession::getGroupData()
	 */
	protected function getGroupData() {
		parent::getGroupData();

		// get group permissions from cache
		$groups = implode(',', $this->groupIDs);
		$groupsFilename = StringUtil::getHash(implode('-', $this->groupIDs));

		// register cache resource
		WCF::getCache()->addResource('categoryPermissions-'.$groups, WSIF_DIR.'cache/cache.categoryPermissions-'.$groupsFilename.'.php', WSIF_DIR.'lib/system/cache/CacheBuilderCategoryPermissions.class.php');

		// get group data from cache
		$this->categoryPermissions = WCF::getCache()->get('categoryPermissions-'.$groups);
		if (isset($this->categoryPermissions['groupIDs']) && $this->categoryPermissions['groupIDs'] != $groups) {
			$this->categoryPermissions = array();
		}

		// get category moderator permissions
		$sql = "SELECT		*
			FROM		wsif".WSIF_N."_category_moderator
			WHERE		groupID IN (".implode(',', $this->groupIDs).")
					".($this->userID ? " OR userID = ".$this->userID : '')."
			ORDER BY 	userID DESC";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$categoryID = $row['categoryID'];
			unset($row['categoryID'], $row['userID'], $row['groupID']);

			if (!isset($this->categoryModeratorPermissions[$categoryID])) {
				$this->categoryModeratorPermissions[$categoryID] = array();
			}

			foreach ($row as $permission => $value) {
				if ($value == -1) continue;

				if (!isset($this->categoryModeratorPermissions[$categoryID][$permission])) $this->categoryModeratorPermissions[$categoryID][$permission] = $value;
				else $this->categoryModeratorPermissions[$categoryID][$permission] = $value || $this->categoryModeratorPermissions[$categoryID][$permission];
			}
		}

		// inherit category permissions
		if (count($this->categoryModeratorPermissions)) {
			require_once(WSIF_DIR.'lib/data/category/Category.class.php');
			Category::inheritPermissions(0, $this->categoryModeratorPermissions);
		}
	}
}
?>