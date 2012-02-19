<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/category/Category.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/moderation/type/AbstractModerationType.class.php');

/**
 * Represents the moderation type for hidden entries.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	data.moderation.type
 * @category	Infinite Filebase
 */
class HiddenEntriesModerationType extends AbstractModerationType {
	/**
	 * @see ModerationType::getName()
	 */
	public function getName() {
		return 'hiddenEntries';
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
		return "index.php?page=ModerationHiddenEntries".SID_ARG_2ND;
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
		return WCF::getUser()->getPermission('mod.filebase.canEnableEntry');
	}
	
	/**
	 * @see ModerationType::getOutstandingModerations()
	 */
	public function getOutstandingModerations() {
		$categoryIDs = Category::getModeratedCategories(array('canEnableEntry'));
		if (!empty($categoryIDs)) {
			$sql = "SELECT	COUNT(*) AS count
				FROM	wsif".WSIF_N."_entry
				WHERE	isDisabled = 1
					AND categoryID IN (".$categoryIDs.")";
			$row = WCF::getDB()->getFirstRow($sql);
			return $row['count'];
		}
		return 0;
	}
}
?>