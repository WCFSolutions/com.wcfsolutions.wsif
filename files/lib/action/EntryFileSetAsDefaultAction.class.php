<?php
// wsif imports
require_once(WSIF_DIR.'lib/action/AbstractEntryFileAction.class.php');

/**
 * Sets an entry file as default.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	action
 * @category	Infinite Filebase
 */
class EntryFileSetAsDefaultAction extends AbstractEntryFileAction {
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();

		// check permission
		if (!$this->entry->isEditable($this->category) || !$this->file->isEditable($this->category)) {
			throw new PermissionDeniedException();
		}

		// set file as default
		$this->file->setAsDefault();
		$this->executed();

		// forward to page
		HeaderUtil::redirect('index.php?page=EntryFile&fileID='.$this->file->fileID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
}
?>