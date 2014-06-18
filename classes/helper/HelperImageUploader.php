<?php
/*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class HelperImageUploaderCore extends HelperUploader
{
	public function getMaxSize()
	{
		return (int)Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE');
	}

	public function getSavePath()
	{
		return $this->_normalizeDirectory(_PS_TMP_IMG_DIR_);
	}

	public function getFilePath($file_name = null)
	{
		//Force file path
		return tempnam($this->getSavePath(), $this->getUniqueFileName());
	}

	protected function validate(&$file)
	{
		$file['error'] = $this->checkUploadError($file['error']);

		$post_max_size = $this->getPostMaxSizeBytes();

		if ($post_max_size && ($this->_getServerVars('CONTENT_LENGTH') > $post_max_size))
		{
			$file['error'] = Tools::displayError('The uploaded file exceeds the post_max_size directive in php.ini');
			return false;
		}

		if ($error = ImageManager::validateUpload($file, Tools::getMaxUploadSize($this->getMaxSize()), $this->getAcceptTypes()))
		{
			$file['error'] = $error;
			return false;
		}

		if ($file['size'] > $this->getMaxSize())
		{
			$file['error'] = Tools::displayError('File is too big');
			return false;
		}

		return true;
	}
}