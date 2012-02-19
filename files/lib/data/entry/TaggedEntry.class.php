<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/entry/ViewableEntry.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/tag/Tagged.class.php');
require_once(WCF_DIR.'lib/data/user/User.class.php');

/**
 * An implementation of Tagged to support the tagging of entries.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	data.entry
 * @category	Infinite Filebase
 */
class TaggedEntry extends ViewableEntry implements Tagged {
	/**
	 * user object
	 * 
	 * @var	User
	 */
	protected $user = null;

	/**
	 * @see ViewableThread::handleData()
	 */
	protected function handleData($data) {
		parent::handleData($data);
		
		// get user
		$this->user = new User(null, array('userID' => $this->userID, 'username' => $this->username));
	}

	/**
	 * @see Tagged::getTitle()
	 */
	public function getTitle() {
		return $this->subject;
	}

	/**
	 * @see Tagged::getObjectID()
	 */
	public function getObjectID() {
		return $this->entryID;
	}

	/**
	 * @see Tagged::getTaggable()
	 */
	public function getTaggable() {
		return $this->taggable;
	}
	
	/**
	 * @see Tagged::getDescription()
	 */
	public function getDescription() {
		require_once(WCF_DIR.'lib/data/message/bbcode/MessageParser.class.php');
		$parser = MessageParser::getInstance();
		$parser->setOutputType('text/html');
		return $parser->parse($this->messagePreview, true, false, true, false);
	}

	/**
	 * @see Tagged::getSmallSymbol()
	 */
	public function getSmallSymbol() {
		return StyleManager::getStyle()->getIconPath('entryS.png');
	}

	/**
	 * @see Tagged::getMediumSymbol()
	 */
	public function getMediumSymbol() {
		return StyleManager::getStyle()->getIconPath('entryM.png');
	}
	
	/**
	 * @see Tagged::getLargeSymbol()
	 */
	public function getLargeSymbol() {
		return StyleManager::getStyle()->getIconPath('entryL.png');
	}

	/**
	 * @see Tagged::getUser()
	 */
	public function getUser() {
		return $this->user;
	}
	
	/**
	 * @see Tagged::getDate()
	 */
	public function getDate() {
		return $this->time;
	}
	
	/**
	 * @see Tagged::getDate()
	 */
	public function getURL() {
		return RELATIVE_WSIF_DIR.'index.php?page=Entry&entryID='.$this->entryID;
	}
}
?>