<?php
// wsif imports
require_once(WSIF_DIR.'lib/action/AbstractEntryImageAction.class.php');

/**
 * Sets an entry image as default.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	action
 * @category	Infinite Filebase
 */
class EntryImageSetAsDefaultAction extends AbstractEntryImageAction {
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();
		
		// check permission
		if (!$this->entry->isEditable($this->category) || !$this->image->isEditable($this->category)) {
			throw new PermissionDeniedException();
		}
		
		// set image as default
		$this->image->setAsDefault();
		$this->executed();
		
		// forward to page
		HeaderUtil::redirect('index.php?page=EntryImage&imageID='.$this->image->imageID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
}
?>