<?php
// wcf imports
require_once(WCF_DIR.'lib/system/cache/CacheBuilderTagCloud.class.php');

/**
 * Caches a category tag cloud.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	system.cache
 * @category	Infinite Filebase
 */
class CacheBuilderCategoryTagCloud extends CacheBuilderTagCloud {
	/**
	 * @see CacheBuilder::getData()
	 */
	public function getData($cacheResource) {
		list($cache, $categoryID, $languageIDs) = explode('-', $cacheResource['cache']);
		$data = array();

		// get taggable
		require_once(WCF_DIR.'lib/data/tag/TagEngine.class.php');
		$taggable = TagEngine::getInstance()->getTaggable('com.wcfsolutions.wsif.entry');

		// get tag ids
		$tagIDArray = array();
		$sql = "SELECT		COUNT(*) AS counter, object.tagID
			FROM 		wsif".WSIF_N."_entry entry,
					wcf".WCF_N."_tag_to_object object
			WHERE 		entry.categoryID = ".$categoryID."
					AND object.taggableID = ".$taggable->getTaggableID()."
					AND object.languageID IN (".$languageIDs.")
					AND object.objectID = entry.entryID
			GROUP BY 	object.tagID
			ORDER BY 	counter DESC";
		$result = WCF::getDB()->sendQuery($sql, 500);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$tagIDArray[$row['tagID']] = $row['counter'];
		}

		// get tags
		if (count($tagIDArray)) {
			$sql = "SELECT		name, tagID
				FROM		wcf".WCF_N."_tag
				WHERE		tagID IN (".implode(',', array_keys($tagIDArray)).")";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				$row['counter'] = $tagIDArray[$row['tagID']];
				$this->tags[StringUtil::toLowerCase($row['name'])] = new Tag(null, $row);
			}

			// sort by counter
			uasort($this->tags, array('self', 'compareTags'));

			$data = $this->tags;
		}

		return $data;
	}
}
?>