<?php
// wcf imports
require_once(WCF_DIR.'lib/data/DatabaseObject.class.php');

/**
 * Represents an entry comment.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	data.entry.comment
 * @category	Infinite Filebase
 */
class EntryComment extends DatabaseObject {
	protected $entry = null;

	/**
	 * Creates a new EntryComment object.
	 *
	 * @param	integer		$commentID
	 * @param 	array<mixed>	$row
	 */
	public function __construct($commentID, $row = null) {
		if ($commentID !== null) {
			$sql = "SELECT	*
				FROM 	wsif".WSIF_N."_entry_comment
				WHERE 	commentID = ".$commentID;
			$row = WCF::getDB()->getFirstRow($sql);
		}
		parent::__construct($row);
	}

	/**
	 * Returns true, if the active user can edit this comment.
	 *
	 * @return	boolean
	 */
	public function isEditable($category) {
		if (($this->userID && $this->userID == WCF::getUser()->userID && $category->getPermission('canEditOwnEntryComment')) || $category->getModeratorPermission('canEditEntryComment')) {
			return true;
		}
		return false;
	}

	/**
	 * Returns true, if the active user can delete this comment.
	 *
	 * @return	boolean
	 */
	public function isDeletable($category) {
		if (($this->userID && $this->userID == WCF::getUser()->userID && $category->getPermission('canDeleteOwnEntryComment')) || $category->getModeratorPermission('canDeleteEntryComment')) {
			return true;
		}
		return false;
	}
}
?>