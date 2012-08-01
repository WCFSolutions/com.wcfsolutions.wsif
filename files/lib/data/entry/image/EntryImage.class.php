<?php
// wcf imports
require_once(WCF_DIR.'lib/data/DatabaseObject.class.php');

/**
 * Represents an entry image in the filebase.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	data.entry.image
 * @category	Infinite Filebase
 */
class EntryImage extends DatabaseObject {
	/**
	 * Creates a new EntryImage object.
	 *
	 * @param	integer		$imageID
	 * @param 	array<mixed>	$row
	 */
	public function __construct($imageID, $row = null) {
		if ($imageID !== null) {
			$sql = "SELECT	*
				FROM 	wsif".WSIF_N."_entry_image
				WHERE 	imageID = ".$imageID;
			$row = WCF::getDB()->getFirstRow($sql);
		}
		parent::__construct($row);
	}

	/**
	 * Returns the formatted description of this image.
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
	 * Returns true, if the active user can edit this image.
	 *
	 * @return	boolean
	 */
	public function isEditable($category) {
		if (($this->userID && $this->userID == WCF::getUser()->userID && $category->getPermission('canEditOwnEntryImage')) || $category->getModeratorPermission('canEditEntryImage')) {
			return true;
		}
		return false;
	}

	/**
	 * Returns true, if the active user can delete this image.
	 *
	 * @return	boolean
	 */
	public function isDeletable($entry, $category) {
		if (($this->userID && $this->userID == WCF::getUser()->userID && $category->getPermission('canDeleteOwnEntryImage')) || $category->getModeratorPermission('canDeleteEntryImage')) {
			return true;
		}
		return false;
	}
}
?>