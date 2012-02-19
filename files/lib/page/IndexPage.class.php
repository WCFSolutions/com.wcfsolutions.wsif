<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/category/CategoryList.class.php');

// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');

/**
 * Shows the index page of the filebase.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	page
 * @category	Infinite Filebase
 */
class IndexPage extends AbstractPage {
	public $templateName = 'index';
	public $tags = array();
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		// read categories
		$this->categoryList = new CategoryList();
		$this->categoryList->readCategories();
		
		// read tags
		if (MODULE_TAGGING && ENTRY_ENABLE_TAGS && INDEX_ENABLE_TAGS) {
			$this->readTags();
		}
	}
	
	/**
	 * @see Page::assignVariables();
	 */
	public function assignVariables() {
		parent::assignVariables();

		// stats
		if (INDEX_ENABLE_STATS) {
			$this->renderStats();
		}
		
		$this->categoryList->assignVariables();
		WCF::getTPL()->assign(array(
			'allowSpidersToIndexThisPage' => true,
			'tags' => $this->tags
		));
	}
	
	/**
	 * Renders the filebase stats.
	 */
	protected function renderStats() {
		$stats = WCF::getCache()->get('stat');
		WCF::getTPL()->assign('stats', $stats);
	}
	
	/**
	 * Reads the tags.
	 */
	protected function readTags() {
		// include files
		require_once(WCF_DIR.'lib/data/tag/TagCloud.class.php');
		
		// get tags
		$tagCloud = new TagCloud(WCF::getSession()->getVisibleLanguageIDArray());
		$this->tags = $tagCloud->getTags();
	}
}
?>