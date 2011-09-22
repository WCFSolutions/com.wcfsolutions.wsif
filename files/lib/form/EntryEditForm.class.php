<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/entry/EntryFrame.class.php');
require_once(WSIF_DIR.'lib/form/EntryAddForm.class.php');

/**
 * Shows the entry edit form.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	form
 * @category	Infinite Filebase
 */
class EntryEditForm extends EntryAddForm {
	// system
	public $templateName = 'entryEdit';
	
	/**
	 * entry frame object
	 * 
	 * @var EntryFrame
	 */
	public $frame = null;
	
	// parameters
	public $deleteReason = '';
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		MessageForm::readParameters();

		// get entry frame
		$this->frame = new EntryFrame($this);
		
		// get entry
		$this->entry = $this->frame->getEntry()->getEditor();
		
		// get category
		$this->category = $this->frame->getCategory()->getEditor();
		
		// check permission
		if (!$this->entry->isEditable($this->category) && !$this->entry->isDeletable($this->category)) {
			throw new PermissionDeniedException();
		}
	}
	
	/**
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_POST['deleteReason'])) $this->deleteReason = StringUtil::trim($_POST['deleteReason']);
	}
	
	/**
	 * @see Form::submit()
	 */
	public function submit() {
		// call submit event
		EventHandler::fireAction($this, 'submit');
		
		$this->readFormParameters();
		
		try {
			// preview
			if ($this->preview) {
				WCF::getTPL()->assign('preview', EntryEditor::createPreview($this->subject, $this->text, $this->enableSmilies, $this->enableHtml, $this->enableBBCodes));
			}

			// send message
			if ($this->send) {
				$this->validate();
				// no errors
				$this->save();
			}
			
			// delete entry
			if (isset($_POST['deleteEntry'])) {
				if (!$this->entry->isDeletable($this->category)) {
					throw new PermissionDeniedException();
				}
				
				if (isset($_POST['sure'])) {
					if (ENTRY_ENABLE_RECYCLE_BIN && !$this->entry->isDeleted) {
						$this->entry->trash($this->deleteReason);
					}
					else {
						$this->entry->delete();
					}
					
					// refresh last category entry
					$this->category->refresh();
					if ($this->entry->entryID == $this->category->getLastEntryID($this->entry->languageID)) {
						$this->category->setLastEntries();
					}
					
					// reset cache
					WCF::getCache()->clearResource('categoryData', true);
					WCF::getCache()->clearResource('stat');
					
					if ($this->entry->isDeleted) HeaderUtil::redirect('index.php?page=Category&categoryID='.$this->entry->categoryID.SID_ARG_2ND_NOT_ENCODED);
					else HeaderUtil::redirect('index.php?page=Entry&entryID='.$this->entry->entryID.SID_ARG_2ND_NOT_ENCODED);
					exit;
				}
				else {
					throw new UserInputException('sure');
				}
			}
		}
		catch (UserInputException $e) {
			$this->errorField = $e->getField();
			$this->errorType = $e->getType();
		}
	}
	
	/**
	 * Does nothing.
	 */
	protected function validateFile() {}
	
	/**
	 * @see Form::save()
	 */
	public function save() {
		if (!$this->entry->isEditable($this->frame->getCategory())) {
			throw new PermissionDeniedException();
		}
		MessageForm::save();
		
		// update entry
		$this->entry->update($this->languageID, $this->prefixID, $this->subject, $this->text, $this->teaser, $this->getOptions());
		
		// save tags
		if (MODULE_TAGGING && ENTRY_ENABLE_TAGS && $this->frame->getCategory()->getPermission('canSetEntryTags')) {
			$this->entry->updateTags(TaggingUtil::splitString($this->tags));
		}
		$this->saved();
		
		// forward to entry
		HeaderUtil::redirect('index.php?page=Entry&entryID='.$this->entry->entryID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();

		if (!count($_POST)) {
			$this->languageID = $this->frame->getEntry()->languageID;
			$this->prefixID = $this->frame->getEntry()->prefixID;
			$this->subject = $this->frame->getEntry()->subject;
			$this->text = $this->frame->getEntry()->message;
			$this->teaser = $this->frame->getEntry()->teaser;
			$this->enableSmilies =  $this->frame->getEntry()->enableSmilies;
			$this->enableHtml = $this->frame->getEntry()->enableHtml;
			$this->enableBBCodes = $this->frame->getEntry()->enableBBCodes;
			
			// tags
			if (MODULE_TAGGING && ENTRY_ENABLE_TAGS && $this->frame->getCategory()->getPermission('canSetEntryTags')) {
				$this->tags = TaggingUtil::buildString($this->entry->getTags(array($this->languageID)));
			}
		}
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		// set active menu item
		require_once(WSIF_DIR.'lib/page/util/menu/EntryMenu.class.php');
		EntryMenu::getInstance()->setActiveMenuItem('wsif.entry.menu.link.entry');

		// show page
		parent::show();
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		$this->frame->assignVariables();
		WCF::getTPL()->assign(array(
			'action' => 'edit',
			'deleteReason' => $this->deleteReason
		));
	}
	
	/**
	 * @see EntryAddForm::getAvailableLanguages()
	 */
	protected function getAvailableLanguages() {
		$visibleLanguages = explode(',', WCF::getUser()->languageIDs);
		$availableLanguages = Language::getAvailableContentLanguages(PACKAGE_ID);
		foreach ($availableLanguages as $key => $language) {
			if (!in_array($language['languageID'], $visibleLanguages) && !$this->frame->getCategory()->getModeratorPermission('canEditEntry')) {
				unset($availableLanguages[$key]);
			}
		}	
		return $availableLanguages;
	}
}
?>