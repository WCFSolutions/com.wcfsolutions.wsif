<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/category/Category.class.php');
require_once(WSIF_DIR.'lib/data/entry/prefix/EntryPrefixEditor.class.php');

// wcf imports
require_once(WCF_DIR.'lib/acp/form/ACPForm.class.php');

/**
 * Shows the entry prefix add form.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	acp.form
 * @category	Infinite Filebase
 */
class EntryPrefixAddForm extends ACPForm {
	// system
	public $templateName = 'entryPrefixAdd';
	public $activeMenuItem = 'wsif.acp.menu.link.content.entry.prefix.add';
	public $neededPermissions = 'admin.filebase.canAddEntryPrefix';
	
	// form parameters
	public $prefixName = '';
	public $prefixMarking = '%s';
	public $prefixType = 0;
	public $showOrder = 0;
	public $categoryIDs = array();
	
	/**
	 * list of available categories
	 * 
	 * @var	array
	 */
	public $categoryOptions = array();
	
	/**
	 * prefix editor object
	 * 
	 * @var	EntryPrefixEditor
	 */
	public $newPrefix = null;

	/**
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_POST['prefixType'])) $this->prefixType = intval($_POST['prefixType']);
		if (isset($_POST['prefixName'])) $this->prefixName = StringUtil::trim($_POST['prefixName']);
		if (isset($_POST['prefixMarking'])) $this->prefixMarking = StringUtil::trim($_POST['prefixMarking']);
		if (isset($_POST['showOrder'])) $this->showOrder = intval($_POST['showOrder']);
		if (isset($_POST['categoryIDs'])) $this->categoryIDs = ArrayUtil::toIntegerArray($_POST['categoryIDs']);
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		$this->categoryOptions = Category::getCategorySelect(array(), true);
	}
	
	/**
	 * @see Form::validate()
	 */
	public function validate() {
		parent::validate();
		
		// type
		if ($this->prefixType < 0 || $this->prefixType > 1) {
			throw new UserInputException('prefixType', 'invalid');
		}
		
		// assigned categories
		if ($this->prefixType == 1) {
			if (!count($this->categoryIDs)) {
				throw new UserInputException('assignedCategories');
			}
		}
		
		// name
		if (empty($this->prefixName)) {
			throw new UserInputException('prefixName');
		}
		
		// marking
		if (empty($this->prefixMarking)) {
			throw new UserInputException('prefixMarking');
		}
	}
	
	/**
	 * @see Form::save()
	 */
	public function save() {
		parent::save();
		
		// save
		$this->newPrefix = EntryPrefixEditor::create($this->prefixName, $this->prefixMarking, $this->prefixType, $this->showOrder, WCF::getLanguage()->getLanguageID());
		if ($this->prefixType == 1) {
			$this->newPrefix->assignCategories($this->categoryIDs);
		}
		
		// reset cache
		WCF::getCache()->clearResource('entryPrefix');
		$this->saved();
		
		// reset values
		$this->prefixName = '';
		$this->prefixMarking = '%s';
		$this->prefixType = $this->showOrder = 0;
		$this->categoryIDs = array();
		
		// show success message
		WCF::getTPL()->assign('success', true);
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'action' => 'add',
			'prefixType' => $this->prefixType,
			'prefixName' => $this->prefixName,
			'prefixMarking' => $this->prefixMarking,
			'showOrder' => $this->showOrder,
			'categoryOptions' => $this->categoryOptions,
			'categoryIDs' => $this->categoryIDs
		));
	}
}
?>