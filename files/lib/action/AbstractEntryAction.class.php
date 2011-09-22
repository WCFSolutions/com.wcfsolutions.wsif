<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/category/CategoryEditor.class.php');
require_once(WSIF_DIR.'lib/data/entry/EntryEditor.class.php');

// wcf imports
require_once(WCF_DIR.'lib/action/AbstractSecureAction.class.php');

/**
 * Represents an abstract entry action.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	action
 * @category	Infinite Filebase
 */
abstract class AbstractEntryAction extends AbstractSecureAction {	
	/**
	 * entry id
	 * 
	 * @var integer
	 */
	public $entryID = 0;
	
	/**
	 * entry editor object
	 * 
	 * @var EntryEditor
	 */
	public $entry = null;
	
	/**
	 * category editor object
	 * 
	 * @var CategoryEditor
	 */
	public $category = null;

	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get entry
		if (isset($_REQUEST['entryID'])) $this->entryID = intval($_REQUEST['entryID']);
		$this->entry = new EntryEditor($this->entryID);
		if (!$this->entry->entryID) {
			throw new IllegalLinkException();
		}
		
		// get category
		$this->category = new CategoryEditor($this->entry->categoryID);
		$this->entry->enter($this->category);
	}
}
?>