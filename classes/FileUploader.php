<?php
/*
* 2007-2011 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 7331 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class FileUploaderCore
{

	private $allowedExtensions = array();
	private $sizeLimit = 10485760;
	private $file;

	function __construct(array $allowedExtensions = array(), $sizeLimit = 10485760)
	{
		$allowedExtensions = array_map("strtolower", $allowedExtensions);

		$this->allowedExtensions = $allowedExtensions;
		$this->sizeLimit = $sizeLimit;

		if (isset($_GET['qqfile']))
			$this->file = new qqUploadedFileXhr();
		else
			$this->file = false;
	}


	private function toBytes($str)
	{
		$val = trim($str);
		$last = strtolower($str[strlen($str)-1]);
		switch($last) {
			case 'g': $val *= 1024;
			case 'm': $val *= 1024;
			case 'k': $val *= 1024;
		}
		return $val;
	}

	/**
	 * Returns array('success'=>true) or array('error'=>'error message')
	 */
	function handleUpload()
	{
		if (!$this->file){
			return array('error' => Tools::displayError('No files were uploaded.'));
		}

		$size = $this->file->getSize();

		if ($size == 0) {
			return array('error' => Tools::displayError('File is empty'));
		}

		if ($size > $this->sizeLimit) {
			return array('error' => Tools::displayError('File is too large'));
		}

		$pathinfo = pathinfo($this->file->getName());
		$filename = $pathinfo['filename'];
		$these = implode(', ', $this->allowedExtensions);
		if (!isset($pathinfo['extension']))
			return array('error' => Tools::displayError('File has an invalid extension, it should be one of '). $these . '.');
		$ext = $pathinfo['extension'];
		if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions))
			return array('error' => Tools::displayError('File has an invalid extension, it should be one of '). $these . '.');


		return $this->file->save();

	}
}

/**
 * Handle file uploads via XMLHttpRequest
 */
class qqUploadedFileXhr
{
	/**
	* Save the file to the specified path
	* @return boolean TRUE on success
	*/
	function upload($path)
	{
		$input = fopen("php://input", "r");
		$temp = tmpfile();
		$realSize = stream_copy_to_stream($input, $temp);
		fclose($input);
		if ($realSize != $this->getSize())
			return false;
		$target = fopen($path, "w");
		fseek($temp, 0, SEEK_SET);
		stream_copy_to_stream($temp, $target);
		fclose($target);

		return true;
	}

	function save()
	{
		$product = new Product($_GET['id_product']);
		if (!Validate::isLoadedObject($product))
			return array('error' => Tools::displayError('Cannot add image because product add failed.'));
		else
		{
			$image = new Image();
			$image->id_product = (int)($product->id);
			$image->position = Image::getHighestPosition($product->id) + 1;
			if (!Image::getCover($image->id_product))
				$image->cover = 1;
			else
				$image->cover = 0;
			if (!$image->add())
				return array('error' => Tools::displayError('Error while creating additional image'));
			else
			{
				return $this->copyImage($product->id, $image->id);
			}
		}
	}

	public function copyImage($id_product, $id_image, $method = 'auto')
	{
		$image = new Image($id_image);
		$p = new Product($id_product);
		if (!$new_path = $image->getPathForCreation())
			return array('error' => Tools::displayError('An error occurred during new folder creation'));
		if (!$tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS') OR !$this->upload($tmpName))
			return array('error' => Tools::displayError('An error occurred during the image upload'));
		elseif (!imageResize($tmpName, $new_path.'.'.$image->image_format))
			return array('error' => Tools::displayError('An error occurred while copying image.'));
		elseif($method == 'auto')
		{
			$imagesTypes = ImageType::getImagesTypes('products');
			foreach ($imagesTypes AS $k => $imageType)
			{
				$theme = (Shop::isFeatureActive() ? '-'.$imageType['id_theme'] : '');
				if (!imageResize($tmpName, $new_path.'-'.stripslashes($imageType['name']).$theme.'.'.$image->image_format, $imageType['width'], $imageType['height'], $image->image_format))
					return array('error' => Tools::displayError('An error occurred while copying image:').' '.stripslashes($imageType['name']));
			}
		}
		unlink($tmpName);
		Hook::exec('watermark', array('id_image' => $id_image, 'id_product' => $id_product));
		$lang = Context::getContext()->employee->id_lang;

		if (!$image->update())
			return array('error' => Tools::displayError('Error while updating status'));
		$img = array('id_image' => $image->id, 'position' => $image->position, 'cover' => $image->cover);
		return array("success" => $img);
	}

	function getName()
	{
		return $_GET['qqfile'];
	}

	function getSize()
	{
		if (isset($_SERVER["CONTENT_LENGTH"]))
			return (int)$_SERVER["CONTENT_LENGTH"];
		else
			throw new Exception('Getting content length is not supported.');
	}
}
