<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/category/Category.class.php');
require_once(WSIF_DIR.'lib/data/entry/FeedEntryList.class.php');

// wcf imports
require_once(WCF_DIR.'lib/page/AbstractFeedPage.class.php');

/**
 * Prints a list of entries as a rss or an atom feed.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	page
 * @category	Infinite Filebase
 */
class EntriesFeedPage extends AbstractFeedPage {
	/**
	 * list of category ids
	 * 
	 * @var	array<integer>
	 */
	public $categoryIDArray = array();
	
	/**
	 * list of entries
	 *
	 * @var FeedEntryList
	 */
	public $entryList = null;
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get category ids
		if (isset($_REQUEST['categoryID'])) $this->categoryIDArray = ArrayUtil::toIntegerArray(explode(',', $_REQUEST['categoryID']));
		$categoryIDArray = $this->categoryIDArray;
		
		// include sub categories
		if (count($categoryIDArray)) {
			$categoryIDArray = array_merge($categoryIDArray, Category::getSubCategoryIDArray($categoryIDArray));
		}
		
		// get accessible categories
		$accessibleCategoryIDArray = Category::getAccessibleCategoryIDArray(array('canViewCategory', 'canEnterCategory', 'canViewEntry'));
		if (count($categoryIDArray)) {
			$categoryIDArray = array_intersect($categoryIDArray, $accessibleCategoryIDArray);
		}
		else {
			$categoryIDArray = $accessibleCategoryIDArray;
		}

		// get feed entry list
		$this->entryList = new FeedEntryList();
		$this->entryList->sqlConditions .= 'entry.categoryID IN ('.implode(',', $categoryIDArray).')';
		$this->entryList->sqlConditions .= ' AND entry.time > '.($this->hours ? (TIME_NOW - $this->hours * 3600) : (TIME_NOW - 30 * 86400));
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		// read entries
		$this->entryList->sqlLimit = $this->limit;
		$this->entryList->readObjects();
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'entries' => $this->entryList->getObjects()
		));
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {		
		parent::show();
		
		// send content
		WCF::getTPL()->display(($this->format == 'atom' ? 'feedAtomEntries' : 'feedRss2Entries'), false);
	}
}
?>