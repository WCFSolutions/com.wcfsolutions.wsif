<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/category/CategoryEditor.class.php');
require_once(WSIF_DIR.'lib/data/entry/EntryEditor.class.php');
require_once(WSIF_DIR.'lib/data/entry/file/EntryFileEditor.class.php');

// wcf imports
require_once(WCF_DIR.'lib/action/AbstractSecureAction.class.php');

/**
 * Represents an abstract entry file action.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	action
 * @category	Infinite Filebase
 */
abstract class AbstractEntryFileAction extends AbstractSecureAction {	
	/**
	 * file id
	 *
	 * @var	integer
	 */
	public $fileID = 0;
	
	/**
	 * file object
	 * 
	 * @var	EntryFile
	 */
	public $file = null;
	
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
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get file
		if (isset($_REQUEST['fileID'])) $this->fileID = intval($_REQUEST['fileID']);
		$this->file = new EntryFileEditor($this->fileID);
		if (!$this->file->fileID) {
			throw new IllegalLinkException();
		}
		
		// get entry
		$this->entry = new EntryEditor($this->file->entryID);
		
		// get category
		$this->category = new CategoryEditor($this->entry->categoryID);
		$this->entry->enter($this->category);
	}
}
?>