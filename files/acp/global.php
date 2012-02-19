<?php
/**
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
// define paths
define('RELATIVE_WSIF_DIR', '../');

// include config
$packageDirs = array();
require_once(dirname(dirname(__FILE__)).'/config.inc.php');

// include wcf
require_once(RELATIVE_WCF_DIR.'global.php');
if (!count($packageDirs)) $packageDirs[] = WSIF_DIR;
$packageDirs[] = WCF_DIR;

// starting wsif acp
require_once(WSIF_DIR.'lib/system/WSIFACP.class.php');
new WSIFACP();
?>