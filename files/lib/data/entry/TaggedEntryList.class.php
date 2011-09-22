<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/entry/ViewableEntryList.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/DatabaseObjectList.class.php');

/**
 * TaggedEntryList provides extended functions for displaying a list of entries of a specific tag.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	data.entry
 * @category	Infinite Filebase
 */
class TaggedEntryList extends ViewableEntryList {
	/**
	 * tag id
	 * 
	 * @var	integer
	 */
	public $tagID = 0;
	
	/**
	 * taggable object
	 * 
	 * @var	Taggable
	 */
	public $taggable = null;

	/**
	 * Creates a new TaggedEntryList object.
	 */
	public function __construct($tagID) {
		$this->tagID = $tagID;
		$this->taggable = TagEngine::getInstance()->getTaggable('com.wcfsolutions.wsif.entry');
	}
	
	/**
	 * @see DatabaseObjectList::countObjects()
	 */
	public function countObjects() {
		if (!empty($this->sqlConditions)) {
			$sql = "SELECT	COUNT(*) AS count
				FROM	wcf".WCF_N."_tag_to_object tag_to_object,
					wsif".WSIF_N."_entry entry
				WHERE	tag_to_object.tagID = ".$this->tagID."
					AND tag_to_object.taggableID = ".$this->taggable->getTaggableID()."
					AND entry.entryID = tag_to_object.objectID
					AND ".$this->sqlConditions;
		}
		else {
			$sql = "SELECT	COUNT(*) AS count
				FROM	wcf".WCF_N."_tag_to_object
				WHERE	tagID = ".$this->tagID."
					AND taggableID = ".$this->taggable->getTaggableID();
		}
		$row = WCF::getDB()->getFirstRow($sql);
		return $row['count'];
	}
	
	/**
	 * @see ViewableEntryList::readObjectIDArray()
	 */
	protected function readObjectIDArray() {
		$sql = "SELECT		entry.entryID
			FROM		wcf".WCF_N."_tag_to_object tag_to_object,
					wsif".WSIF_N."_entry entry
			WHERE		tag_to_object.tagID = ".$this->tagID."
					AND tag_to_object.taggableID = ".$this->taggable->getTaggableID()."
					AND entry.entryID = tag_to_object.objectID
					".(!empty($this->sqlConditions) ? "AND ".$this->sqlConditions : '')."
			".(!empty($this->sqlOrderBy) ? "ORDER BY ".$this->sqlOrderBy : '');
		$result = WCF::getDB()->sendQuery($sql, $this->sqlLimit, $this->sqlOffset);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$this->objectIDArray[] = $row['entryID'];
		}
	}
}
?>