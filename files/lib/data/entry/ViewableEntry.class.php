<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/entry/Entry.class.php');
require_once(WSIF_DIR.'lib/data/entry/image/EntryImage.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/message/bbcode/MessageParser.class.php');
require_once(WCF_DIR.'lib/data/message/bbcode/AttachmentBBCode.class.php');

/**
 * Represents a viewable entry in the filebase.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	data.entry
 * @category	Infinite Filebase
 */
class ViewableEntry extends Entry {
	protected $image = null;

	/**
	 * @see DatabaseObject::handleData()
	 */
	protected function handleData($data) {
		parent::handleData($data);
		if ($this->defaultImageID) $this->image = new EntryImage(null, $data);
	}
	
	/**
	 * Returns the message of this entry.
	 *
	 * @return 	string		the message of this entry
	 */
	public function getFormattedMessage() {		
		// parse message
		$parser = MessageParser::getInstance();
		$parser->setOutputType('text/html');
		AttachmentBBCode::setMessageID($this->entryID);
		return $parser->parse($this->message, $this->enableSmilies, $this->enableHtml, $this->enableBBCodes);
	}
	
	/**
	 * Returns the entry rating result for template output.
	 *
	 * @return	string
	 */
	public function getRatingOutput() {
		$rating = $this->getRating();
		if ($rating !== false) $roundedRating = round($rating, 0);
		else $roundedRating = 0;
		$description = '';
		if ($this->ratings > 0) {
			$description = WCF::getLanguage()->getDynamicVariable('wsif.entry.vote.description', array('votes' => StringUtil::formatNumeric($this->ratings), 'vote' => StringUtil::formatNumeric($rating)));
		}
		return '<img src="'.StyleManager::getStyle()->getIconPath('rating'.$roundedRating.'.png').'" alt="" title="'.$description.'" />';
	}
	
	/**
	 * Returns the flag icon for the thread language.
	 * 
	 * @return	string
	 */
	public function getLanguageIcon() {
		$languageData = Language::getLanguage($this->languageID);
		if ($languageData !== null) {
			return '<img src="'.StyleManager::getStyle()->getIconPath('language'.ucfirst($languageData['languageCode']).'S.png').'" alt="" title="'.WCF::getLanguage()->get('wcf.global.language.'.$languageData['languageCode']).'" />';
		}
		return '';
	}

	/**
	 * Returns the filename of the entry icon.
	 *
	 * @return	string
	 */
	public function getIconName() {
		return 'entry';
	}
	
	/** 
	 * Returns the default image of this entry.
	 *
	 * @return	EntryImage
	 */
	public function getImage() {
		return $this->image;
	}
}
?>