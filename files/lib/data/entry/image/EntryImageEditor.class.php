<?php
// wsif imports
require_once(WSIF_DIR.'lib/data/entry/image/EntryImage.class.php');

/**
 * Provides functions to manage entry images.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wsif
 * @subpackage	data.entry.image
 * @category	Infinite Filebase
 */
class EntryImageEditor extends EntryImage {
	protected static $allowedImageExtensions = null;
	protected static $allowedImageExtensionsDesc = null;

	/**
	 * Updates this image.
	 *
	 * @param	string		$title
	 * @param	string		$description
	 */
	public function update($title, $description) {
		$sql = "UPDATE 	wsif".WSIF_N."_entry_image
			SET	title = '".escapeString($title)."',
				description = '".escapeString($description)."'
			WHERE 	imageID = ".$this->imageID;
		WCF::getDB()->sendQuery($sql);
	}

	/*
	 * Replaces the physical image of this image.
	 *
	 * @param	string		$field
	 * @param	string		$tmpName
	 * @param	string		$filename
	 * @param	string		$title
	 * @return	EntryImageEditor
	 */
	public function replacePhysicalImage($field, $tmpName, $filename, $title) {
		// get validated data
		$data = self::validateFile($field, $tmpName, $filename, $title);

		// copy image
		$path = WSIF_DIR.'storage/images/'.$this->imageID;
		if (!@copy($tmpName, $path)) {
			// rollback
			@unlink($tmpName);
			$sql = "DELETE FROM	wsif".WSIF_N."_entry_image
				WHERE		imageID = ".$this->imageID;
			WCF::getDB()->sendQuery($sql);
			throw new UserInputException($field, 'copyFailed');
		}
		@chmod($path, 0777);

		// update image
		$sql = "UPDATE 	wsif".WSIF_N."_entry_image
			SET	title = '".escapeString($data['title'])."',
				filesize = ".$data['filesize'].",
				mimeType = '".escapeString($data['mimeType'])."',
				uploadTime = ".TIME_NOW.",
				width = ".$data['width'].",
				height = ".$data['height']."
			WHERE 	imageID = ".$this->imageID;
		WCF::getDB()->sendQuery($sql);

		// update instance
		$this->data = array_merge($this->data, $data);

		// create thumbnail
		$this->createThumbnail();
	}

	/**
	 * Sets the entry id for this image.
	 *
	 * @param	integer		$imageID
	 */
	public function setEntryID($entryID) {
		$sql = "UPDATE 	wsif".WSIF_N."_entry_image
			SET	entryID = ".$entryID."
			WHERE 	imageID = ".$this->imageID;
		WCF::getDB()->sendQuery($sql);
		$this->data['entryID'] = $entryID;
	}

	/**
	 * Sets this image as default.
	 */
	public function setAsDefault() {
		// remove old default
		$sql = "UPDATE	wsif".WSIF_N."_entry_image
			SET	isDefault = 0
			WHERE	entryID = ".$this->entryID."
				AND isDefault = 1";
		WCF::getDB()->sendQuery($sql);

		// set new default
		$sql = "UPDATE 	wsif".WSIF_N."_entry_image
			SET	isDefault = 1
			WHERE 	imageID = ".$this->imageID;
		WCF::getDB()->sendQuery($sql);

		// update entry
		$sql = "UPDATE 	wsif".WSIF_N."_entry
			SET	defaultImageID = ".$this->imageID."
			WHERE 	entryID = ".$this->entryID;
		WCF::getDB()->sendQuery($sql);
	}

	/**
	 * Creates a thumbnail for this image.
	 */
	public function createThumbnail() {
		if ($this->width > 150 || $this->height > 150) {
			require_once(WCF_DIR.'lib/data/image/Thumbnail.class.php');
			$targetFile = WSIF_DIR.'storage/images/thumbnails/'.$this->imageID;
			$thumbnail = new Thumbnail(WSIF_DIR.'storage/images/'.$this->imageID, 150, 150, false, null, false);

			// get thumbnail
			try {
				if (($thumbnailData = $thumbnail->makeThumbnail())) {
					// save thumbnail
					$file = new File($targetFile);
					$file->write($thumbnailData);
					unset($thumbnailData);
					$file->close();

					// set permissions
					@chmod($targetFile, 0777);

					// update image
					$thumbnailFilesize = intval(filesize($targetFile));
					list($thumbnailWidth, $thumbnailHeight,) = @getImageSize($targetFile);
					$sql = "UPDATE	wsif".WSIF_N."_entry_image
						SET 	hasThumbnail = 1,
							thumbnailMimeType = '".escapeString($thumbnail->getMimeType())."',
							thumbnailFilesize = ".$thumbnailFilesize.",
							thumbnailWidth = ".$thumbnailWidth.",
							thumbnailHeight = ".$thumbnailHeight."
						WHERE 	imageID = ".$this->imageID;
					WCF::getDB()->registerShutdownUpdate($sql);
				}
			}
			catch (Exception $e) {}
		}
	}

	/**
	 * Deletes this image.
	 */
	public function delete() {
		self::deleteAll($this->imageID);
	}

	/**
	 * Creates a new image.
	 *
	 * @param	string			$field
	 * @param	integer			$entryID
	 * @param	integer			$userID
	 * @param	string			$username
	 * @param	string			$tmpName
	 * @param	string			$filename
	 * @param	string			$title
	 * @param	string			$description
	 * @param	string			$ipAddress
	 * @return	EntryImageEditor
	 */
	public static function create($field, $entryID, $userID, $username, $tmpName, $filename, $title, $description = '', $ipAddress = null) {
		if ($ipAddress == null) $ipAddress = WCF::getSession()->ipAddress;

		// get validated data
		$data = self::validateFile($field, $tmpName, $filename, title);

		// save image
		$sql = "INSERT INTO	wsif".WSIF_N."_entry_image
					(entryID, userID, username, title, description, filename, filesize, mimeType, uploadTime, width, height, ipAddress)
			VALUES		(".$entryID.", ".$userID.", '".escapeString($username)."', '".escapeString($data['title'])."', '".escapeString($description)."', '".escapeString($filename)."', '".$data['filesize']."', '".escapeString($data['mimeType'])."', ".TIME_NOW.", ".$data['width'].", ".$data['height'].", '".escapeString($ipAddress)."')";
		WCF::getDB()->sendQuery($sql);

		// get image id
		$imageID = WCF::getDB()->getInsertID("wsif".WSIF_N."_entry_image", 'imageID');

		// copy image
		$path = WSIF_DIR.'storage/images/'.$imageID;
		if (!@copy($tmpName, $path)) {
			// rollback
			@unlink($tmpName);
			$sql = "DELETE FROM	wsif".WSIF_N."_entry_image
				WHERE		imageID = ".$imageID;
			WCF::getDB()->sendQuery($sql);
			throw new UserInputException($field, 'copyFailed');
		}
		@chmod($path, 0777);

		// get new image
		$image = new EntryImageEditor($imageID);

		// create thumbnail
		$image->createThumbnail();

		// return image
		return $image;
	}

	/**
	 * Validates the file with the given data and returns the validated data.
	 *
	 * @param	string		$field
	 * @param	string		$tmpName
	 * @param	string		$filename
	 * @param	string		$title
	 * @return	array
	 */
	protected static function validateFile($field, $tmpName, $filename, $title) {
		// check image content
		if (!ImageUtil::checkImageContent($tmpName)) {
			throw new UserInputException($field, 'badImage');
		}

		// get image data
		if (($imageData = @getImageSize($tmpName)) === false) {
			throw new UserInputException($field, 'badImage');
		}

		// get mime type
		$mimeType = $imageData['mime'];

		// get file extension by mime
		$fileExtension = ImageUtil::getExtensionByMimeType($mimeType);

		// check file extension
		if (!in_array($fileExtension, self::getAllowedImageExtensions())) {
			throw new UserInputException($field, 'illegalExtension');
		}

		// get image size
		$width = $imageData[0];
		$height = $imageData[1];
		if (!$width || !$height) {
			throw new UserInputException($field, 'badImage');
		}

		// get filesize
		$filesize = intval(@filesize($tmpName));

		// check size
		if ($width > WCF::getUser()->getPermission('user.filebase.maxEntryImageWidth') || $height > WCF::getUser()->getPermission('user.filebase.maxEntryImageHeight') || $filesize > WCF::getUser()->getPermission('user.filebase.maxEntryImageSize')) {
			throw new UserInputException($field, 'tooLarge');
		}

		// use filename as title
		if (empty($title)) {
			$title = $filename;
		}

		// return validated data
		return array(
			'title' => $title,
			'filesize' => $filesize,
			'mimeType' => $mimeType,
			'width' => $width,
			'height' => $height
		);
	}

	/**
	 * Deletes all images with the given image ids.
	 *
	 * @param	string		$imageIDs
	 */
	public static function deleteAll($imageIDs) {
		if (empty($imageIDs)) return;

		// delete images
		$sql = "DELETE FROM	wsif".WSIF_N."_entry_image
			WHERE		imageID IN (".$imageIDs.")";
		WCF::getDB()->sendQuery($sql);

		// delete files
		$imageIDArray = explode(',', $imageIDs);
		foreach ($imageIDArray as $imageID) {
			if (file_exists(WSIF_DIR.'storage/images/'.$imageID)) @unlink(WSIF_DIR.'storage/images/'.$imageID);
			if (file_exists(WSIF_DIR.'storage/images/thumbnails/'.$imageID)) @unlink(WSIF_DIR.'storage/images/thumbnails/'.$imageID);
		}
	}

	/**
	 * Returns the allowed image extensions.
	 *
	 * @return	array
	 */
	public static function getAllowedImageExtensions() {
		if (self::$allowedImageExtensions === null) {
			self::$allowedImageExtensions = array_unique(explode("\n", StringUtil::unifyNewlines(WCF::getUser()->getPermission('user.filebase.allowedEntryImageExtensions'))));
		}
		return self::$allowedImageExtensions;
	}

	/**
	 * Returns the allowed image extensions description.
	 *
	 * @return	string
	 */
	public static function getAllowedImageExtensionsDesc() {
		if (self::$allowedImageExtensionsDesc === null) {
			// get allowed extensions
			$allowedExtensions = array_unique(explode("\n", StringUtil::unifyNewlines(WCF::getUser()->getPermission('user.filebase.allowedEntryImageExtensions'))));

			// sort
			sort($allowedExtensions);

			// implode to string
			self::$allowedImageExtensionsDesc = implode(', ', $allowedExtensions);
		}
		return self::$allowedImageExtensionsDesc;
	}
}
?>