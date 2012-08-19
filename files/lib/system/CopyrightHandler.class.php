<?php
// wcf imports
require_once(WCF_DIR.'lib/system/event/EventHandler.class.php');

/**
 * Handles the visible copyright notices.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	system
 * @category	Infinite Filebase
 */
final class CopyrightHandler {
	/**
	 * copyright handler object
	 *
	 * @var	CopyrightHandler
	 */
	private static $instance = null;

	/**
	 * list of copyrights with their identifiers
	 *
	 * @var	array
	 */
	private $copyrights = array();

	/**
	 * list of copyright texts
	 *
	 * @var	array
	 */
	private $copyrightTexts = array();

	/**
	 * Creates a new CopyrightHandler object.
	 */
	private function __construct() {
		$this->modifyCopyrightNotices();
	}

	/**
	 * Modifies the copyright notices.
	 */
	private function modifyCopyrightNotices() {
		// add filebase copyright notice
		$this->add('com.wcfsolutions.wsif', WCF::getLanguage()->getDynamicVariable('wsif.global.copyright'));

		// call modifyCopyrightNotices event
		EventHandler::fireAction($this, 'modifyCopyrightNotices');
	}

	/**
	 * Adds the copyright notice with the given identifier and the given text.
	 *
	 * @param	string		$identifier
	 * @param	string		$text
	 */
	public function add($identifier, $text) {
		if (array_search($identifier, $this->copyrights)) {
			throw new SystemException("copyright with the identifier '".$identifier."' does already exist");
		}

		$this->copyrights[] = $identifier;
		$this->copyrightTexts[$identifier] = $text;
	}

	/**
	 * Replaces the copyright notice with the given old identifier with the copyright notice with the given new
	 * identifier.
	 *
	 * Please note that some plugins might require a branding free license for a legal replacement of their
	 * corresponding copyright notices.
	 *
	 * @param	string		$oldIdentifier
	 * @param	string		$newIdentifier
	 * @param	string		$text
	 */
	public function replace($oldIdentifier, $newIdentifier, $text) {
		$key = array_search($oldIdentifier, $this->copyrights);
		if ($key === false) {
			throw new SystemException("copyright with the identifier '".$oldIdentifier."' not found");
		}

		$this->copyrights[$key] = $newIdentifier;
		$this->copyrightTexts[$newIdentifier] = $text;

		unset($this->copyrightTexts[$oldIdentifier]);
	}

	/**
	 * Removes the copyright notice with the given identifier.
	 *
	 * Please note that some plugins might require a branding free license for a legal removement of their
	 * corresponding copyright notices.
	 *
	 * @param	string		$identifier
	 */
	public function remove($identifier) {
		$key = array_search($identifier, $this->copyrights);
		if ($key === false) {
			throw new SystemException("copyright with the identifier '".$identifier."' not found");
		}

		unset($this->copyrights[$key]);
		unset($this->copyrightTexts[$identifier]);
	}

	/**
	 * Returns the copyright notice output.
	 *
	 * @return	string
	 */
	public function getOutput() {
		$output = '';

		foreach ($this->copyrights as $identifier) {
			if (!empty($output)) $output .= '<br />';
			$output .= $this->copyrightTexts[$identifier];
		}

		return $output;
	}

	/**
	 * Returns an instance of the CopyrightHandler class.
	 *
	 * @return	CopyrightHandler
	 */
	public static function getInstance() {
		if (self::$instance == null) {
			self::$instance = new CopyrightHandler();
		}

		return self::$instance;
	}
}
?>