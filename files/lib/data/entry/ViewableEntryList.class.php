<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/entry/EntryList.class.php');
require_once(WSIF_DIR.'lib/data/entry/ViewableEntry.class.php');

/**
 * Represents a viewable list of entries.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	data.entry
 * @category	Infinite Filebase
 */
class ViewableEntryList extends EntryList {
	/**
	 * list of object ids
	 * 
	 * @var	array<integer>
	 */
	public $objectIDArray = array();
	
	/**
	 * list of tags
	 * 
	 * @var	array
	 */
	public $tags = array();
	
	/**
	 * Gets the object ids.
	 */
	protected function readObjectIDArray() {
		$sql = "SELECT		entry.entryID
			FROM		wsif".WSIF_N."_entry entry
			".(!empty($this->sqlConditions) ? "WHERE ".$this->sqlConditions : '')."
			".(!empty($this->sqlOrderBy) ? "ORDER BY ".$this->sqlOrderBy : '');
		$result = WCF::getDB()->sendQuery($sql, $this->sqlLimit, $this->sqlOffset);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$this->objectIDArray[] = $row['entryID'];
		}
	}

	/**
	 * Gets the list of tags.
	 */
	protected function readTags() {
		if (MODULE_TAGGING) {
			require_once(WCF_DIR.'lib/data/tag/TagEngine.class.php');
			$taggable = TagEngine::getInstance()->getTaggable('com.wcfsolutions.wsif.entry');
			$sql = "SELECT		tag_to_object.objectID AS entryID,
						tag.tagID, tag.name
				FROM		wcf".WCF_N."_tag_to_object tag_to_object
				LEFT JOIN	wcf".WCF_N."_tag tag
				ON		(tag.tagID = tag_to_object.tagID)
				WHERE		tag_to_object.taggableID = ".$taggable->getTaggableID()."
						AND tag_to_object.languageID IN (".implode(',', (count(WCF::getSession()->getVisibleLanguageIDArray()) ? WCF::getSession()->getVisibleLanguageIDArray() : array(0))).")
						AND tag_to_object.objectID IN (".implode(',', $this->objectIDArray).")";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				if (!isset($this->tags[$row['entryID']])) $this->tags[$row['entryID']] = array();
				$this->tags[$row['entryID']][] = new Tag(null, $row);
			}
		}
	}
	
	/**
	 * @see DatabaseObjectList::readObjects()
	 */
	public function readObjects() {
		// get ids
		$this->readObjectIDArray();
		
		// get entries
		if (count($this->objectIDArray)) {
			$this->readTags();
			
			$sql = "SELECT		".(!empty($this->sqlSelects) ? $this->sqlSelects.',' : '')."
						entry.*,
						entry_image.imageID, entry_image.hasThumbnail
				FROM		wsif".WSIF_N."_entry entry
				LEFT JOIN	wsif".WSIF_N."_entry_image entry_image
				ON		(entry_image.imageID = entry.defaultImageID)
				".$this->sqlJoins."
				WHERE 		entry.entryID IN (".implode(',', $this->objectIDArray).")
				".(!empty($this->sqlOrderBy) ? "ORDER BY ".$this->sqlOrderBy : '');
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				$this->entries[] = new ViewableEntry(null, $row);
			}
		}
	}

	/**
	 * Returns the list of tags.
	 * 
	 * @return	array
	 */
	public function getTags() {
		return $this->tags;
	}
}
?>