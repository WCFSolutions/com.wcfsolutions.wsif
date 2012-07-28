<?php
/**
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
$packageID = $this->installation->getPackageID();

// set installation date
$sql = "UPDATE	wcf".WCF_N."_option
	SET	optionValue = ".TIME_NOW."
	WHERE	optionName = 'install_date'
		AND packageID = ".$packageID;
WCF::getDB()->sendQuery($sql);

// set page url and cookie path
if (!empty($_SERVER['SERVER_NAME'])) {
	// domain
	$pageURL = 'http://' . $_SERVER['SERVER_NAME'];

	// port
	if (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != 80) {
		$pageURL .= ':' . $_SERVER['SERVER_PORT'];
	}

	// file
	$path = FileUtil::removeTrailingSlash(FileUtil::getRealPath(FileUtil::addTrailingSlash(dirname(WCF::getSession()->requestURI)).RELATIVE_WCF_DIR.$this->installation->getPackage()->getDir()));
	$pageURL .= $path;

	$sql = "UPDATE	wcf".WCF_N."_option
		SET	optionValue = '".escapeString($pageURL)."'
		WHERE	optionName = 'page_url'
			AND packageID = ".$packageID;
	WCF::getDB()->sendQuery($sql);

	$sql = "UPDATE	wcf".WCF_N."_option
		SET	optionValue = '".escapeString($path)."'
		WHERE	optionName = 'cookie_path'
			AND packageID = ".$packageID;
	WCF::getDB()->sendQuery($sql);
}

// admin options
$sql = "UPDATE 	wcf".WCF_N."_group_option_value
	SET	optionValue = 1
	WHERE	groupID = 4
		AND optionID IN (
			SELECT	optionID
			FROM	wcf".WCF_N."_group_option
			WHERE	packageID IN (
					SELECT	dependency
					FROM	wcf".WCF_N."_package_dependency
					WHERE	packageID = ".$packageID."
				)
		)
		AND optionValue = '0'";
WCF::getDB()->sendQuery($sql);

// mod options
$sql = "UPDATE 	wcf".WCF_N."_group_option_value
	SET	optionValue = 1
	WHERE	groupID IN (5,6)
		AND optionID IN (
			SELECT	optionID
			FROM	wcf".WCF_N."_group_option
			WHERE	optionName LIKE 'mod.filebase.%'
				AND packageID IN (
					SELECT	dependency
					FROM	wcf".WCF_N."_package_dependency
					WHERE	packageID = ".$packageID."
				)
		)
		AND optionValue = '0'";
WCF::getDB()->sendQuery($sql);

// disable flood control
$sql = "UPDATE 	wcf".WCF_N."_group_option_value
	SET	optionValue = 0
	WHERE	groupID IN (4, 5, 6)
		AND optionID IN (
			SELECT	optionID
			FROM	wcf".WCF_N."_group_option
			WHERE	optionName = 'user.message.floodControlTime'
		)
		AND optionValue = '30'";
WCF::getDB()->sendQuery($sql);

// refresh (basic) style file
require_once(WCF_DIR.'lib/data/style/StyleEditor.class.php');
$sql = "SELECT * FROM wcf".WCF_N."_style WHERE isDefault = 1";
$style = new StyleEditor(null, WCF::getDB()->getFirstRow($sql));
$style->writeStyleFile();
?>