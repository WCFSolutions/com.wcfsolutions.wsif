<?php
// wsif imports
require_once(WSIF_DIR.'lib/action/AbstractEntryImageAction.class.php');

/**
 * Deletes an entry image.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	action
 * @category	Infinite Filebase
 */
class EntryImageDeleteAction extends AbstractEntryImageAction {
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();
		
		// check permission
		if (!$this->image->isDeletable($this->entry, $this->category)) {
			throw new PermissionDeniedException();
		}
		
		// delete image
		$this->image->delete();
		
		// update file count
		$this->entry->updateImages(-1);
		$this->category->updateEntryImages(-1);
		$this->executed();
		
		// forward to page
		HeaderUtil::redirect('index.php?page=EntryImages&entryID='.$this->image->entryID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
}
?>