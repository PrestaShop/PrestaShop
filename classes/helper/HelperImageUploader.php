<?php
/*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class HelperImageUploaderCore extends HelperUploader
{
	private $_temporary_path;

	public function getMaxSize()
	{
		return (int)Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE');
	}

	public function setTemporaryPath($value)
	{
		$this->_temporary_path = $value;
	}

	public function getTemporaryPath()
	{
		if (!isset($this->_temporary_path))
			$this->_temporary_path = _PS_TMP_IMG_DIR_;

		return $this->_normalizeDirectory($this->_temporary_path);
	}

	public function getFilePath($file_name = null)
	{
		//Force file path
		return tempnam($this->getTemporaryPath(), $this->getUniqueFileName());
	}

	protected function validate($file)
	{
		$post_max_size = $this->_getPostMaxSizeBytes();

		if ($post_max_size && ($this->_getServerVars('CONTENT_LENGTH') > $post_max_size))
		{
			$file['error'] = Tools::displayError('The uploaded file exceeds the post_max_size directive in php.ini');
			return false;
		}

		if (!preg_match($this->getAcceptTypes(), $file['name']))
		{
			$file['error'] = Tools::displayError('Filetype not allowed');
			return false;
		}

		if ($error = ImageManager::validateUpload($file, Tools::getMaxUploadSize($this->getMaxSize())))
			$file['error'] = $error;

		if ($file['size'] > $this->getMaxSize())
		{
			$file['error'] = Tools::displayError('File is too big');
			return false;
		}

		return true;
	}
}