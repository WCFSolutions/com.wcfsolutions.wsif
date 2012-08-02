<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/category/Category.class.php');

// wcf imports
require_once(WCF_DIR.'lib/page/SortablePage.class.php');

/**
 * Shows the category page.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	page
 * @category	Infinite Filebase
 */
class CategoryPage extends SortablePage {
	// system
	public $templateName = 'category';
	public $defaultSortField = CATEGORY_ENTRIES_DEFAULT_SORT_FIELD;
	public $defaultSortOrder = CATEGORY_ENTRIES_DEFAULT_SORT_ORDER;
	public $defaultDaysPrune = CATEGORY_ENTRIES_DEFAULT_DAYS_PRUNE;
	public $itemsPerPage = CATEGORY_ENTRIES_PER_PAGE;
	public $enableRating = ENTRY_ENABLE_RATING;

	/**
	 * category id
	 *
	 * @var integer
	 */
	public $categoryID = 0;

	/**
	 * category object
	 *
	 * @var Category
	 */
	public $category = null;

	/**
	 * list of categories
	 *
	 * @var CategoryList
	 */
	public $categoryList = null;

	/**
	 * list of entries
	 *
	 * @var EntryList
	 */
	public $entryList = null;

	/**
	 * list of tags
	 *
	 * @var	array
	 */
	public $tags = array();

	/**
	 * number of marked entries
	 *
	 * @var	integer
	 */
	public $markedEntries = 0;

	/**
	 * prefix id
	 *
	 * @var integer
	 */
	public $prefixID = -1;

	/**
	 * language id
	 *
	 * @var integer
	 */
	public $languageID = 0;

	/**
	 * days prune
	 *
	 * @var integer
	 */
	public $daysPrune = 1000;

	/**
	 * tag id
	 *
	 * @var integer
	 */
	public $tagID = 0;

	/**
	 * tag object
	 *
	 * @var Tag
	 */
	public $tag = null;

	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		if (isset($_REQUEST['categoryID'])) $this->categoryID = intval($_REQUEST['categoryID']);
		if (isset($_REQUEST['prefixID'])) $this->prefixID = intval($_REQUEST['prefixID']);
		if (isset($_REQUEST['languageID'])) $this->languageID = intval($_REQUEST['languageID']);
		if (isset($_REQUEST['tagID'])) $this->tagID = intval($_REQUEST['tagID']);

		// get category
		$this->category = new Category($this->categoryID);
		$this->category->enter();

		// redirect to external url
		if ($this->category->isExternalLink()) {
			if (!WCF::getSession()->spiderID) {
				// update redirect counter
				$sql = "UPDATE	wsif".WSIF_N."_category
					SET	clicks = clicks + 1
					WHERE	categoryID = ".$this->categoryID;
				WCF::getDB()->registerShutdownUpdate($sql);

				// reset cache
				WCF::getCache()->clearResource('categoryData');
			}

			// redirect to external url
			HeaderUtil::redirect($this->category->externalURL, false);
			exit;
		}

		// entries per page
		if ($this->category->entriesPerPage) $this->itemsPerPage = $this->category->entriesPerPage;
		if (WCF::getUser()->entriesPerPage) $this->itemsPerPage = WCF::getUser()->entriesPerPage;

		// get sorting values
		if ($this->category->entrySortField) $this->defaultSortField = $this->category->entrySortField;
		if ($this->category->entrySortOrder) $this->defaultSortOrder = $this->category->entrySortOrder;
		if ($this->category->entryDaysPrune) $this->defaultDaysPrune = $this->category->entryDaysPrune;
		if (WCF::getUser()->entryDaysPrune) $this->defaultDaysPrune = WCF::getUser()->entryDaysPrune;

		// entry rating
		if ($this->category->enableRating != -1) $this->enableRating = $this->category->enableRating;

		// days prune
		if (isset($_REQUEST['daysPrune'])) $this->daysPrune = intval($_REQUEST['daysPrune']);
		switch ($this->daysPrune) {
			case 0: case 1: case 3: case 7: case 14: case 30: case 60: case 100: case 365: case 1000: break;
			default: $this->daysPrune = $this->defaultDaysPrune;
		}

		// get entry list
		if ($this->category->isCategory()) {
			if (MODULE_TAGGING && $this->tagID) {
				require_once(WCF_DIR.'lib/data/tag/TagEngine.class.php');
				$this->tag = TagEngine::getInstance()->getTagByID($this->tagID);
				if ($this->tag === null) {
					throw new IllegalLinkException();
				}
				require_once(WSIF_DIR.'lib/data/entry/TaggedCategoryEntryList.class.php');
				$this->entryList = new TaggedCategoryEntryList($this->tagID, $this->category, $this->daysPrune, $this->prefixID, $this->languageID, $this->enableRating);
			}
			else {
				require_once(WSIF_DIR.'lib/data/entry/CategoryEntryList.class.php');
				$this->entryList = new CategoryEntryList($this->category, $this->daysPrune, $this->prefixID, $this->languageID, $this->enableRating);
			}
		}
	}

	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();

		// get category list
		require_once(WSIF_DIR.'lib/data/category/CategoryList.class.php');
		$this->categoryList = new CategoryList($this->categoryID);
		$this->categoryList->readCategories();

		// get entries
		if ($this->entryList != null) {
			$this->entryList->sqlLimit = $this->itemsPerPage;
			$this->entryList->sqlOffset = ($this->pageNo - 1) * $this->itemsPerPage;
			$this->entryList->sqlOrderBy = 'entry'.$this->sortField." ".$this->sortOrder.
							($this->sortField == 'rating' ? ", entry.ratings ".$this->sortOrder : '');
			$this->entryList->readObjects();
		}

		// get tags
		if (MODULE_TAGGING && ENTRY_ENABLE_TAGS && CATEGORY_ENABLE_TAGS && $this->category->isCategory()) {
			require_once(WSIF_DIR.'lib/data/category/CategoryTagCloud.class.php');
			$tagCloud = new CategoryTagCloud($this->categoryID, WCF::getSession()->getVisibleLanguageIDArray());
			$this->tags = $tagCloud->getTags();
		}

		// get marked entries
		$sessionVars = WCF::getSession()->getVars();
		if (isset($sessionVars['markedEntries'])) {
			$this->markedEntries = count($sessionVars['markedEntries']);
		}
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		$this->categoryList->assignVariables();
		WCF::getTPL()->assign(array(
			'url' => "index.php?page=Category&categoryID=".$this->categoryID."&pageNo=".$this->pageNo."&sortField=".$this->sortField."&sortOrder=".$this->sortOrder."&daysPrune=".$this->daysPrune."&prefixID=".$this->prefixID."&languageID=".$this->languageID.SID_ARG_2ND_NOT_ENCODED,
			'permissions' => $this->category->getModeratorPermissions(),
			'markedEntries' => $this->markedEntries,
			'daysPrune' => $this->daysPrune,
			'category' => $this->category,
			'categoryID' => $this->categoryID,
			'prefixID' => $this->prefixID,
			'categoryQuickJumpOptions' => Category::getCategorySelect(),
			'entries' => ($this->entryList !== null ? $this->entryList->getObjects() : array()),
			'tags' => ($this->entryList !== null ? $this->entryList->getTags() : array()),
			'allowSpidersToIndexThisPage' => true,
			'defaultSortField' => $this->defaultSortField,
			'defaultSortOrder' => $this->defaultSortOrder,
			'defaultDaysPrune' => $this->defaultDaysPrune,
			'languageID' => $this->languageID,
			'contentLanguages' => Language::getContentLanguages(),
			'enableRating' => $this->enableRating,
			'availableTags' => $this->tags,
			'tagID' => $this->tagID,
			'tag' => $this->tag
		));
	}

	/**
	 * @see MultipleLinkPage::countItems()
	 */
	public function countItems() {
		parent::countItems();

		if ($this->entryList === null) return 0;
		return $this->entryList->countObjects();
	}

	/**
	 * @see SortablePage::validateSortField()
	 */
	public function validateSortField() {
		parent::validateSortField();

		switch ($this->sortField) {
			case 'subject':
			case 'username':
			case 'time':
			case 'views':
			case 'downloads': break;
			case 'rating': if ($this->enableRating) break;
			default: $this->sortField = $this->defaultSortField;
		}
	}
}
?>