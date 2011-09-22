<?php
// wcf imports
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');

/**
 * Shows the amout of entries in members list page.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	system.event.listener
 * @category	Infinite Filebase
 */
class MembersListPageEntriesListener implements EventListener {
	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		// OptionTypeMemberslistcolumns
		if ($eventName == 'construct') {
			$eventObj->staticColumns['entries'] = 'wcf.user.entries';
		}
		// MembersListPage
		else if ($eventName == 'readParameters') {
			$eventObj->specialSortFields[] = 'entries';
		}
		else if ($eventName == 'readData') {
			if ($eventObj->sortField == 'entries') {
				$eventObj->userTable = 'wsif'.WSIF_N.'_user';
			}
			else {
				$eventObj->sqlSelects .= 'wsif_user.entries,';
				$eventObj->sqlJoins .= '	LEFT JOIN wsif'.WSIF_N.'_user wsif_user
								ON (wsif_user.userID = user.userID) ';
			}
		}
		else if ($eventName == 'assignVariables') {
			if (in_array('entries', $eventObj->activeFields)) {
				foreach ($eventObj->members as $key => $memberData) {
					$user = $memberData['user'];
					$username = $memberData['encodedUsername'];
					$eventObj->members[$key]['entries'] = '<a href="index.php?form=Search&amp;types[]=entry&amp;userID='.$user->userID.SID_ARG_2ND.'" title="'.WCF::getLanguage()->get('wcf.user.profile.search', array('$username' => $username)).'">'.StringUtil::formatInteger(intval($user->entries)).'</a>';
				}
			}
		}
	}
}
?>