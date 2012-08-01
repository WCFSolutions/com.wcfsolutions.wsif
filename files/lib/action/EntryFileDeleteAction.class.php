<?php
// wsif imports
require_once(WSIF_DIR.'lib/action/AbstractEntryFileAction.class.php');

/**
 * Deletes an entry file.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	action
 * @category	Infinite Filebase
 */
class EntryFileDeleteAction extends AbstractEntryFileAction {
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();

		// check permission
		if (!$this->file->isDeletable($this->entry, $this->category)) {
			throw new PermissionDeniedException();
		}

		// delete file
		$this->file->delete();

		// update file count
		$this->entry->updateFiles(-1);
		$this->category->updateEntryFiles(-1);
		$this->executed();

		// forward to page
		HeaderUtil::redirect('index.php?page=EntryFiles&entryID='.$this->file->entryID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
}
?>