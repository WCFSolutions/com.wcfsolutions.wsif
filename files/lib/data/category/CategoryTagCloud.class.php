<?php
// wcf imports
require_once(WCF_DIR.'lib/data/tag/TagCloud.class.php');

/**
 * Represents a category tag cloud.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	data.category
 * @category	Infinite Filebase
 */
class CategoryTagCloud extends TagCloud {
	/**
	 * category id
	 *
	 * @var	integer
	 */
	public $categoryID = 0;

	/**
	 * Creates a new CategoryTagCloud object.
	 *
	 * @param	integer		$categoryID
	 * @param	array<integer>	$languageIDArray
	 */
	public function __construct($categoryID, $languageIDArray = array()) {
		$this->categoryID = $categoryID;
		$this->languageIDArray = $languageIDArray;
		if (!count($this->languageIDArray)) $this->languageIDArray = array(0);

		// init cache
		$this->cacheName = 'tagCloud-'.$this->categoryID.'-'.implode(',', $this->languageIDArray);
		$this->loadCache();
	}

	/**
	 * Loads the tag cloud cache.
	 */
	public function loadCache() {
		if ($this->tags !== null) return;

		// get cache
		WCF::getCache()->addResource($this->cacheName, WSIF_DIR.'cache/cache.tagCloud-'.$this->categoryID.'-'.StringUtil::getHash(implode(',', $this->languageIDArray)).'.php', WSIF_DIR.'lib/system/cache/CacheBuilderCategoryTagCloud.class.php', 0, 86400);
		$this->tags = WCF::getCache()->get($this->cacheName);
	}
}
?>