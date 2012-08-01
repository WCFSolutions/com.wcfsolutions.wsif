<?php
// wcf imports
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');

/**
 * Shows the amout of entries and the last five entries on profile page.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	system.event.listener
 * @category	Infinite Filebase
 */
class UserPageEntriesListener implements EventListener {
	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		if ($eventName == 'init') {
			$eventObj->sqlSelects .= 'wsif_user.entries,';
			$eventObj->sqlJoins .= ' LEFT JOIN wsif'.WSIF_N.'_user wsif_user
						ON (wsif_user.userID = user.userID) ';
		}
		else if ($eventName == 'assignVariables') {
			$user = $eventObj->frame->getUser();

			// show entry amount
			$eventObj->generalInformation[] = array(
				'icon' => StyleManager::getStyle()->getIconPath('entryM.png'),
				'title' => WCF::getLanguage()->get('wcf.user.entries'),
				'value' => '<a href="index.php?form=Search&amp;types[]=entry&amp;userID='.$user->userID.SID_ARG_2ND.'" title="'.WCF::getLanguage()->get('wcf.user.profile.search', array('$username' => StringUtil::encodeHTML($user->username))).'">'.StringUtil::formatInteger(intval($user->entries)) .
					($user->getProfileAge() > 1 ? ' '.WCF::getLanguage()->get('wcf.user.entriesPerDay', array('$entries' => StringUtil::formatDouble($user->entries / $user->getProfileAge()))) : '').'</a>');

			// show last five entries
			if (PROFILE_SHOW_LAST_ENTRIES) {
				require_once(WSIF_DIR.'lib/data/category/Category.class.php');
				require_once(WSIF_DIR.'lib/data/entry/ViewableEntry.class.php');
				$categoryIDArray = Category::getAccessibleCategoryIDArray(array('canViewCategory', 'canEnterCategory', 'canViewEntry'));

				if (count($categoryIDArray)) {
					$entries = array();
					$sql = "SELECT		entryID, prefixID, subject, time
						FROM		wsif".WSIF_N."_entry
						WHERE		userID = ".$user->userID."
								AND isDeleted = 0
								AND isDisabled = 0
								AND categoryID IN (".implode(',', $categoryIDArray).")
								".(count(WCF::getSession()->getVisibleLanguageIDArray()) ? "AND languageID IN (".implode(',', WCF::getSession()->getVisibleLanguageIDArray()).")" : "")."
						ORDER BY	time DESC";
					$result = WCF::getDB()->sendQuery($sql, 5);
					while ($row = WCF::getDB()->fetchArray($result)) {
						$entries[] = new ViewableEntry(null, $row);
					}

					if (count($entries)) {
						WCF::getTPL()->assign(array(
							'entries' => $entries,
							'user' => $user
						));
						WCF::getTPL()->append('additionalContent2', WCF::getTPL()->fetch('userProfileLastEntries'));
					}
				}
			}
		}
	}
}
?>