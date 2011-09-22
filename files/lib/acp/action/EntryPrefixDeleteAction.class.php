<?php
// wsif imports
require_once(WSIF_DIR.'lib/acp/action/AbstractEntryPrefixAction.class.php');

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
class EntryPrefixDeleteAction extends AbstractEntryPrefixAction {
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