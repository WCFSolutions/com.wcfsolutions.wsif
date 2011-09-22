<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/entry/ViewableEntry.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/message/util/SearchResultTextParser.class.php');

/**
 * Represents an entry search result output.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	data.entry
 * @category	Infinite Filebase
 */
class EntrySearchResult extends ViewableEntry {
	/**
	 * @see DatabaseObject::handleData()
	 */
	protected function handleData($data) {
		$data['messagePreview'] = true;
		parent::handleData($data);
	}
	
	/**
	 * @see ViewableEntry::getFormattedMessage()
	 */
	public function getFormattedMessage() {
		return SearchResultTextParser::parse(parent::getFormattedMessage());
	}
}
?>