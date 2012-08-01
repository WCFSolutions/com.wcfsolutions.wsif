<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/category/Category.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/moderation/type/AbstractModerationType.class.php');

/**
 * Represents the moderation type for deleted entries.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	data.moderation.type
 * @category	Infinite Filebase
 */
class DeletedEntriesModerationType extends AbstractModerationType {
	/**
	 * @see ModerationType::getName()
	 */
	public function getName() {
		return 'deletedEntries';
	}

	/**
	 * @see ModerationType::getIcon()
	 */
	public function getIcon() {
		return 'entry';
	}

	/**
	 * @see ModerationType::getURL()
	 */
	public function getURL() {
		return "index.php?page=ModerationDeletedEntries".SID_ARG_2ND;
	}

	/**
	 * @see ModerationType::isImportant()
	 */
	public function isImportant() {
		return true;
	}

	/**
	 * @see ModerationType::isModerator()
	 */
	public function isModerator() {
		return WCF::getUser()->getPermission('mod.filebase.canViewDeletedEntry');
	}

	/**
	 * @see ModerationType::getOutstandingModerations()
	 */
	public function getOutstandingModerations() {
		$categoryIDs = Category::getModeratedCategories(array('canViewDeletedEntry'));
		if (!empty($categoryIDs)) {
			$sql = "SELECT	COUNT(*) AS count
				FROM	wsif".WSIF_N."_entry
				WHERE	isDeleted = 1
					AND categoryID IN (".$categoryIDs.")";
			$row = WCF::getDB()->getFirstRow($sql);
			return $row['count'];
		}
		return 0;
	}
}
?>