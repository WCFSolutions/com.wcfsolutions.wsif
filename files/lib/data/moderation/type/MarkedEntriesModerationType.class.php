<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/category/Category.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/moderation/type/AbstractModerationType.class.php');

/**
 * Represents the moderation type for marked entries.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.mysteryforce.wscfb
 * @subpackage	data.moderation.type
 * @category 	Community Filebase
 */
class MarkedEntriesModerationType extends AbstractModerationType {
	/**
	 * @see ModerationType::getName()
	 */
	public function getName() {
		return 'markedEntries';
	}
	
	/**
	 * @see ModerationType::getIcon()
	 */	
	public function getIcon() {
		return 'entry';
	}
	
	/**
	 * Returns the url of this moderation type.
	 * 
	 * @return	string
	 */
	public function getURL() {
		return "index.php?page=ModerationMarkedEntries".SID_ARG_2ND;
	}
	
	/**
	 * @see ModerationType::isImportant()
	 */	
	public function isImportant() {
		return false;
	}

	/**
	 * @see ModerationType::isModerator()
	 */
	public function isModerator() {
		return intval(WCF::getUser()->getPermission('mod.filebase.canDeleteEntry') || WCF::getUser()->getPermission('mod.filebase.canMoveEntry'));
	}
	
	/**
	 * @see ModerationType::getOutstandingModerations()
	 */
	public function getOutstandingModerations() {
		return (($markedEntries = WCF::getSession()->getVar('markedEntries')) ? count($markedEntries) : 0);
	}
}
?>