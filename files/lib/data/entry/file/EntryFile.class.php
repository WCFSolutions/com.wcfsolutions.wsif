<?php
// wcf imports
require_once(WCF_DIR.'lib/data/DatabaseObject.class.php');

/**
 * Represents an entry file.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	data.entry.file
 * @category	Infinite Filebase
 */
class EntryFile extends DatabaseObject {
	/**
	 * Defines that a file is an upload.
	 */
	const TYPE_UPLOAD = 0;

	/**
	 * Defines that a file is an external link.
	 */
	const TYPE_LINK = 1;

	/**
	 * Creates a new EntryFile object.
	 *
	 * @param	integer		$fileID
	 * @param 	array<mixed>	$row
	 */
	public function __construct($fileID, $row = null) {
		if ($fileID !== null) {
			$sql = "SELECT	*
				FROM 	wsif".WSIF_N."_entry_file
				WHERE 	fileID = ".$fileID;
			$row = WCF::getDB()->getFirstRow($sql);
		}
		parent::__construct($row);
	}

	/**
	 * Returns the formatted description of this file.
	 *
	 * @return	string
	 */
	public function getFormattedDescription() {
		if ($this->description) {
			return nl2br(StringUtil::encodeHTML($this->description));
		}
		return '';
	}

	/**
	 * Returns the number of views per day.
	 *
	 * @return	float
	 */
	public function getViewsPerDay() {
		$age = round(((TIME_NOW - $this->uploadTime) / 86400), 0);
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
		$age = round(((TIME_NOW - $this->uploadTime) / 86400), 0);
		if ($age > 0) {
			return $this->downloads / $age;
		}
		return $this->downloads;
	}

	/**
	 * Returns true, if this file is an upload.
	 *
	 * @return	boolean
	 */
	public function isUpload() {
		if ($this->fileType == self::TYPE_UPLOAD) {
			return true;
		}
		return false;
	}

	/**
	 * Returns true, if this file is an external link.
	 *
	 * @return	boolean
	 */
	public function isExternalLink() {
		if ($this->fileType == self::TYPE_LINK) {
			return true;
		}
		return false;
	}

	/**
	 * Returns true, if the active user can edit this file.
	 *
	 * @return	boolean
	 */
	public function isEditable($category) {
		if (($this->userID && $this->userID == WCF::getUser()->userID && $category->getPermission('canEditOwnEntryFile')) || $category->getModeratorPermission('canEditEntryFile')) {
			return true;
		}
		return false;
	}

	/**
	 * Returns true, if the active user can delete this file.
	 *
	 * @return	boolean
	 */
	public function isDeletable($entry, $category) {
		if ($this->fileID != $entry->defaultFileID && (($this->userID && $this->userID == WCF::getUser()->userID && $category->getPermission('canDeleteOwnEntryFile')) || $category->getModeratorPermission('canDeleteEntryFile'))) {
			return true;
		}
		return false;
	}
}
?>