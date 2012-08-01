<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/category/Category.class.php');
require_once(WSIF_DIR.'lib/data/entry/ViewableEntry.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/page/location/Location.class.php');

/**
 * EntryCommentLocation is an implementation of Location for the entry file page.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	data.page.location
 * @category	Infinite Filebase
 */
class EntryCommentLocation implements Location {
	public $cachedCommentIDs = array();
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

		$commentID = $match[1];
		if (!isset($this->entries[$commentID])) {
			return '';
		}

		return WCF::getLanguage()->get($location['locationName'], array(
			'$comment' => '<a href="index.php?page=EntryComments&amp;commentID='.$commentID.SID_ARG_2ND.'#comment'.$commentID.'">'.StringUtil::encodeHTML($this->entries[$commentID]->commentSubject).'</a>',
			'$entry' => ($this->entries[$commentID]->prefixID ? $this->entries[$commentID]->getPrefix()->getStyledPrefix().' ' : '').'<a href="index.php?page=Entry&amp;entryID='.$this->entries[$commentID]->entryID.SID_ARG_2ND.'">'.StringUtil::encodeHTML($this->entries[$commentID]->subject).'</a>'
		));
	}

	/**
	 * Gets the entries.
	 */
	protected function readEntries() {
		$this->entries = array();

		if (!count($this->cachedCommentIDs)) {
			return;
		}

		// get accessible categories
		$categoryIDs = Category::getAccessibleCategories();
		if (empty($categoryIDs)) return;

		$sql = "SELECT		entry_comment.commentID, entry_comment.subject as commentSubject, entry.entryID, entry.prefixID, entry.subject
			FROM		wsif".WSIF_N."_entry_comment entry_comment
			LEFT JOIN	wsif".WSIF_N."_entry entry
			ON		(entry.entryID = entry_comment.entryID)
			WHERE		entry_comment.commentID IN (".implode(',', $this->cachedCommentIDs).")
					AND entry.isDeleted = 0
					AND entry.isDisabled = 0
					AND entry.categoryID IN (".$categoryIDs.")";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$this->entries[$row['entryID']] = new ViewableEntry(null, $row);
		}
	}
}
?>