<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/category/CategoryEditor.class.php');

// wcf imports
require_once(WCF_DIR.'lib/acp/form/ACPForm.class.php');
require_once(WCF_DIR.'lib/system/style/StyleManager.class.php');

/**
 * Shows the category add form.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	acp.form
 * @category	Infinite Filebase
 */
class CategoryAddForm extends ACPForm {
	// system
	public $templateName = 'categoryAdd';
	public $activeMenuItem = 'wsif.acp.menu.link.content.category.add';
	public $neededPermissions = 'admin.filebase.canAddCategory';
	
	/**
	 * category editor object
	 * 
	 * @var	CategoryEditor
	 */
	public $category = null;
	
	/**
	 * list of available permisions
	 * 
	 * @var	array
	 */
	public $permissionSettings = array();
	
	/**
	 * list of available moderator permisions
	 * 
	 * @var	array
	 */
	public $moderatorSettings = array();
	
	/**
	 * list of available parent categories
	 * 
	 * @var	array
	 */
	public $categoryOptions = array();
	
	/**
	 * list of available styles
	 * 
	 * @var	array
	 */
	public $availableStyles = array();
	
	// parameters
	public $parentID = 0;
	public $position = '';
	public $title = '';
	public $description = '';
	public $allowDescriptionHtml = 0;
	public $categoryType = 0;
	public $icon = '';
	public $externalURL = '';
	public $styleID = 0;
	public $enforceStyle = 0;
	public $daysPrune = 0;
	public $sortField = '';
	public $sortOrder = '';
	public $enableRating = -1;
	public $entriesPerPage = 0;
	public $permissions = array();
	public $moderators = array();
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		if (isset($_REQUEST['parentID'])) $this->parentID = intval($_REQUEST['parentID']);
		
		$this->moderatorSettings = Category::getModeratorSettings();
		$this->permissionSettings = Category::getPermissionSettings();
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		$this->categoryOptions = Category::getCategorySelect(array());
		$this->availableStyles = StyleManager::getAvailableStyles();
	}
	
	/**
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (!empty($_POST['position'])) $this->position = intval($_POST['position']);
		if (isset($_POST['title'])) $this->title = StringUtil::trim($_POST['title']);
		if (isset($_POST['description'])) $this->description = StringUtil::trim($_POST['description']);
		if (isset($_POST['allowDescriptionHtml'])) $this->allowDescriptionHtml = intval($_POST['allowDescriptionHtml']);
		if (isset($_POST['categoryType'])) $this->categoryType = intval($_POST['categoryType']);
		if (isset($_POST['icon'])) $this->icon = StringUtil::trim($_POST['icon']);
		if (isset($_POST['externalURL'])) $this->externalURL = StringUtil::trim($_POST['externalURL']);
		if (isset($_POST['styleID'])) $this->styleID = intval($_POST['styleID']);
		if (isset($_POST['enforceStyle'])) $this->enforceStyle = intval($_POST['enforceStyle']);
		if (isset($_POST['daysPrune'])) $this->daysPrune = intval($_POST['daysPrune']);
		if (isset($_POST['sortField'])) $this->sortField = StringUtil::trim($_POST['sortField']);
		if (isset($_POST['sortOrder'])) $this->sortOrder = StringUtil::trim($_POST['sortOrder']);
		if (isset($_POST['enableRating'])) $this->enableRating = intval($_POST['enableRating']);
		if (isset($_POST['entriesPerPage'])) $this->entriesPerPage = intval($_POST['entriesPerPage']);
		if (isset($_POST['permission']) && is_array($_POST['permission'])) $this->permissions = $_POST['permission'];
		if (isset($_POST['moderator']) && is_array($_POST['moderator'])) $this->moderators = $_POST['moderator'];
	}
	
	/**
	 * @see Form::validate()
	 */
	public function validate() {
		parent::validate();
		
		// category type
		if ($this->categoryType < 0 || $this->categoryType > 2) {
			throw new UserInputException('categoryType', 'invalid');
		}
		
		// parent id
		$this->validateParentID();
		
		// title
		if (empty($this->title)) {
			throw new UserInputException('title');
		}
	
		// external url
		if ($this->categoryType == 2 && empty($this->externalURL)) {
			throw new UserInputException('externalURL');
		}
		
		// sortField
		switch ($this->sortField) {
			case '': case 'subject': case 'username': case 'time': case 'ratingResult': case 'downloads': case 'views': break;
			default: throw new UserInputException('sortField', 'invalid');
		}
		
		// sortOrder
		switch ($this->sortOrder) {
			case '': case 'ASC': case 'DESC': break;
			default: throw new UserInputException('sortOrder', 'invalid');
		}
		
		// daysPrune
		switch ($this->daysPrune) {
			case 0: case 1: case 3: case 7: case 14: case 30: case 60: case 100: case 365: case 1000: break;
			default: throw new UserInputException('daysPrune', 'invalid');
		}
		
		// permissions
		$this->validatePermissions($this->permissions, array_flip($this->permissionSettings));
		$this->validatePermissions($this->moderators, array_flip($this->moderatorSettings));
	}
	
	/**
	 * Validates the parent id.
	 */
	protected function validateParentID() {
		if ($this->parentID) {
			try {
				Category::getCategory($this->parentID);
			}
			catch (IllegalLinkException $e) {
				throw new UserInputException('parentID', 'invalid');
			}
		}
	}
	
	/**
	 * Validates the given permissions with the given settings.
	 *
	 * @param	array		$permissions
	 * @param	array		$settings
	 */
	public function validatePermissions($permissions, $settings) {
		foreach ($permissions as $permission) {
			// type
			if (!isset($permission['type']) || ($permission['type'] != 'user' && $permission['type'] != 'group')) {
				throw new UserInputException();
			}
			
			// id
			if (!isset($permission['id'])) {
				throw new UserInputException();
			}
			if ($permission['type'] == 'user') {
				$user = new User(intval($permission['id']));
				if (!$user->userID) throw new UserInputException();
			}
			else {
				$group = new Group(intval($permission['id']));
				if (!$group->groupID) throw new UserInputException();
			}
			
			// settings
			if (!isset($permission['settings']) || !is_array($permission['settings'])) {
				throw new UserInputException();
			}
			
			// find invalid settings
			foreach ($permission['settings'] as $key => $value) {
				if (!isset($settings[$key]) || ($value != -1 && $value != 0 && $value =! 1)) {
					throw new UserInputException();
				}
			}
			
			// find missing settings
			foreach ($settings as $key => $value) {
				if (!isset($permission['settings'][$key])) {
					throw new UserInputException();
				}
			}
		}
	}
	
	/**
	 * @see Form::save()
	 */
	public function save() {
		parent::save();
		
		// save category
		$this->category = CategoryEditor::create($this->parentID, ($this->position ? $this->position : null), $this->title, $this->description, $this->allowDescriptionHtml, $this->categoryType,
		$this->icon, $this->externalURL, $this->styleID, $this->enforceStyle, $this->daysPrune, $this->sortField, $this->sortOrder, $this->enableRating, $this->entriesPerPage, WCF::getLanguage()->getLanguageID());

		// save permissions
		$this->permissions = CategoryEditor::getCleanedPermissions($this->permissions);
		$this->category->addPermissions($this->permissions, $this->permissionSettings);
		
		// save moderators
		$this->moderators = CategoryEditor::getCleanedPermissions($this->moderators);
		$this->category->addModerators($this->moderators, $this->moderatorSettings);
		
		// reset cache
		Category::resetCache();
		
		// reset sessions
		Session::resetSessions(array(), true, false);
		$this->saved();
		
		// reset values
		$this->parentID = $this->allowDescriptionHtml = $this->categoryType = $this->styleID = $this->enforceStyle = $this->daysPrune = $this->entriesPerPage = 0;
		$this->position = $this->title = $this->description = $this->icon = $this->externalURL = $this->sortField = $this->sortOrder = '';
		$this->enableRating = -1;
		$this->permissions = $this->moderators = array();
		
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
			'categoryOptions' => $this->categoryOptions,
			'availableStyles' => $this->availableStyles,
			'permissions' => $this->permissions,
			'moderators' => $this->moderators,
			'permissionSettings' => $this->permissionSettings,
			'moderatorSettings' => $this->moderatorSettings,
			'parentID' => $this->parentID,
			'position' => $this->position,
			'title' => $this->title,
			'description' => $this->description,
			'allowDescriptionHtml' => $this->allowDescriptionHtml,
			'categoryType' => $this->categoryType,
			'icon' => $this->icon,
			'externalURL' => $this->externalURL,
			'styleID' => $this->styleID,
			'enforceStyle' => $this->enforceStyle,
			'daysPrune' => $this->daysPrune,
			'sortField' => $this->sortField,
			'sortOrder' => $this->sortOrder,
			'enableRating' => $this->enableRating,
			'entriesPerPage' => $this->entriesPerPage
		));
	}
}
?>