<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/category/Category.class.php');
require_once(WSIF_DIR.'lib/data/entry/ViewableEntry.class.php');
require_once(WSIF_DIR.'lib/data/entry/comment/EntryCommentEditor.class.php');

// wcf imports
require_once(WCF_DIR.'lib/action/AbstractSecureAction.class.php');

/**
 * Deletes an entry comment.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	action
 * @category	Infinite Filebase
 */
class EntryCommentDeleteAction extends AbstractSecureAction {
	/**
	 * comment id
	 *
	 * @var integer
	 */
	public $commentID = 0;

	/**
	 * comment editor object
	 *
	 * @var EntryCommentEditor
	 */
	public $comment = null;

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

		// check module
		if (MODULE_COMMENT != 1) {
			throw new IllegalLinkException();
		}

		// get comment
		if (isset($_REQUEST['commentID'])) $this->commentID = intval($_REQUEST['commentID']);
		$this->comment = new EntryCommentEditor($this->commentID);
		if (!$this->comment->commentID) {
			throw new IllegalLinkException();
		}

		// get entry
		$this->entry = new ViewableEntry($this->comment->entryID);

		// get category
		$this->category = Category::getCategory($this->entry->categoryID);
		$this->entry->enter($this->category);

		// check comment availability
		if (!$this->entry->enableComments) {
			throw new IllegalLinkException();
		}

		// check permission
		if (!$this->comment->isDeletable($this->category)) {
			throw new PermissionDeniedException();
		}
	}

	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();

		// delete comment
		$this->comment->delete();

		// update comment count
		$this->entry->updateComments(-1);
		$this->category->updateEntryComments(-1);
		$this->executed();

		// forward
		HeaderUtil::redirect('index.php?page=EntryComments&entryID='.$this->comment->entryID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
}
?>