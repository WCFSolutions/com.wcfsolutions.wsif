<?php
// wsif imports
require_once(WSIF_DIR.'lib/system/session/WSIFSession.class.php');
require_once(WSIF_DIR.'lib/data/user/WSIFUserSession.class.php');
require_once(WSIF_DIR.'lib/data/user/WSIFGuestSession.class.php');

// wcf imports
require_once(WCF_DIR.'lib/system/session/CookieSessionFactory.class.php');

/**
 * WSIFSessionFactory extends the CookieSessionFactory class with filebase specific functions.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	system.session
 * @category	Infinite Filebase
 */
class WSIFSessionFactory extends CookieSessionFactory {
	protected $guestClassName = 'WSIFGuestSession';
	protected $userClassName = 'WSIFUserSession';
	protected $sessionClassName = 'WSIFSession';
}
?>