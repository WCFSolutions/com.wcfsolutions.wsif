<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/category/CategoryEditor.class.php');

// wcf imports
require_once(WCF_DIR.'lib/system/counterUpdate/type/AbstractCounterUpdateType.class.php');

/**
 * Updates the category counters.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	system.counterUpdate.type
 * @category	Infinite Filebase
 */
class CategoryCounterUpdateType extends AbstractCounterUpdateType {
	/**
	 * @see	CounterUpdateType::getDefaultLimit()
	 */
	public function getDefaultLimit() {
		return 50;
	}
	
	/**
	 * @see	CounterUpdateType::countItems()
	 */
	public function countItems() {
		$sql = "SELECT	COUNT(*) AS count
			FROM	wsif".WSIF_N."_category";
		$row = WCF::getDB()->getFirstRow($sql);
		return $row['count'];
	}
	
	/**
	 * @see	CounterUpdateType::update()
	 */
	public function update($offset, $limit) {
		parent::update($offset, $limit);
		
		// get category ids
		$categoryIDs = '';
		$sql = "SELECT		categoryID
			FROM		wsif".WSIF_N."_category
			ORDER BY	categoryID";
		$result = WCF::getDB()->sendQuery($sql, $limit, $offset);
		while ($row = WCF::getDB()->fetchArray($result)) {
			if (!empty($categoryIDs)) $categoryIDs .= ',';
			$categoryIDs .= $row['categoryID'];
			
			// update last entry
			$category = new CategoryEditor($row['categoryID']);
			$category->setLastEntries();
		}
		if (empty($categoryIDs)) {
			// reset cache
			WCF::getCache()->clear(WSIF_DIR.'cache', 'cache.categoryData.php');
			$this->finished = true;
			return;
		}
		
		// refresh categories
		CategoryEditor::refreshAll($categoryIDs);
		$this->updated();
	}
}
?>