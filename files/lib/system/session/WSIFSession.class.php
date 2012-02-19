<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/user/WSIFUserSession.class.php');
require_once(WSIF_DIR.'lib/data/user/WSIFGuestSession.class.php');

// wcf imports
require_once(WCF_DIR.'lib/system/session/CookieSession.class.php');
require_once(WCF_DIR.'lib/data/user/User.class.php');

/**
 * WSIFSession extends the CookieSession class with filebase specific functions.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	system.session
 * @category	Infinite Filebase
 */
class WSIFSession extends CookieSession {
	protected $userSessionClassName = 'WSIFUserSession';
	protected $guestSessionClassName = 'WSIFGuestSession';
	protected $categoryID = 0;
	protected $entryID = 0;
	protected $styleID = 0;
	
	/**
	 * Initialises the session.
	 */
	public function init() {
		parent::init();
		
		// handle style id
		if ($this->user->userID) $this->styleID = $this->user->styleID;
		if (($styleID = $this->getVar('styleID')) !== null) $this->styleID = $styleID;
	}
	
	/**
	 * @see CookieSession::update()
	 */
	public function update() {
		$this->updateSQL .= ", filebaseCategoryID = ".$this->categoryID.", filebaseEntryID = ".$this->entryID;
		 
		parent::update();
	}
	
	/**
	 * Sets the current category id for this session.
	 *
	 * @param	integer		$categoryID
	 */
	public function setCategoryID($categoryID) {
		$this->categoryID = $categoryID;
	}
	
	/**
	 * Sets the current entry id for this session.
	 *
	 * @param	integer		$entryID
	 */
	public function setEntryID($entryID) {
		$this->entryID = $entryID;
	}
	
	/**
	 * Sets the active style id.
	 * 
	 * @param 	integer		$newStyleID
	 */
	public function setStyleID($newStyleID) {
		$this->styleID = $newStyleID;
		if ($newStyleID > 0) $this->register('styleID', $newStyleID);
		else $this->unregister('styleID');
	}
	
	/**
	 * Returns the active style id.
	 * 
	 * @return	integer
	 */
	public function getStyleID() {
		return $this->styleID;
	}
}
?>