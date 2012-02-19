<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/entry/Entry.class.php');

/**
 * EntryEditor provides functions to create and edit the data of an entry.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	data.entry
 * @category	Infinite Filebase
 */
class EntryEditor extends Entry {
	/**
	 * Updates this entry.
	 * 
	 * @param	integer		$languageID
	 * @param	integer		$prefixID
	 * @param	string		$subject
	 * @param	string		$message
	 * @param	string		$teaser
	 * @param	array		$options
	 */
	public function update($languageID, $prefixID, $subject, $message, $teaser, $options) {
		$sql = "UPDATE 	wsif".WSIF_N."_entry
			SET	languageID = ".$languageID.",
				prefixID = ".$prefixID.",
				subject = '".escapeString($subject)."',
				message = '".escapeString($message)."',
				teaser = '".escapeString($teaser)."',
				enableSmilies = ".(isset($options['enableSmilies']) ? $options['enableSmilies'] : 1).",
				enableHtml = ".(isset($options['enableHtml']) ? $options['enableHtml'] : 0).",
				enableBBCodes = ".(isset($options['enableBBCodes']) ? $options['enableBBCodes'] : 1)."
			WHERE 	entryID = ".$this->entryID;
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Updates the amount of images of this entry.
	 *
	 * @param	integer		$images
	 */
	public function updateImages($images) {
		$sql = "UPDATE 	wsif".WSIF_N."_entry
			SET	images = IF(".$images." > 0 OR images > ABS(".$images."), images + ".$images.", 0)
			WHERE 	entryID = ".$this->entryID;
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Updates the amount of files of this entry.
	 *
	 * @param	integer		$files
	 */
	public function updateFiles($files) {
		$sql = "UPDATE 	wsif".WSIF_N."_entry
			SET	files = IF(".$files." > 0 OR files > ABS(".$files."), files + ".$files.", 0)
			WHERE 	entryID = ".$this->entryID;
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Updates the tags of this entry.
	 * 
	 * @param	array		$tags
	 */
	public function updateTags($tagArray) {
		// include files
		require_once(WCF_DIR.'lib/data/tag/TagEngine.class.php');
		require_once(WSIF_DIR.'lib/data/entry/TaggedEntry.class.php');
		
		// save tags
		$tagged = new TaggedEntry(null, array(
			'entryID' => $this->entryID,
			'taggable' => TagEngine::getInstance()->getTaggable('com.wcfsolutions.wsif.entry')
		));

		// delete old tags
		TagEngine::getInstance()->deleteObjectTags($tagged, array($this->languageID));
		
		// save new tags
		if (count($tagArray) > 0) TagEngine::getInstance()->addTags($tagArray, $tagged, $this->languageID);
	}
	
	/**
	 * Sets the subject of this entry.
	 * 
	 * @param	string		$subject
	 */
	public function setSubject($subject) {
		if ($subject == $this->subject) return;
		
		$sql = "UPDATE 	wsif".WSIF_N."_entry
			SET	subject = '".escapeString($subject)."'
			WHERE 	entryID = ".$this->entryID;
		WCF::getDB()->registerShutdownUpdate($sql);
	}
	
	/**
	 * Sets the prefix of this entry.
	 * 
	 * @param	integer		$prefixID
	 */
	public function setPrefixID($prefixID) {
		if ($prefixID == $this->prefixID) return;
		
		$sql = "UPDATE 	wsif".WSIF_N."_entry
			SET	prefixID = ".$prefixID."
			WHERE 	entryID = ".$this->entryID;
		WCF::getDB()->registerShutdownUpdate($sql);
	}
	
	/**
	 * Marks this entry.
	 */
	public function mark() {
		$markedEntries = self::getMarkedEntries();
		if ($markedEntries === null || !is_array($markedEntries)) { 
			$markedEntries = array($this->entryID);
			WCF::getSession()->register('markedEntries', $markedEntries);
		}
		else {
			if (!in_array($this->entryID, $markedEntries)) {
				array_push($markedEntries, $this->entryID);
				WCF::getSession()->register('markedEntries', $markedEntries);
			}
		}
	}
	
	/**
	 * Unmarks this entry.
	 */
	public function unmark() {
		$markedEntries = self::getMarkedEntries();
		if (is_array($markedEntries) && in_array($this->entryID, $markedEntries)) {
			$key = array_search($this->entryID, $markedEntries);
			unset($markedEntries[$key]);
			if (count($markedEntries) == 0) {
				self::unmarkAll();
			} 
			else {
				WCF::getSession()->register('markedEntries', $markedEntries);
			}
		}
	}
	
	/**
	 * Disables this entry.
	 */
	public function disable() {
		self::disableAll($this->entryID);
	}
	
	/**
	 * Enables this entry.
	 */
	public function enable() {
		self::enableAll($this->entryID);
	}
	
	/**
	 * Moves this entry into the recycle bin.
	 */
	public function trash($reason = '') {
		self::trashAll($this->entryID, $reason);
	}
	
	/**
	 * Deletes this entry completely.
	 */
	public function delete($updateUserStats = true) {
		self::deleteAllCompletely($this->entryID, $updateUserStats);
	}
	
	/**
	 * Restores this deleted thread.
	 */
	public function restore() {
		self::restoreAll($this->entryID);
	}

	/**
	 * Creates a new entry.
	 * 
	 * @param	integer		$categoryID
	 * @param	integer		$languageID
	 * @param	integer		$prefixID
	 * @param	string		$subject
	 * @param	string		$message
	 * @param	string		$teaser
	 * @param	integer		$userID
	 * @param	string		$username
	 * @param	array		$options
	 * @param	string		$ipAddress
	 * @param	integer		$isDisabled
	 * @return	EntryEditor
	 */
	public static function create($categoryID, $languageID, $prefixID, $subject, $message, $teaser, $userID, $username, $options, $ipAddress = null, $isDisabled = 0) {
		if ($ipAddress === null) $ipAddress = WCF::getSession()->ipAddress;
		
		// insert entry
		$sql = "INSERT INTO	wsif".WSIF_N."_entry
					(categoryID, languageID, prefixID, userID, username, subject, message, teaser, time, everEnabled, isDisabled, ipAddress, enableSmilies, enableHtml, enableBBCodes)
			VALUES		(".$categoryID.", ".$languageID.", ".$prefixID.", ".$userID.", '".escapeString($username)."', '".escapeString($subject)."', '".escapeString($message)."', '".escapeString($teaser)."', ".TIME_NOW.", ".($isDisabled ? 0 : 1).", ".$isDisabled.", '".escapeString($ipAddress)."',
					".(isset($options['enableSmilies']) ? $options['enableSmilies'] : 1).",
					".(isset($options['enableHtml']) ? $options['enableHtml'] : 0).",
					".(isset($options['enableBBCodes']) ? $options['enableBBCodes'] : 1).")";
		WCF::getDB()->sendQuery($sql);
		
		// get entry id
		$entryID = WCF::getDB()->getInsertID("wsif".WSIF_N."_entry", 'entryID');
		
		// get entry
		$entry = new EntryEditor($entryID);
		
		// return entry
		return $entry;
	}
	
	/**
	 * Creates the preview of an entry with the given data.
	 * 
	 * @param	string		$subject
	 * @param	string		$text
	 * 
	 * @return	string
	 */
	public static function createPreview($subject, $message, $enableSmilies = 1, $enableHtml = 0, $enableBBCodes = 1) {
		$row = array(
			'entryID' => 0,
			'subject' => $subject,
			'message' => $message,
			'enableSmilies' => $enableSmilies,
			'enableHtml' => $enableHtml,
			'enableBBCodes' => $enableBBCodes,
			'messagePreview' => true
		);

		require_once(WSIF_DIR.'lib/data/entry/ViewableEntry.class.php');
		$entry = new ViewableEntry(null, $row);
		return $entry->getFormattedMessage();
	}
	
	/**
	 * Returns the currently marked entries. 
	 */
	public static function getMarkedEntries() {
		$sessionVars = WCF::getSession()->getVars();
		if (isset($sessionVars['markedEntries'])) {
			return $sessionVars['markedEntries'];
		}
		return null;
	}
	
	/**
	 * Unmarks all marked entries.
	 */
	public static function unmarkAll() {
		WCF::getSession()->unregister('markedEntries');
	}
	
	/**
	 * Disables the entries with the given entry ids.
	 *
	 * @param	string		$entryIDs
	 */
	public static function disableAll($entryIDs) {
		if (empty($entryIDs)) return;
		
		$sql = "UPDATE 	wsif".WSIF_N."_entry
			SET	isDeleted = 0,
				isDisabled = 1
			WHERE 	entryID IN (".$entryIDs.")";
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Enables the entries with the given entry ids.
	 */
	public static function enableAll($entryIDs) {
		if (empty($entryIDs)) return;
		
		// get not yet enabled entries
		$statEntryIDs = '';
		$sql = "SELECT	entryID
			FROM	wsif".WSIF_N."_entry
			WHERE	entryID IN (".$entryIDs.")
				AND isDisabled = 1
				AND everEnabled = 0";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			if (!empty($statEntryIDs)) $statEntryIDs .= ',';
			$statEntryIDs .= $row['entryID'];
		}
		
		// update user entries and activity points
		self::updateUserStats($statEntryIDs, 'enable');
		
		// enable entries
		$sql = "UPDATE 	wsif".WSIF_N."_entry
			SET	isDisabled = 0,
				everEnabled = 1
			WHERE 	entryID IN (".$entryIDs.")";
		WCF::getDB()->registerShutdownUpdate($sql);
	}	
	
	/**
	 * Refreshes the stats of this entry.
	 */
	public function refresh() {
		self::refreshAll($this->entryID);
	}
	
	/**
	 * Updates the user stats.
	 * 
	 * @param	string		$entryIDs
	 * @param 	string		$mode
	 */
	public static function updateUserStats($entryIDs, $mode) {
		if (empty($entryIDs)) return;
				
		// update user entries and activity points
		$userEntries = array();
		$userActivityPoints = array();
		$sql = "SELECT	categoryID, userID
			FROM	wsif".WSIF_N."_entry
			WHERE	entryID IN (".$entryIDs.")
				".($mode != 'enable' ? "AND everEnabled = 1" : '')."
				AND userID <> 0";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			switch ($mode) {
				case 'enable':
					// entries
					if (!isset($userEntries[$row['userID']])) $userEntries[$row['userID']] = 0;
					$userEntries[$row['userID']]++;
					// activity points
					if (!isset($userActivityPoints[$row['userID']])) $userActivityPoints[$row['userID']] = 0;
					$userActivityPoints[$row['userID']] += ACTIVITY_POINTS_PER_ENTRY;
					break;
				case 'delete':
					// entries
					if (!isset($userEntries[$row['userID']])) $userEntries[$row['userID']] = 0;
					$userEntries[$row['userID']]--;
					// activity points
					if (!isset($userActivityPoints[$row['userID']])) $userActivityPoints[$row['userID']] = 0;
					$userActivityPoints[$row['userID']] -= ACTIVITY_POINTS_PER_ENTRY;
					break;
			}
		}
		
		// save user entries
		if (count($userEntries)) {
			require_once(WSIF_DIR.'lib/data/user/WSIFUser.class.php');
			foreach ($userEntries as $userID => $entries) {
				WSIFUser::updateUserEntries($userID, $entries);
			}
		}
		
		// save activity points
		if (count($userActivityPoints)) {
			require_once(WCF_DIR.'lib/data/user/rank/UserRank.class.php');
			foreach ($userActivityPoints as $userID => $points) {
				UserRank::updateActivityPoints($points, $userID);
			}
		}
	}
	
	/**
	 * Moves all entries with the given entry ids into the category with the given category id.
	 *
	 * @param	string		$entryIDs
	 * @param	integer		$newCategoryID
	 */
	public static function moveAll($entryIDs, $newCategoryID) {
		if (empty($entryIDs)) return;
		
		// update user posts and activity points
		self::updateUserStats($entryIDs, 'move', $newCategoryID);
		
		// move entries
		$sql = "UPDATE 	wsif".WSIF_N."_entry
			SET	categoryID = ".$newCategoryID."
			WHERE 	entryID IN (".$entryIDs.")
				AND categoryID <> ".$newCategoryID;
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Refreshes the stats of this entry.
	 */
	public static function refreshAll($entryIDs) {
		if (empty($entryIDs)) return;
		
		$sql = "UPDATE 	wsif".WSIF_N."_entry entry
			SET	images = (
					SELECT	COUNT(*)
					FROM	wsif".WSIF_N."_entry_image
					WHERE	entryID = entry.entryID
				),
				files = (
					SELECT	COUNT(*)
					FROM	wsif".WSIF_N."_entry_file
					WHERE	entryID = entry.entryID
				),
				downloads = (
					SELECT	IFNULL(SUM(downloads), 0)
					FROM	wsif".WSIF_N."_entry_file
					WHERE	entryID = entry.entryID
				)
			WHERE 	entryID IN (".$entryIDs.")";
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Deletes the entries with the given entry ids.
	 *
	 * @param	array		$entryIDs
	 * @param	string		$reason
	 */
	public static function deleteAll($entryIDs, $reason = '') {
		if (empty($entryIDs)) return;
		
		$trashIDs = '';
		$deleteIDs = '';
		if (ENTRY_ENABLE_RECYCLE_BIN) {
			$sql = "SELECT 	entryID, isDeleted
				FROM 	wsif".WSIF_N."_entry
				WHERE 	entryID IN (".$entryIDs.")";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				if ($row['isDeleted']) {
					if (!empty($deleteIDs)) $deleteIDs .= ',';
					$deleteIDs .= $row['entryID'];
				}
				else {
					if (!empty($trashIDs)) $trashIDs .= ',';
					$trashIDs .= $row['entryID'];
				}
			}
		}
		else {
			$deleteIDs = $entryIDs;
		}
		
		self::trashAll($trashIDs, $reason);
		self::deleteAllCompletely($deleteIDs);
	}
	
	/**
	 * Moves the entries with the given entry ids into the recycle bin.
	 *
	 * @param	array		$entryIDs
	 * @param	string		$reason
	 */
	public static function trashAll($entryIDs, $reason = '') {
		if (empty($entryIDs)) return;
		
		// trash entry
		$sql = "UPDATE 	wsif".WSIF_N."_entry
			SET	isDeleted = 1,
				deleteTime = ".TIME_NOW.",
				deletedBy = '".escapeString(WCF::getUser()->username)."',
				deletedByID = ".WCF::getUser()->userID.",
				deleteReason = '".escapeString($reason)."',
				isDisabled = 0
			WHERE 	entryID IN (".$entryIDs.")";
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Deletes the entries with the given entry ids completely.
	 *
	 * @param	string		$entryIDs
	 */
	public static function deleteAllCompletely($entryIDs, $updateUserStats = true, $deleteComments = true, $deleteImages = true, $deleteFiles = true) {
		if (empty($entryIDs)) return;
		
		// update user stats
		if ($updateUserStats) {
			self::updateUserStats($entryIDs, 'delete');
		}
		
		// get all comment ids
		if ($deleteComments) {
			$commentIDs = '';
			$sql = "SELECT	commentID
				FROM	wsif".WSIF_N."_entry_comment
				WHERE	entryID IN (".$entryIDs.")";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				if (!empty($commentIDs)) $commentIDs .= ',';
				$commentIDs .= $row['commentID'];
			}
			if (!empty($commentIDs)) {
				// delete comments
				require_once(WSIF_DIR.'lib/data/entry/comment/EntryCommentEditor.class.php');
				EntryCommentEditor::deleteAll($commentIDs);
			}
		}
		
		// get all image ids
		if ($deleteImages) {
			$imageIDs = '';
			$sql = "SELECT	imageID
				FROM	wsif".WSIF_N."_entry_image
				WHERE	entryID IN (".$entryIDs.")";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				if (!empty($imageIDs)) $imageIDs .= ',';
				$imageIDs .= $row['imageID'];
			}
			if (!empty($imageIDs)) {
				// delete images
				require_once(WSIF_DIR.'lib/data/entry/image/EntryImageEditor.class.php');
				EntryImageEditor::deleteAll($imageIDs);
			}
		}
		
		// get all file ids
		if ($deleteFiles) {
			$fileIDs = '';
			$sql = "SELECT	fileID
				FROM	wsif".WSIF_N."_entry_file
				WHERE	entryID IN (".$entryIDs.")";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				if (!empty($fileIDs)) $fileIDs .= ',';
				$fileIDs .= $row['fileID'];
			}
			if (!empty($fileIDs)) {
				// delete images
				require_once(WSIF_DIR.'lib/data/entry/file/EntryFileEditor.class.php');
				EntryFileEditor::deleteAll($fileIDs);
			}
		}
		
		// delete entry
		$sql = "DELETE FROM	wsif".WSIF_N."_entry
			WHERE 		entryID IN (".$entryIDs.")";
		WCF::getDB()->sendQuery($sql);
		
		// delete entry rating
		$sql = "DELETE FROM	wsif".WSIF_N."_entry_rating
			WHERE 		entryID IN (".$entryIDs.")";
		WCF::getDB()->registerShutdownUpdate($sql);
		
		// delete entry visitors
		$sql = "DELETE FROM	wsif".WSIF_N."_entry_visitor
			WHERE 		entryID IN (".$entryIDs.")";
		WCF::getDB()->registerShutdownUpdate($sql);
		
		// delete tags
		if (MODULE_TAGGING) {
			require_once(WCF_DIR.'lib/data/tag/TagEngine.class.php');
			$taggable = TagEngine::getInstance()->getTaggable('com.wcfsolutions.wsif.entry');
			
			$sql = "DELETE FROM	wcf".WCF_N."_tag_to_object
				WHERE 		taggableID = ".$taggable->getTaggableID()."
						AND objectID IN (".$entryIDs.")";
			WCF::getDB()->registerShutdownUpdate($sql);
		}
	}
	
	/**
	 * Restores the entries with the given entry ids.
	 *
	 * @param	string		$entryIDs
	 */
	public static function restoreAll($entryIDs) {
		if (empty($entryIDs)) return;
		
		// restore entries
		$sql = "UPDATE 	wsif".WSIF_N."_entry
			SET	isDeleted = 0
			WHERE 	entryID IN (".$entryIDs.")";
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Returns the categories of the entries with the given entry ids.
	 * 
	 * @param	string		$entryIDs
	 * @return	array
	 */
	public static function getCategoriesByEntryIDs($entryIDs) {
		if (empty($entryIDs)) return array(array(), '', 'categories' => array(), 'categoryIDs' => '');
		
		$categories = array();
		$categoryIDs = '';
		$sql = "SELECT 	DISTINCT categoryID
			FROM 	wsif".WSIF_N."_entry
			WHERE 	entryID IN (".$entryIDs.")";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			if (!empty($categoryIDs)) $categoryIDs .= ',';
			$categoryIDs .= $row['categoryID'];
			$categories[$row['categoryID']] = new CategoryEditor($row['categoryID']);
		}
		
		return array($categories, $categoryIDs, 'categories' => $categories, 'categoryIDs' => $categoryIDs);
	}
}
?>