<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/entry/prefix/EntryPrefixEditor.class.php');

// wcf imports
require_once(WCF_DIR.'lib/action/AbstractAction.class.php');

/**
 * Deletes an entry prefix.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	acp.action
 * @category	Infinite Filebase
 */
class EntryPrefixDeleteAction extends AbstractAction {
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
	public $prefix;
	
	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get prefix
		if (isset($_REQUEST['prefixID'])) $this->prefixID = intval($_REQUEST['prefixID']);
		$this->prefix = new EntryPrefixEditor($this->prefixID);
		if (!$this->prefix->prefixID) {
			throw new IllegalLinkException();
		}
	}
	
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();
		
		// check permission
		WCF::getUser()->checkPermission('admin.filebase.canDeleteEntryPrefix');
		
		// delete prefix
		$this->prefix->delete();
		
		// reset cache
		WCF::getCache()->clearResource('entryPrefix');
		WCF::getCache()->clearResource('categoryData');
		$this->executed();
		
		// forward to list page
		HeaderUtil::redirect('index.php?page=EntryPrefixList&deletedPrefixID='.$this->prefixID.'&packageID='.PACKAGE_ID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
}
?>