<?php
// wcf imports
require_once(WCF_DIR.'lib/acp/page/UserSuggestPage.class.php');

/**
 * Outputs an XML document with a list of permissions objects (user or user groups).
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	acp.page
 * @category	Infinite Filebase
 */
class CategoryPermissionsObjectsSuggestPage extends UserSuggestPage {
	/**
	 * @see Page::show()
	 */
	public function show() {
		AbstractPage::show();
				
		header('Content-type: text/xml');
		echo "<?xml version=\"1.0\" encoding=\"".CHARSET."\"?>\n<suggestions>\n";
		
		if (!empty($this->query)) {
			// get suggestions
			$sql = "(SELECT		username AS name, 'user' AS type
				FROM		wcf".WCF_N."_user
				WHERE		username LIKE '".escapeString($this->query)."%')
				UNION ALL
				(SELECT		groupName AS name, 'group' AS type
				FROM		wcf".WCF_N."_group
				WHERE		groupName LIKE '".escapeString($this->query)."%')
				ORDER BY	name";
			$result = WCF::getDB()->sendQuery($sql, 10);
			while ($row = WCF::getDB()->fetchArray($result)) {
				echo "<".$row['type']."><![CDATA[".StringUtil::escapeCDATA($row['name'])."]]></".$row['type'].">\n";
			}
		}
		echo '</suggestions>';
		exit;
	}
}
?>