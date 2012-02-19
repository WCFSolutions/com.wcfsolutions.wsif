<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/category/Category.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/page/location/Location.class.php');

/**
 * CategoryLocation is an implementation of Location for the category page.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	data.page.location
 * @category	Infinite Filebase
 */
class CategoryLocation implements Location {
	public $categories = null;
	
	/**
	 * @see Location::cache()
	 */
	public function cache($location, $requestURI, $requestMethod, $match) {}
	
	/**
	 * @see Location::get()
	 */
	public function get($location, $requestURI, $requestMethod, $match) {
		if ($this->categories === null) {
			$this->readCategories();
		}
		
		$categoryID = $match[1];
		if (!isset($this->categories[$categoryID]) || !$this->categories[$categoryID]->getPermission()) {
			return '';
		}
		
		return WCF::getLanguage()->get($location['locationName'], array('$category' => '<a href="index.php?page=Category&amp;categoryID='.$this->categories[$categoryID]->categoryID.SID_ARG_2ND.'">'.$this->categories[$categoryID]->getTitle().'</a>'));
	}
	
	/**
	 * Gets categories from cache.
	 */
	protected function readCategories() {
		$this->categories = WCF::getCache()->get('category', 'categories');
	}
}
?>