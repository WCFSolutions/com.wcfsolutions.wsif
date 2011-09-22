<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/entry/ViewableEntry.class.php');
require_once(WSIF_DIR.'lib/page/util/menu/EntryMenu.class.php');

// wcf imports
require_once(WCF_DIR.'lib/system/event/EventHandler.class.php');
require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');

/**
 * Manages the entry pages.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	data.entry
 * @category	Infinite Filebase
 */
class EntryFrame {
	// system
	public $enableRating = ENTRY_ENABLE_RATING;

	/**
	 * entry container
	 * 
	 * @var	object 
	 */
	public $container = null;
	
	/**
	 * entry id
	 *
	 * @var integer
	 */
	public $entryID = 0;
	
	/**
	 * entry object
	 *
	 * @var ViewableEntry
	 */
	public $entry = null;
	
	/**
	 * category object
	 *
	 * @var Category
	 */
	public $category = null;

	/**
	 * number of marked entries
	 *
	 * @var	integer
	 */
	public $markedEntries = 0;
	
	/**
	 * Creates a new EntryFrame.
	 *
	 * @param	object		$container
	 * @param	integer		$entryID
	 */
	public function __construct($container = null, $entryID = null) {
		$this->container = $container;

		// get entry id
		if ($entryID !== null) {
			$this->entryID = $entryID;
		}
		else if (!empty($_REQUEST['entryID'])) {
			$this->entryID = intval($_REQUEST['entryID']);
		}
		
		// init frame
		$this->init();
	}
	
	/**
	 * Initializes the entry frame.
	 */
	public function init() {
		// call init event
		EventHandler::fireAction($this, 'init');
		
		// get entry
		$this->entry = new ViewableEntry($this->entryID);
		if (!$this->entry->entryID) {
			throw new IllegalLinkException();
		}
		
		// get category
		$this->category = Category::getCategory($this->entry->categoryID);
		$this->entry->enter($this->category);
		if ($this->category->enableRating != -1) $this->enableRating = $this->category->enableRating;
		
		// get marked entries
		$sessionVars = WCF::getSession()->getVars();
		if (isset($sessionVars['markedEntries'])) {
			$this->markedEntries = count($sessionVars['markedEntries']);
		}
		
		// set active entry menu entry
		EntryMenu::getInstance()->entryID = $this->entryID;
	}
	
	/**
	 * Returns the entry id.
	 * 
	 * @return 	integer
	 */
	public function getEntryID() {
		return $this->entryID;
	}
	
	/**
	 * Returns the entry object.
	 * 
	 * @return 	ViewableEntry
	 */
	public function getEntry() {
		return $this->entry;
	}
	
	/**
	 * Returns the category object.
	 * 
	 * @return 	Category
	 */
	public function getCategory() {
		return $this->category;
	}
	
	/**
	 * Assigns variables to the template engine.
	 */
	public function assignVariables() {
		// call assignVariables event
		EventHandler::fireAction($this, 'assignVariables');
		
		// remove image tab
		if (!$this->entry->images && (!WCF::getUser()->userID || WCF::getUser()->userID != $this->entry->userID) && !$this->entry->isEditable($this->category)) {
			foreach (EntryMenu::getInstance()->menuItems as $parentMenuItem => $items) {
				foreach ($items as $key => $item) {
					if ($item['menuItem'] == 'wsif.entry.menu.link.entryImages') {
						unset(EntryMenu::getInstance()->menuItems[$parentMenuItem][$key]);
					}
				}
			}
		}
		
		// assign variables
		WCF::getTPL()->assign(array(
			'url' => 'index.php?page=Entry&entryID='.$this->entry->entryID.SID_ARG_2ND_NOT_ENCODED,
			'entry' => $this->entry,
			'entryID' => $this->entryID,
			'category' => $this->category,
			'enableRating' => $this->enableRating,
			'permissions' => $this->category->getModeratorPermissions(),
			'markedEntries' => $this->markedEntries,
			'categoryQuickJumpOptions' => Category::getCategorySelect()
		));
	}
}
?>