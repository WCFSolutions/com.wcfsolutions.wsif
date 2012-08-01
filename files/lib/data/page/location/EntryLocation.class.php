<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/category/Category.class.php');
require_once(WSIF_DIR.'lib/data/entry/ViewableEntry.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/page/location/Location.class.php');

/**
 * EntryLocation is an implementation of Location for the entry page.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	data.page.location
 * @category	Infinite Filebase
 */
class EntryLocation implements Location {
	public $cachedEntryIDs = array();
	public $entries = null;

	/**
	 * @see Location::cache()
	 */
	public function cache($location, $requestURI, $requestMethod, $match) {
		$this->cachedEntryIDs[] = $match[1];
	}

	/**
	 * @see Location::get()
	 */
	public function get($location, $requestURI, $requestMethod, $match) {
		if ($this->entries === null) {
			$this->readEntries();
		}

		$entryID = $match[1];
		if (!isset($this->entries[$entryID])) {
			return '';
		}

		return WCF::getLanguage()->get($location['locationName'], array('$entry' => ($this->entries[$entryID]->prefixID ? $this->entries[$entryID]->getPrefix()->getStyledPrefix().' ' : '').'<a href="index.php?page=Entry&amp;entryID='.$entryID.SID_ARG_2ND.'">'.StringUtil::encodeHTML($this->entries[$entryID]->subject).'</a>'));
	}

	/**
	 * Gets the entries.
	 */
	protected function readEntries() {
		$this->entries = array();

		if (!count($this->cachedEntryIDs)) {
			return;
		}

		// get accessible categories
		$categoryIDs = Category::getAccessibleCategories();
		if (empty($categoryIDs)) return;

		$sql = "SELECT	entryID, subject
			FROM	wsif".WSIF_N."_entry
			WHERE	entryID IN (".implode(',', $this->cachedEntryIDs).")
				AND isDeleted = 0
				AND isDisabled = 0
				AND categoryID IN (".$categoryIDs.")";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$this->entries[$row['entryID']] = new ViewableEntry(null, $row);
		}
	}
}
?>