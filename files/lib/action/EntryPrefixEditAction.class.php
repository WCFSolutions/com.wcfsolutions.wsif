<?php
// wsif imports
require_once(WSIF_DIR.'lib/action/AbstractEntryAction.class.php');
require_once(WSIF_DIR.'lib/data/entry/prefix/EntryPrefixEditor.class.php');

/**
 * Edits the prefix of an entry.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	action
 * @category	Infinite Filebase
 */
class EntryPrefixEditAction extends AbstractEntryAction {
	/**
	 * prefix id
	 *
	 * @var	integer
	 */
	public $prefixID = 0;
	
	/**
	 * prefix editor object
	 *
	 * @var	EntryPrefixEditor
	 */
	public $prefix = null;
	
	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get prefix
		if (isset($_REQUEST['prefixID'])) $this->prefixID = intval($_REQUEST['prefixID']);
		if ($this->prefixID != 0) {
			$this->prefix = new EntryPrefixEditor($this->prefixID);
			if (!$this->prefix->prefixID) {
				throw new IllegalLinkException();
			}
		}
	}

	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();
		
		// check permission
		$this->category->checkModeratorPermission('canEditEntry');

		// edit prefix
		$this->entry->setPrefixID($this->prefixID);
			
		// reset cache
		if ($this->entry->time == $this->category->getLastEntryTime($this->entry->languageID)) {
			WCF::getCache()->clearResource('categoryData', true);
		}
		$this->executed();
	}
}
?>