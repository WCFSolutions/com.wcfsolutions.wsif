<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/category/Category.class.php');
require_once(WSIF_DIR.'lib/data/entry/TaggedEntry.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/tag/AbstractTaggableObject.class.php');

/**
 * An implementation of Taggable to support the tagging of entries.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	data.entry
 * @category	Infinite Filebase
 */
class TaggableEntry extends AbstractTaggableObject {
	/**
	 * @see Taggable::getObjectsByIDs()
	 */
	public function getObjectsByIDs($objectIDs, $taggedObjects) {
		$sql = "SELECT		*
			FROM		wsif".WSIF_N."_entry
			WHERE		entryID IN (".implode(",", $objectIDs).")
					AND isDeleted = 0
					AND isDisabled = 0";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$row['taggable'] = $this;
			$taggedObjects[] = new TaggedEntry(null, $row);
		}
		return $taggedObjects;
	}
	
	/**
	 * @see Taggable::countObjectsByTagID()
	 */
	public function countObjectsByTagID($tagID) {
		$accessibleCategoryIDArray = Category::getAccessibleCategoryIDArray();
		if (count($accessibleCategoryIDArray) == 0) return 0;
		
		$sql = "SELECT		COUNT(*) AS count
			FROM		wcf".WCF_N."_tag_to_object tag_to_object
			LEFT JOIN	wsif".WSIF_N."_entry entry
			ON		(entry.entryID = tag_to_object.objectID)
			WHERE 		tag_to_object.tagID = ".$tagID."
					AND tag_to_object.taggableID = ".$this->getTaggableID()."
					AND entry.categoryID IN (".implode(',', $accessibleCategoryIDArray).")
					AND entry.isDeleted = 0
					AND entry.isDisabled = 0";
		$row = WCF::getDB()->getFirstRow($sql);
		return $row['count'];
	}
	
	/**
	 * @see Taggable::getObjectsByTagID()
	 */
	public function getObjectsByTagID($tagID, $limit = 0, $offset = 0) {
		$accessibleCategoryIDArray = Category::getAccessibleCategoryIDArray();
		if (count($accessibleCategoryIDArray) == 0) return array();
		
		$entries = array();
		$sql = "SELECT		entry.*,
					entry_image.imageID, entry_image.hasThumbnail
			FROM		wcf".WCF_N."_tag_to_object tag_to_object
			LEFT JOIN	wsif".WSIF_N."_entry entry
			ON		(entry.entryID = tag_to_object.objectID)
			LEFT JOIN	wsif".WSIF_N."_entry_image entry_image
			ON		(entry_image.imageID = entry.defaultImageID)
			LEFT JOIN 	wsif".WSIF_N."_category category
			ON 		(category.categoryID = entry.categoryID)
			WHERE		tag_to_object.tagID = ".$tagID."
					AND tag_to_object.taggableID = ".$this->getTaggableID()."
					AND entry.categoryID IN (".implode(',', $accessibleCategoryIDArray).")
					AND entry.isDeleted = 0
					AND entry.isDisabled = 0
			ORDER BY	entry.time DESC";
		$result = WCF::getDB()->sendQuery($sql, $limit, $offset);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$row['taggable'] = $this;
			$entries[] = new TaggedEntry(null, $row);
		}
		return $entries;
	}

	/**
	 * @see Taggable::getIDFieldName()
	 */
	public function getIDFieldName() {
		return 'entryID';
	}
	
	/**
	 * @see Taggable::getResultTemplateName()
	 */
	public function getResultTemplateName() {
		return 'taggedEntries';
	}
	
	/**
	 * @see Taggable::getSmallSymbol()
	 */
	public function getSmallSymbol() {
		return StyleManager::getStyle()->getIconPath('entryS.png');
	}

	/**
	 * @see Taggable::getMediumSymbol()
	 */
	public function getMediumSymbol() {
		return StyleManager::getStyle()->getIconPath('entryM.png');
	}
	
	/**
	 * @see Taggable::getLargeSymbol()
	 */
	public function getLargeSymbol() {
		return StyleManager::getStyle()->getIconPath('entryL.png');
	}
}
?>