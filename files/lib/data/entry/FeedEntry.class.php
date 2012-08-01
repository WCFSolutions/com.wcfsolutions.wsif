<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/entry/ViewableEntry.class.php');

/**
 * Represents a viewable entry in a rss or an atom feed.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	data.entry
 * @category	Infinite Filebase
 */
class FeedEntry extends ViewableEntry {
	/**
	 * @see ViewableEntry::getFormattedMessage()
	 */
	public function getFormattedMessage() {
		// replace relative urls
		$text = preg_replace('~(?<=href="|src=")(?![a-z0-9]+://)~i', PAGE_URL.'/', parent::getFormattedMessage());

		return StringUtil::escapeCDATA($text);
	}
}
?>