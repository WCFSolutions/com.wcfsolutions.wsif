<?php
// wcf imports
require_once(WCF_DIR.'lib/system/WCFACP.class.php');

/**
 * This class extends the main WCFACP class by filebase specific functions.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	system
 * @category	Infinite Filebase
 */
class WSIFACP extends WCFACP {
	/**
	 * @see WCF::getOptionsFilename()
	 */
	protected function getOptionsFilename() {
		return WSIF_DIR.'options.inc.php';
	}
	
	/**
	 * Initialises the template engine.
	 */
	protected function initTPL() {
		global $packageDirs;
		
		self::$tplObj = new ACPTemplate(self::getLanguage()->getLanguageID(), ArrayUtil::appendSuffix($packageDirs, 'acp/templates/'));
		$this->assignDefaultTemplateVariables();
	}
	
	/**
	 * Does the user authentication.
	 */
	protected function initAuth() {
		parent::initAuth();
		
		// user ban
		if (self::getUser()->banned) {
			throw new PermissionDeniedException();
		}
	}
	
	/**
	 * @see WCF::assignDefaultTemplateVariables()
	 */
	protected function assignDefaultTemplateVariables() {
		parent::assignDefaultTemplateVariables();
		
		self::getTPL()->assign(array(
			// add jump to filebase link 			
			'additionalHeaderButtons' => '<li><a href="'.RELATIVE_WSIF_DIR.'index.php?page=Index"><img src="'.RELATIVE_WSIF_DIR.'icon/indexS.png" alt="" /> <span>'.WCF::getLanguage()->get('wsif.acp.jumpToFilebase').'</span></a></li>',
			// individual page title
			'pageTitle' => WCF::getLanguage()->get(StringUtil::encodeHTML(PAGE_TITLE)).' - '.StringUtil::encodeHTML(PACKAGE_NAME.' '.PACKAGE_VERSION)
		));
	}
	
	/**
	 * @see WCF::loadDefaultCacheResources()
	 */
	protected function loadDefaultCacheResources() {
		parent::loadDefaultCacheResources();
		$this->loadDefaultWSIFCacheResources();
	}
	
	/**
	 * Loads default cache resources of community filebase acp.
	 * Can be called statically from other applications or plugins.
	 */
	public static function loadDefaultWSIFCacheResources() {
		WCF::getCache()->addResource('category', WSIF_DIR.'cache/cache.category.php', WSIF_DIR.'lib/system/cache/CacheBuilderCategory.class.php');
		WCF::getCache()->addResource('categoryData', WSIF_DIR.'cache/cache.categoryData.php', WSIF_DIR.'lib/system/cache/CacheBuilderCategoryData.class.php', 0, 300);
		WCF::getCache()->addResource('entryPrefix', WSIF_DIR.'cache/cache.entryPrefix.php', WSIF_DIR.'lib/system/cache/CacheBuilderEntryPrefix.class.php');
	}
}
?>