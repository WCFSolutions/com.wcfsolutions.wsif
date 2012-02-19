<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/category/Category.class.php');
require_once(WSIF_DIR.'lib/data/entry/prefix/EntryPrefix.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/DatabaseObject.class.php');

/**
 * Represents an entry.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	data.entry
 * @category	Infinite Filebase
 */
class Entry extends DatabaseObject {
	protected $category = null;
	protected $prefix = null;

	/**
	 * Creates a new Entry object.
	 * 
	 * @param	integer		$entryID
	 * @param 	array<mixed>	$row
	 */
	public function __construct($entryID, $row = null) {
		if ($entryID !== null) {
			$sql = "SELECT		entry.*,
						entry_rating.rating AS userRating 
				FROM 		wsif".WSIF_N."_entry entry
				LEFT JOIN 	wsif".WSIF_N."_entry_rating entry_rating
				ON 		(entry_rating.entryID = entry.entryID
						AND ".(WCF::getUser()->userID ? "entry_rating.userID = ".WCF::getUser()->userID : "entry_rating.ipAddress = '".escapeString(WCF::getSession()->ipAddress)."'").")
				WHERE 		entry.entryID = ".$entryID;
			$row = WCF::getDB()->getFirstRow($sql);
		}
		parent::__construct($row);
	}

	/**
	 * Handles the given resultset.
	 *
	 * @param 	array 		$row		resultset with entry data form database
	 */
	protected function handleData($data) {
		parent::handleData($data);
		
		// get prefix
		if ($this->prefixID) {
			$this->prefix = new EntryPrefix($this->prefixID);
		}
	}
	
	/**
	 * Enters this entry.
	 */
	public function enter($category = null) {
		if ($category == null || $category->categoryID != $this->categoryID) {
			$category = new Category($this->categoryID);
		}	
		$category->enter();
		
		// check permissions
		if ((!$category->getPermission('canViewEntry') && (!$category->getPermission('canViewOwnEntry') || !$this->userID || $this->userID != WCF::getUser()->userID)) || ($this->isDeleted && !$category->getModeratorPermission('canViewDeletedEntry')) || ($this->isDisabled && !$category->getModeratorPermission('canEnableEntry'))) {
			throw new PermissionDeniedException();
		}
		
		// refresh session
		WCF::getSession()->setEntryID($this->entryID);
			
		// save category
		$this->category = $category;
	}
	
	/**
	 * Returns the category of this entry.
	 *
	 * @return	Category
	 */
	public function getCategory() {
		if ($this->category === null) {
			$this->category = new Category($this->categoryID);
		}
		return $this->category;
	}
	
	/**
	 * Returns the prefix of this entry.
	 *
	 * @return	EntryPrefix
	 */
	public function getPrefix() {
		return $this->prefix;
	}
	
	/**
	 * Returns the number of views per day.
	 *
	 * @return	float
	 */
	public function getViewsPerDay() {
		$age = round(((TIME_NOW - $this->time) / 86400), 0);
		if ($age > 0) {
			return $this->views / $age;
		}
		return $this->views;
	}
	
	/**
	 * Returns the number of downloads per day.
	 *
	 * @return	float
	 */
	public function getDownloadsPerDay() {
		$age = round(((TIME_NOW - $this->time) / 86400), 0);
		if ($age > 0) {
			return $this->downloads / $age;
		}
		return $this->downloads;
	}
	
	/**
	 * Returns true, if this entry is marked.
	 *
	 * @return	integer
	 */
	public function isMarked() {
		$sessionVars = WCF::getSession()->getVars();
		if (isset($sessionVars['markedEntries'])) {
			if (in_array($this->entryID, $sessionVars['markedEntries'])) return 1;
		}		
		return 0;
	}

	/**
	 * Returns the tags of this entry.
	 * 
	 * @return	array
	 */
	public function getTags($languageIDArray) {
		// include files
		require_once(WSIF_DIR.'lib/data/entry/TaggedEntry.class.php');
		require_once(WCF_DIR.'lib/data/tag/TagEngine.class.php');
		
		// get tags
		return TagEngine::getInstance()->getTagsByTaggedObject(new TaggedEntry(null, array(
			'entryID' => $this->entryID,
			'taggable' => TagEngine::getInstance()->getTaggable('com.wcfsolutions.wsif.entry')
		)), $languageIDArray);
	}
	
	/**
	 * Returns true, if the active user can comment this entry.
	 * 
	 * @param	Category		$category
	 * @return	boolean
	 */
	public function isCommentable(Category $category) {
		return $category->getPermission('canCommentEntry');
	}
	
	/**
	 * Returns true, if the active user can rate this entry.
	 * 
	 * @param	Category		$category
	 * @return	boolean
	 */
	public function isRatable($category) {
		if ($this->userID && $this->userID != WCF::getUser()->userID && $category->getPermission('canRateEntry')) {
			return true;
		}
		return false;
	}
	
	/**
	 * Returns true, if the active user can edit this entry.
	 * 
	 * @param	Category		$category
	 * @return	boolean
	 */
	public function isEditable($category) {
		if (($this->userID && $this->userID == WCF::getUser()->userID && $category->getPermission('canEditOwnEntry')) || $category->getModeratorPermission('canEditEntry')) {
			return true;
		}
		return false;
	}
	
	/**
	 * Returns true, if the active user can delete this entry.
	 * 
	 * @param	Category		$category
	 * @return	boolean
	 */
	public function isDeletable($category) {
		if (($this->userID && $this->userID == WCF::getUser()->userID && $category->getPermission('canDeleteOwnEntry')) || $category->getModeratorPermission('canDeleteEntry')) {
			return true;
		}
		return false;
	}
	
	/**
	 * Returns an editor object for this entry.
	 *
	 * @return	EntryEditor
	 */
	public function getEditor() {
		require_once(WSIF_DIR.'lib/data/entry/EntryEditor.class.php');
		return new EntryEditor(null, $this->data);
	}
}
?>