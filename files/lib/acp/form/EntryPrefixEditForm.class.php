<?php
// wcf imports
require_once(WSIF_DIR.'lib/acp/form/EntryPrefixAddForm.class.php');

/**
 * Shows the entry prefix edit form.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	acp.form
 * @category	Infinite Filebase
 */
class EntryPrefixEditForm extends EntryPrefixAddForm {
	// system
	public $activeMenuItem = 'wsif.acp.menu.link.content.entry.prefix.view';
	public $neededPermissions = 'admin.filebase.canEditEntryPrefix';
	
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

	public $languageID = 0;	
	public $languages = array();
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get language id
		if (isset($_REQUEST['languageID'])) $this->languageID = intval($_REQUEST['languageID']);
		else $this->languageID = WCF::getLanguage()->getLanguageID();
		
		// get prefix
		if (isset($_REQUEST['prefixID'])) $this->prefixID = intval($_REQUEST['prefixID']);
		$this->prefix = new EntryPrefixEditor($this->prefixID);
		if (!$this->prefix->prefixID) {
			throw new IllegalLinkException();
		}
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		// get all available languages
		$this->languages = Language::getLanguageCodes();
		
		// default values
		if (!count($_POST)) {
			$this->prefixType = $this->prefix->prefixType;
			$this->categoryIDs = $this->prefix->getAssignedCategories();
			$this->prefixMarking = $this->prefix->prefixMarking;
			$this->showOrder = $this->prefix->showOrder;
			
			// get name
			if (WCF::getLanguage()->getLanguageID() != $this->languageID) $language = new Language($this->languageID);
			else $language = WCF::getLanguage();
			$this->prefixName = $language->get('wsif.entry.prefix.'.$this->prefix->prefix);
			if ($this->prefixName == 'wsif.entry.prefix.'.$this->prefix->prefix) $this->prefixName = '';
		}
	}
	
	/**
	 * @see Form::save()
	 */
	public function save() {
		AbstractForm::save();
		
		// update
		$this->prefix->update($this->prefixName, $this->prefixMarking, $this->prefixType, $this->showOrder, $this->languageID);
		if ($this->prefix->prefixType == 1) {
			$this->prefix->removeAssignedCategories();
		}
		if ($this->prefixType == 1) {
			$this->prefix->assignCategories($this->categoryIDs);
		}

		// reset cache
		WCF::getCache()->clearResource('entryPrefix');
		WCF::getCache()->clearResource('categoryData');
		$this->saved();
		
		// show success message
		WCF::getTPL()->assign('success', true);
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'action' => 'edit',
			'prefixID' => $this->prefixID,
			'prefix' => $this->prefix,
			'languageID' => $this->languageID,
			'languages' => $this->languages
		));
	}
}
?>