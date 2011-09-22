<?php
// wsif imports
require_once(WSIF_DIR.'lib/acp/action/AbstractEntryPrefixAction.class.php');

/**
 * Renames an entry prefix.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	acp.action
 * @category	Infinite Filebase
 */
class EntryPrefixRenameAction extends AbstractEntryPrefixAction {
	/**
	 * new name
	 * 
	 * @var	string
	 */
	public $title = '';
	
	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get title
		if (isset($_POST['title'])) {
			$this->title = $_POST['title'];
			if (CHARSET != 'UTF-8') $this->title = StringUtil::convertEncoding('UTF-8', CHARSET, $this->title);
		}
	}
	
	/**
	 * @see Action::execute();
	 */
	public function execute() {
		parent::execute();
		
		// check permission
		WCF::getUser()->checkPermission('admin.filebase.canEditEntryPrefix');
		
		// change language variable
		require_once(WCF_DIR.'lib/system/language/LanguageEditor.class.php');
		$language = new LanguageEditor(WCF::getLanguage()->getLanguageID());
		$language->updateItems(array(('wsif.entry.prefix.'.$this->prefix->prefix) => $this->title), 0, PACKAGE_ID, array('wsif.entry.prefix.'.$this->prefix->prefix => 1));
		
		// reset cache
		WCF::getCache()->clearResource('entryPrefix');
		WCF::getCache()->clearResource('categoryData');
		$this->executed();
	}
}
?>