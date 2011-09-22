<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/entry/prefix/EntryPrefixEditor.class.php');

// wcf imports
require_once(WCF_DIR.'lib/action/AbstractAction.class.php');

/**
 * Provides default implementations for entry prefix actions.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	acp.action
 * @category	Infinite Filebase
 */
abstract class AbstractEntryPrefixAction extends AbstractAction {
	/**
	 * entry prefix id
	 * 
	 * @var	integer
	 */
	public $prefixID = 0;
	
	/**
	 * entry prefix editor object
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
		$this->prefix = new EntryPrefixEditor($this->prefixID);
		if (!$this->prefix->prefixID) {
			throw new IllegalLinkException();
		}
	}
}
?>