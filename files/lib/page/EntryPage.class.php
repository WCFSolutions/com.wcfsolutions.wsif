<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/category/Category.class.php');
require_once(WSIF_DIR.'lib/data/entry/EntryFrame.class.php');
require_once(WSIF_DIR.'lib/data/user/WSIFUser.class.php');

// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');

/**
 * Shows the entry page.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	page
 * @category	Infinite Filebase
 */
class EntryPage extends AbstractPage {
	// system
	public $templateName = 'entry';	
	
	/**
	 * entry frame object
	 * 
	 * @var EntryFrame
	 */
	public $frame = null;
	
	/**
	 * list of entry tags
	 * 
	 * @var array
	 */
	public $tags = array();
	
	/**
	 * list of entry visitors
	 *
	 * @var array<WSIFUser>
	 */
	public $entryVisitors = array();

	/**
	 * @see Page::readParameters()
	 */	
	public function readParameters() {
		parent::readParameters();
		
		// get entry frame
		$this->frame = new EntryFrame($this);

		// handle rating
		$this->handleRating();

		// update views
		if (!WCF::getSession()->spiderID) {
			$this->updateViews();
		}
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		// get tags
		$this->tags = $this->frame->getEntry()->getTags(WCF::getSession()->getVisibleLanguageIDArray());
		
		// get entry visitors
		$sql = "SELECT		avatar.*, user_table.*, visitor.*
			FROM		wsif".WSIF_N."_entry_visitor visitor
			LEFT JOIN 	wcf".WCF_N."_user user_table
			ON 		(user_table.userID = visitor.userID)
			LEFT JOIN 	wcf".WCF_N."_avatar avatar
			ON 		(avatar.avatarID = user_table.avatarID)
			WHERE		visitor.entryID = ".$this->frame->getEntryID()."
			ORDER BY	time DESC";
		$result = WCF::getDB()->sendQuery($sql, 5);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$this->entryVisitors[] = new WSIFUser(null, $row);
		}
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		// set active menu item
		require_once(WSIF_DIR.'lib/page/util/menu/EntryMenu.class.php');
		EntryMenu::getInstance()->setActiveMenuItem('wsif.entry.menu.link.entry');

		// show page
		parent::show();
	}
	
	/**
	* @see Page::assignVariables();
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		$this->frame->assignVariables();
		WCF::getTPL()->assign(array(
			'tags' => $this->tags,
			'entryVisitors' => $this->entryVisitors,
			'allowSpidersToIndexThisPage' => true
		));
	}
	
	/**
	 * Handles a rating request.
	 */
	public function handleRating() {
		if (!isset($_POST['rating'])) return;
		
		// get rating
		$rating = intval($_POST['rating']);
			
		// rating is disabled
		if (!$this->frame->enableRating) {
			throw new IllegalLinkException();
		}
			
		// user cannot rate this entry again
		if ($this->frame->getEntry()->userRating !== null && !$this->frame->getEntry()->userRating) {
			throw new PermissionDeniedException();
		}
			
		// user cannot rate this entry
		if (!$this->frame->getEntry()->isRatable($this->frame->getCategory())) {
			throw new PermissionDeniedException();
		}
			
		// rating is not valid
		if ($rating < 1 || $rating > 5) {
			throw new IllegalLinkException();
		}
			
		// change rating
		if ($this->frame->getEntry()->userRating) {
			$sql = "UPDATE 	wsif".WSIF_N."_entry_rating
				SET 	rating = ".$rating."
				WHERE 	entryID = ".$this->frame->getEntryID()."
					AND ".(WCF::getUser()->userID ? "userID = ".WCF::getUser()->userID : "ipAddress = '".escapeString(WCF::getSession()->ipAddress)."'");
			WCF::getDB()->sendQuery($sql);
				
			$sql = "UPDATE 	wsif".WSIF_N."_entry
				SET 	rating = rating + ".$rating." - ".$this->frame->getEntry()->userRating."
				WHERE 	entryID = ".$this->frame->getEntryID();
			WCF::getDB()->sendQuery($sql);
		}
		// insert new rating
		else {
			$sql = "INSERT INTO	wsif".WSIF_N."_entry_rating
						(entryID, rating, userID, ipAddress)
				VALUES		(".$this->frame->getEntryID().",
						".$rating.",
						".WCF::getUser()->userID.",
						'".escapeString(WCF::getSession()->ipAddress)."')";
			WCF::getDB()->sendQuery($sql);	
				
			$sql = "UPDATE 	wsif".WSIF_N."_entry
				SET 	ratings = ratings + 1,
					rating = rating + ".$rating."
				WHERE 	entryID = ".$this->frame->getEntryID();
			WCF::getDB()->sendQuery($sql);	
		}
		
		// forward to entry page
		HeaderUtil::redirect('index.php?page=Entry&entryID='.$this->frame->getEntryID().SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
	
	/**
	 * Updates the views of this entry.
	 */
	public function updateViews() {
		// update entry views
		$sql = "UPDATE	wsif".WSIF_N."_entry
			SET	views = views + 1
			WHERE	entryID = ".$this->frame->getEntryID();
		WCF::getDB()->registerShutdownUpdate($sql);
			
		// save visitor
		if (WCF::getUser()->userID && !WCF::getUser()->invisible) {
			$sql = "INSERT INTO			wsif".WSIF_N."_entry_visitor
								(entryID, userID, time)
				VALUES				(".$this->frame->getEntryID().", ".WCF::getUser()->userID.", ".TIME_NOW.")
				ON DUPLICATE KEY UPDATE		time = VALUES(time)";
			WCF::getDB()->registerShutdownUpdate($sql);
		}
	}
}
?>