<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/user/AbstractWSIFUserSession.class.php');

/**
 * Represents a guest session in the filebase.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	data.user
 * @category	Infinite Filebase
 */
class WSIFGuestSession extends AbstractWSIFUserSession {}
?>