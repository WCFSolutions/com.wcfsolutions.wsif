<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/entry/file/EntryFile.class.php');

/**
 * Provides functions to manage entry files.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	data.entry.file
 * @category	Infinite Filebase
 */
class EntryFileEditor extends EntryFile {
	protected static $allowedFileExtensions = null;
	protected static $allowedFileExtensionsDesc = null;
	
	/**
	 * Updates this file.
	 * 
	 * @param	string		$title
	 * @param	string		$description
	 */
	public function update($title, $description) {
		$sql = "UPDATE 	wsif".WSIF_N."_entry_file
			SET	title = '".escapeString($title)."',
				description = '".escapeString($description)."'
			WHERE 	fileID = ".$this->fileID;
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Sets the entry id for this file.
	 *
	 * @param	integer		$entryID
	 */
	public function setEntryID($entryID) {
		$sql = "UPDATE 	wsif".WSIF_N."_entry_file
			SET	entryID = ".$entryID."
			WHERE 	fileID = ".$this->fileID;
		WCF::getDB()->sendQuery($sql);
		$this->data['entryID'] = $entryID;
	}
	
	/**
	 * Sets this file as default.
	 */
	public function setAsDefault() {
		// remove old default
		$sql = "UPDATE	wsif".WSIF_N."_entry_file
			SET	isDefault = 0
			WHERE	entryID = ".$this->entryID."
				AND isDefault = 1";
		WCF::getDB()->sendQuery($sql);
		
		// set new default
		$sql = "UPDATE 	wsif".WSIF_N."_entry_file
			SET	isDefault = 1
			WHERE 	fileID = ".$this->fileID;
		WCF::getDB()->sendQuery($sql);
		
		// update entry
		$sql = "UPDATE 	wsif".WSIF_N."_entry
			SET	defaultFileID = ".$this->fileID."
			WHERE 	entryID = ".$this->entryID;
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Deletes this file.
	 */
	public function delete() {
		self::deleteAll($this->fileID);
	}
	
	/**
	 * Creates a new file.
	 * 
	 * @param	string			$field
	 * @param	integer			$entryID
	 * @param	integer			$userID
	 * @param	string			$username
	 * @param	string			$file
	 * @param	string			$filename
	 * @param	string			$mimeType
	 * @param	string			$title
	 * @param	string			$description
	 * @param	integer			$fileType
	 * @param	string			$externalURL
	 * @param	string			$ipAddress
	 * @return	EntryImageEditor
	 */
	public static function create($field, $entryID, $userID, $username, $file, $filename, $mimeType, $title, $description, $fileType, $externalURL = '', $ipAddress = null) {
		if ($ipAddress == null) $ipAddress = WCF::getSession()->ipAddress;
		
		$filesize = 0;
		if ($fileType == self::TYPE_UPLOAD) {
			// check file extension
			$fileExtension = StringUtil::toLowerCase(StringUtil::substring($filename, StringUtil::lastIndexOf($filename, '.') + 1));
			if (!preg_match(self::getAllowedFileExtensions(), $fileExtension)) {
				throw new UserInputException($field, 'illegalExtension');
			}
			
			// get filesize
			$filesize = intval(@filesize($file));
			
			// check size
			if ($filesize > WCF::getUser()->getPermission('user.filebase.maxEntryFileSize')) {
				throw new UserInputException($field, 'tooLarge');
			}
		}
		
		// use filename as title
		if (empty($title)) {
			if ($fileType == self::TYPE_UPLOAD) $title = $filename;
			else $title = $externalURL;
		}
		
		// save file
		$sql = "INSERT INTO	wsif".WSIF_N."_entry_file
					(entryID, userID, username, title, description, fileType, filename, filesize, externalURL, mimeType, uploadTime, ipAddress)
			VALUES		(".$entryID.", ".$userID.", '".escapeString($username)."', '".escapeString($title)."', '".escapeString($description)."', '".escapeString($fileType)."', '".escapeString($filename)."', '".$filesize."', '".escapeString($externalURL)."', '".escapeString($mimeType)."', ".TIME_NOW.", '".escapeString($ipAddress)."')";
		WCF::getDB()->sendQuery($sql);
		
		// get file id
		$fileID = WCF::getDB()->getInsertID("wsif".WSIF_N."_entry_file", 'fileID');
		
		// copy file
		if ($fileType == self::TYPE_UPLOAD) {
			$path = WSIF_DIR.'storage/files/'.$fileID;
			if (!@copy($file, $path)) {
				// rollback
				@unlink($file);
				$sql = "DELETE FROM	wsif".WSIF_N."_entry_file
					WHERE		fileID = ".$fileID;
				WCF::getDB()->sendQuery($sql);
				throw new UserInputException($field, 'copyFailed');
			}
			@chmod($path, 0777);
		}
		
		// get new file
		$file = new EntryFileEditor($fileID);
		
		// return file
		return $file;
	}
	
	/**
	 * Deletes all files with the given file ids.
	 *
	 * @param	string		$fileIDs
	 */
	public static function deleteAll($fileIDs) {
		if (empty($fileIDs)) return;
		
		// delete file
		$sql = "DELETE FROM	wsif".WSIF_N."_entry_file
			WHERE		fileID IN (".$fileIDs.")";
		WCF::getDB()->sendQuery($sql);
		
		// delete file downloaders
		$sql = "DELETE FROM	wsif".WSIF_N."_entry_file_downloader
			WHERE		fileID IN (".$fileIDs.")";
		WCF::getDB()->sendQuery($sql);
		
		// delete file from filesystem
		$fileIDArray = explode(',', $fileIDs); 
		foreach ($fileIDArray as $fileID) {
			if (file_exists(WSIF_DIR.'storage/files/'.$fileID)) @unlink(WSIF_DIR.'storage/files/'.$fileID);
		}
	}
	
	/**
	 * Returns the allowed file extensions.
	 *
	 * @return	string
	 */
	public static function getAllowedFileExtensions() {
		if (self::$allowedFileExtensions === null) {
			$allowedExtensions = implode("\n", array_unique(explode("\n", StringUtil::unifyNewlines(WCF::getUser()->getPermission('user.filebase.allowedEntryFileExtensions')))));
			self::$allowedFileExtensions = '/^('.StringUtil::replace("\n", "|", StringUtil::replace('\*', '.*', preg_quote($allowedExtensions, '/'))).')$/i';
		}
		return self::$allowedFileExtensions;
	}
	
	/**
	 * Returns the allowed file extensions description.
	 *
	 * @return	string
	 */
	public static function getAllowedFileExtensionsDesc() {
		if (self::$allowedFileExtensionsDesc === null) {
			// get allowed extensions
			$allowedExtensions = array_unique(explode("\n", StringUtil::unifyNewlines(WCF::getUser()->getPermission('user.filebase.allowedEntryFileExtensions'))));

			// sort
			sort($allowedExtensions);
			
			// check wildcards
			for ($i = 0, $j = count($allowedExtensions); $i < $j; $i++) {
				if (strpos($allowedExtensions[$i], '*') !== false) {
					for ($k = $j - 1; $k > $i; $k--) {
						if (preg_match('/^'.str_replace('\*', '.*', preg_quote($allowedExtensions[$i], '/')).'$/i', $allowedExtensions[$k])) {
							array_splice($allowedExtensions, $k, 1);
							$j--;
						}
					}
				}
			}
			
			// implode to string
			self::$allowedFileExtensionsDesc = implode(', ', $allowedExtensions);
		}
		return self::$allowedFileExtensionsDesc;
	}
}
?>