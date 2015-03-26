<?php
/*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class HelperImageUploaderCore extends HelperUploader
{
	/**
	 * Get Max Upload Size
	 * @return int
	 */
	public function getMaxSize()
	{
		return (int)Tools::getMaxUploadSize();
	}

	/**
	 * Get path where Image will be saved
	 * @return string
	 */
	public function getSavePath()
	{
		return $this->_normalizeDirectory(_PS_TMP_IMG_DIR_);
	}

	/**
	 * Get Image location
	 * @param null $file_name
	 * @return string
	 */
	public function getFilePath($file_name = null)
	{
		//Force file path
		return tempnam($this->getSavePath(), $this->getUniqueFileName());
	}

	/**
	 * Validate Image Uploaded
	 * @param $file
	 * @return bool
	 */
	protected function validate(&$file)
	{
		$file['error'] = $this->checkUploadError($file['error']);

		$post_max_size = Tools::convertBytes(ini_get('post_max_size'));

		$upload_max_filesize = Tools::convertBytes(ini_get('upload_max_filesize'));

		if ($post_max_size && ($this->_getServerVars('CONTENT_LENGTH') > $post_max_size))
		{
			$file['error'] = Tools::displayError('The uploaded file exceeds the post_max_size directive in php.ini');
			return false;
		}

		if ($upload_max_filesize && ($this->_getServerVars('CONTENT_LENGTH') > $upload_max_filesize))
		{
			$file['error'] = Tools::displayError('The uploaded file exceeds the upload_max_filesize directive in php.ini');
			return false;
		}

		if ($error = ImageManager::validateUpload($file, Tools::getMaxUploadSize($this->getMaxSize()), $this->getAcceptTypes()))
		{
			$file['error'] = $error;
			return false;
		}

		if ($file['size'] > $this->getMaxSize())
		{
			$file['error'] = sprintf(Tools::displayError('File (size : %1s) is too big (max : %2s)'), $file['size'], $this->getMaxSize());
			return false;
		}

		return true;
	}

	/**
	 * Get an array of files with their associated links (if any)
	 * @return array
	 */
	public function getFilesLinks()
	{
		$files = $this->getFiles();
		$files_links = array();

		foreach ($files as $file)
		{
			if (isset($file['is_linkable']) && isset($file['assoc_table']))
			{
				$db_filename = Tools::str_replace_once('.', '_', (string)$file['filename']);
				$file_link = CategoryThumbLink::getLinkFromFilename($db_filename);
				if (!is_null($file_link))
					$files_links[(string)$file['filename']] = (string)$file_link['link'];
			}
		}
		return $files_links;
	}


	/**
	 * Render ImageUploader form
	 * @return Smarty Template
	 */
	public function render()
	{
		$admin_webpath = str_ireplace(_PS_CORE_DIR_, '', _PS_ADMIN_DIR_);
		$admin_webpath = preg_replace('/^'.preg_quote(DIRECTORY_SEPARATOR, '/').'/', '', $admin_webpath);
		$bo_theme = ((Validate::isLoadedObject($this->getContext()->employee)
			&& $this->getContext()->employee->bo_theme) ? $this->getContext()->employee->bo_theme : 'default');

		if (!file_exists(_PS_BO_ALL_THEMES_DIR_.$bo_theme.DIRECTORY_SEPARATOR
			.'template'))
			$bo_theme = 'default';

		$this->getContext()->controller->addJs(__PS_BASE_URI__.$admin_webpath
			.'/themes/'.$bo_theme.'/js/jquery.iframe-transport.js');
		$this->getContext()->controller->addJs(__PS_BASE_URI__.$admin_webpath
			.'/themes/'.$bo_theme.'/js/jquery.fileupload.js');
		$this->getContext()->controller->addJs(__PS_BASE_URI__.$admin_webpath
			.'/themes/'.$bo_theme.'/js/jquery.fileupload-process.js');
		$this->getContext()->controller->addJs(__PS_BASE_URI__.$admin_webpath
			.'/themes/'.$bo_theme.'/js/jquery.fileupload-validate.js');
		$this->getContext()->controller->addJs(__PS_BASE_URI__.'js/vendor/spin.js');
		$this->getContext()->controller->addJs(__PS_BASE_URI__.'js/vendor/ladda.js');

		if ($this->useAjax() && !isset($this->_template))
			$this->setTemplate(self::DEFAULT_AJAX_TEMPLATE);

		$template = $this->getContext()->smarty->createTemplate(
			$this->getTemplateFile($this->getTemplate()), $this->getContext()->smarty
		);

		$template->assign(array(
			'id'            => $this->getId(),
			'name'          => $this->getName(),
			'url'           => $this->getUrl(),
			'multiple'      => $this->isMultiple(),
			'files'         => $this->getFiles(),
			'files_links'   => $this->getFilesLinks(),
			'title'         => $this->getTitle(),
			'max_files'     => $this->getMaxFiles(),
			'post_max_size' => $this->getPostMaxSizeBytes(),
			'drop_zone'     => $this->getDropZone()
		));

		return $template->fetch();
	}
}
