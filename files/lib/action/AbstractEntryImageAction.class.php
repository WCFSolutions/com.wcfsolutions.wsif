<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/category/CategoryEditor.class.php');
require_once(WSIF_DIR.'lib/data/entry/EntryEditor.class.php');
require_once(WSIF_DIR.'lib/data/entry/image/EntryImageEditor.class.php');

// wcf imports
require_once(WCF_DIR.'lib/action/AbstractSecureAction.class.php');

/**
 * Represents an abstract entry image action.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	action
 * @category	Infinite Filebase
 */
abstract class AbstractEntryImageAction extends AbstractSecureAction {
	/**
	 * image id
	 *
	 * @var	integer
	 */
	public $imageID = 0;

	/**
	 * image object
	 *
	 * @var	EntryImage
	 */
	public $image = null;

	/**
	 * entry object
	 *
	 * @var ViewableEntry
	 */
	public $entry = null;

	/**
	 * category object
	 *
	 * @var Category
	 */
	public $category = null;

	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		// get image
		if (isset($_REQUEST['imageID'])) $this->imageID = intval($_REQUEST['imageID']);
		$this->image = new EntryImageEditor($this->imageID);
		if (!$this->image->imageID) {
			throw new IllegalLinkException();
		}

		// get entry
		$this->entry = new EntryEditor($this->image->entryID);

		// get category
		$this->category = new CategoryEditor($this->entry->categoryID);
		$this->entry->enter($this->category);
	}
}
?>