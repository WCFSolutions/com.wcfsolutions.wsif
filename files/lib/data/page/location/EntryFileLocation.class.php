<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/category/Category.class.php');
require_once(WSIF_DIR.'lib/data/entry/ViewableEntry.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/page/location/Location.class.php');

/**
 * EntryFileLocation is an implementation of Location for the entry file page.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	data.page.location
 * @category	Infinite Filebase
 */
class EntryFileLocation implements Location {
	public $cachedFileIDs = array();
	public $entries = null;
	
	/**
	 * @see Location::cache()
	 */
	public function cache($location, $requestURI, $requestMethod, $match) {
		$this->cachedFileIDs[] = $match[1];
	}
	
	/**
	 * @see Location::get()
	 */
	public function get($location, $requestURI, $requestMethod, $match) {
		if ($this->entries === null) {
			$this->readEntries();
		}
		
		$fileID = $match[1];
		if (!isset($this->entries[$fileID])) {
			return '';
		}
		
		return WCF::getLanguage()->get($location['locationName'], array(
			'$file' => '<a href="index.php?page=EntryFile&amp;fileID='.$fileID.SID_ARG_2ND.'">'.StringUtil::encodeHTML($this->entries[$fileID]->title).'</a>',
			'$entry' => ($this->entries[$fileID]->prefixID ? $this->entries[$fileID]->getPrefix()->getStyledPrefix().' ' : '').'<a href="index.php?page=Entry&amp;entryID='.$this->entries[$fileID]->entryID.SID_ARG_2ND.'">'.StringUtil::encodeHTML($this->entries[$fileID]->subject).'</a>'
		));
	}
	
	/**
	 * Gets the entries.
	 */
	protected function readEntries() {
		$this->entries = array();
		
		if (!count($this->cachedFileIDs)) {
			return;
		}
		
		// get accessible categories
		$categoryIDs = Category::getAccessibleCategories();
		if (empty($categoryIDs)) return;
		
		$sql = "SELECT		entry_file.fileID, entry_file.title, entry.entryID, entry.prefixID, entry.subject
			FROM		wsif".WSIF_N."_entry_file entry_file
			LEFT JOIN	wsif".WSIF_N."_entry entry
			ON		(entry.entryID = entry_file.entryID)
			WHERE		entry_file.fileID IN (".implode(',', $this->cachedFileIDs).")
					AND entry.isDeleted = 0
					AND entry.isDisabled = 0
					AND entry.categoryID IN (".$categoryIDs.")";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$this->entries[$row['fileID']] = new ViewableEntry(null, $row);
		}
	}
}
?>