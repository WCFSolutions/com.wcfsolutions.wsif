<?php
// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');

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
class CategoryPermissionsObjectsPage extends AbstractPage {
	/**
	 * query
	 *
	 * @var	array
	 */
	public $query = array();

	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		if (isset($_REQUEST['query'])) {
			$queryString = $_REQUEST['query'];
			if (CHARSET != 'UTF-8') {
				$queryString = StringUtil::convertEncoding('UTF-8', CHARSET, $queryString);
			}
			$this->query = ArrayUtil::trim(explode(',', $queryString));
		}
	}

	/**
	 * @see Page::show()
	 */
	public function show() {
		parent::show();

		header('Content-type: text/xml');
		echo "<?xml version=\"1.0\" encoding=\"".CHARSET."\"?>\n<objects>";

		if (count($this->query)) {
			// get users and groups
			$names = implode("','", array_map('escapeString', $this->query));
			$sql = "(SELECT		username AS name, userID AS id, 'user' AS type
				FROM		wcf".WCF_N."_user
				WHERE		username IN ('".$names."'))
				UNION
				(SELECT		groupName AS name, groupID AS id, 'group' AS type
				FROM		wcf".WCF_N."_group
				WHERE		groupName IN ('".$names."'))
				ORDER BY 	name";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				echo "<object>";
				echo "<name><![CDATA[".StringUtil::escapeCDATA($row['name'])."]]></name>";
				echo "<type>".$row['type']."</type>";
				echo "<id>".$row['id']."</id>";
				echo "</object>";
			}
		}
		echo '</objects>';
		exit;
	}
}
?>