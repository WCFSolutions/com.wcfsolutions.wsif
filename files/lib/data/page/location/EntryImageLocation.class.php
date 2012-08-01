<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/category/Category.class.php');
require_once(WSIF_DIR.'lib/data/entry/ViewableEntry.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/page/location/Location.class.php');

/**
 * EntryImageLocation is an implementation of Location for the entry image page.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	data.page.location
 * @category	Infinite Filebase
 */
class EntryImageLocation implements Location {
	public $cachedImageIDs = array();
	public $entries = null;

	/**
	 * @see Location::cache()
	 */
	public function cache($location, $requestURI, $requestMethod, $match) {
		$this->cachedImageIDs[] = $match[1];
	}

	/**
	 * @see Location::get()
	 */
	public function get($location, $requestURI, $requestMethod, $match) {
		if ($this->entries === null) {
			$this->readEntries();
		}

		$imageID = $match[1];
		if (!isset($this->entries[$imageID])) {
			return '';
		}

		return WCF::getLanguage()->get($location['locationName'], array(
			'$image' => '<a href="index.php?page=EntryImage&amp;imageID='.$imageID.SID_ARG_2ND.'">'.StringUtil::encodeHTML($this->entries[$imageID]->title).'</a>',
			'$entry' => ($this->entries[$imageID]->prefixID ? $this->entries[$imageID]->getPrefix()->getStyledPrefix().' ' : '').'<a href="index.php?page=Entry&amp;entryID='.$this->entries[$imageID]->entryID.SID_ARG_2ND.'">'.StringUtil::encodeHTML($this->entries[$imageID]->subject).'</a>'
		));
	}

	/**
	 * Gets the entries.
	 */
	protected function readEntries() {
		$this->entries = array();

		if (!count($this->cachedImageIDs)) {
			return;
		}

		// get accessible categories
		$categoryIDs = Category::getAccessibleCategories();
		if (empty($categoryIDs)) return;

		$sql = "SELECT		entry_image.imageID, entry_image.title, entry.entryID, entry.prefixID, entry.subject
			FROM		wsif".WSIF_N."_entry_image entry_image
			LEFT JOIN	wsif".WSIF_N."_entry entry
			ON		(entry.entryID = entry_image.entryID)
			WHERE		entry_image.imageID IN (".implode(',', $this->cachedImageIDs).")
					AND entry.isDeleted = 0
					AND entry.isDisabled = 0
					AND entry.categoryID IN (".$categoryIDs.")";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$this->entries[$row['imageID']] = new ViewableEntry(null, $row);
		}
	}
}
?>