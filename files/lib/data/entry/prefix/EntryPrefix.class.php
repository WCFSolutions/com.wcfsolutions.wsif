<?php
// wcf imports
require_once(WCF_DIR.'lib/data/DatabaseObject.class.php');

/**
 * Represents an entry prefix.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	data.entry.prefix
 * @category	Infinite Filebase
 */
class EntryPrefix extends DatabaseObject {
	protected static $prefixes = null;

	/**
	 * Creates a new ItemPrefix object.
	 *
	 * @param	integer		$prefixID
	 * @param 	array<mixed>	$row
	 */
	public function __construct($prefixID, $row = null, $cacheObject = null) {
		if ($prefixID !== null) $cacheObject = self::getPrefix($prefixID);
		if ($row != null) parent::__construct($row);
		if ($cacheObject != null) parent::__construct($cacheObject->data);
	}
	
	/**
	 * Returns the prefix name.
	 *
	 * @return	string
	 */
	public function getPrefixName() {
		return WCF::getLanguage()->getDynamicVariable('wsif.entry.prefix.'.$this->prefix);
	}
	
	/**
	 * Returns the styled prefix.
	 *
	 * @return	string
	 */
	public function getStyledPrefix() {
		if ($this->prefixMarking != '%s') {
			return sprintf($this->prefixMarking, StringUtil::encodeHTML($this->getPrefixName()));
		}
		return StringUtil::encodeHTML($this->getPrefixName());
	}
	
	/**
	 * Returns the prefix with the given prefix id from cache.
	 * 
	 * @param 	integer		$prefixID
	 * @return	EntryPrefix
	 */
	public static function getPrefix($prefixID) {
		if (self::$prefixes === null) {
			self::$prefixes = WCF::getCache()->get('entryPrefix', 'prefixes');
		}

		if (!isset(self::$prefixes[$prefixID])) {
			throw new IllegalLinkException();
		}
		
		return self::$prefixes[$prefixID];
	}
}
?>