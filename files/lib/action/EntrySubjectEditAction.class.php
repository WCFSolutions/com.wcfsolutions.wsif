<?php
// wsif imports
require_once(WSIF_DIR.'lib/action/AbstractEntryAction.class.php');

/**
 * Edits the subject of an entry.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	action
 * @category	Infinite Filebase
 */
class EntrySubjectEditAction extends AbstractEntryAction {
	/**
	 * new subject
	 *
	 * @var	string
	 */
	public $subject = '';

	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		// get subject
		if (isset($_REQUEST['subject'])) {
			$this->subject = StringUtil::trim($_REQUEST['subject']);
			if (CHARSET != 'UTF-8') $this->subject = StringUtil::convertEncoding('UTF-8', CHARSET, $this->subject);
		}
	}

	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();

		// check permission
		$this->category->checkModeratorPermission('canEditEntry');

		// edit subject
		$this->entry->setSubject($this->subject);

		// reset cache
		if ($this->entry->time == $this->category->getLastEntryTime($this->entry->languageID)) {
			WCF::getCache()->clearResource('categoryData', true);
		}
		$this->executed();
	}
}
?>