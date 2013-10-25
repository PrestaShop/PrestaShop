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

class HelperUploaderCore extends Uploader
{
	const DEFAULT_TEMPLATE_DIRECTORY = 'helpers/uploader';
	const DEFAULT_TEMPLATE           = 'simple.tpl';
	const DEFAULT_AJAX_TEMPLATE      = 'ajax.tpl';

	private   $_context;
	private   $_delete_url;
	private   $_display_image;
	private   $_id;
	private   $_image;
	private   $_name;
	private   $_multiple;
	private   $_size;
	protected $_template;
	private   $_template_directory;
	private   $_title;
	private   $_thumb;
	private   $_url;
	private   $_use_ajax;

	public function setContext($value)
	{
		$this->_context = $value;
		return $this;
	}

	public function getContext()
	{
		if (!isset($this->_context))
			$this->_context = Context::getContext();

		return $this->_context;
	}

	public function setDeleteUrl($value)
	{
		$this->_delete_url = $value;
		return $this;
	}

	public function getDeleteUrl()
	{
		return $this->_delete_url;
	}

	public function setDisplayImage($value)
	{
		$this->_display_image = $value;
		return $this;
	}

	public function setId($value)
	{
		$this->_id = (string)$value;
		return $this;
	}

	public function getId()
	{
		if (!isset($this->_id) || trim($this->_id) === '')
			$this->_id = $this->getName();

		return $this->_id;
	}

	public function setImage($value)
	{
		$this->_image = $value;
		return $this;
	}

	public function getImage()
	{
		return $this->_image;
	}

	public function setName($value)
	{
		$this->_name = (string)$value;
		return $this;
	}

	public function getName()
	{
		return $this->_name;
	}

	public function setMultiple($value)
	{
		$this->_multiple = (bool)$value;
		return $this;
	}

	public function setSize($value)
	{
		$this->_size = $value;
		return $this;
	}

	public function getSize()
	{
		return $this->_size;
	}

	public function setTemplate($value)
	{
		$this->_template = $value;
		return $this;
	}

	public function getTemplate()
	{
		if (!isset($this->_template))
			$this->setTemplate(self::DEFAULT_TEMPLATE);

		return $this->_template;
	}

	public function setTemplateDirectory($value)
	{
		$this->_template_directory = $value;
		return $this;
	}

	public function getTemplateDirectory()
	{
		if (!isset($this->_template_directory))
			$this->_template_directory = self::DEFAULT_TEMPLATE_DIRECTORY;

		return $this->_normalizeDirectory($this->_template_directory);
	}

	public function getTemplateFile($template)
	{
		if (preg_match_all('/((?:^|[A-Z])[a-z]+)/', get_class($this->getContext()->controller), $matches) !== FALSE)
			$controllerName = strtolower($matches[0][1]);

		if ($this->getContext()->controller instanceof ModuleAdminController)
			return $this->_normalizeDirectory(
				$this->getContext()->controller->getTemplatePath())
				.$this->getTemplateDirectory().$template;
		else if ($this->getContext()->controller instanceof AdminController
			&& isset($controllerName) && file_exists($this->_normalizeDirectory(
				$this->getContext()->smarty->getTemplateDir(0)).'controllers'
				.DIRECTORY_SEPARATOR
				.$controllerName
				.DIRECTORY_SEPARATOR
				.$this->getTemplateDirectory().$template))
			return $this->_normalizeDirectory(
				$this->getContext()->smarty->getTemplateDir(0)).'controllers'
				.DIRECTORY_SEPARATOR
				.$controllerName
				.DIRECTORY_SEPARATOR
				.$this->getTemplateDirectory().$template;
		else if (file_exists($this->_normalizeDirectory(
				$this->getContext()->smarty->getTemplateDir(1))
				.$this->getTemplateDirectory().$template))
				return $this->_normalizeDirectory(
					$this->getContext()->smarty->getTemplateDir(1))
					.$this->getTemplateDirectory().$template;
		else if (file_exists($this->_normalizeDirectory(
				$this->getContext()->smarty->getTemplateDir(0))
				.$this->getTemplateDirectory().$template))
				return $this->_normalizeDirectory(
				$this->getContext()->smarty->getTemplateDir(0))
				.$this->getTemplateDirectory().$template;
		else
			return $this->getTemplateDirectory().$template;
	}

	public function setTitle($value)
	{
		$this->_title = $value;
		return $this;
	}

	public function getTitle()
	{
		return $this->_title;
	}

	public function setThumb($value)
	{
		$this->_thumb = $value;
		return $this;
	}

	public function getThumb()
	{
		return $this->_thumb;
	}

	public function setUrl($value)
	{
		$this->_url = (string)$value;
		return $this;
	}

	public function getUrl()
	{
		return $this->_url;
	}

	public function setUseAjax($value)
	{
		$this->_use_ajax = (bool)$value;
		return $this;
	}

	public function displayImage()
	{
		return (isset($this->_display_image) && $this->_display_image);
	}

	public function isMultiple()
	{
		return (isset($this->_multiple) && $this->_multiple);
	}

	public function process()
	{
		$files = parent::process();
		die(Tools::jsonEncode(array($this->getName() => $files)));
	}

	public function render()
	{
		$admin_webpath = str_ireplace(_PS_ROOT_DIR_, '', _PS_ADMIN_DIR_);
		$admin_webpath = preg_replace('/^'.preg_quote(DIRECTORY_SEPARATOR, '/').'/', '', $admin_webpath);
		$bo_theme = ((Validate::isLoadedObject($this->getContext()->employee)
			&& $this->getContext()->employee->bo_theme) ? $this->getContext()->employee->bo_theme : 'default');

		if (!file_exists(_PS_BO_ALL_THEMES_DIR_.$bo_theme.DIRECTORY_SEPARATOR
			.'template'))
			$bo_theme = 'default';

		if ($this->getContext()->controller->ajax)
		{
			$html = '<script type="text/javascript" src="'.__PS_BASE_URI__.$admin_webpath
				.'/themes/'.$bo_theme.'/js/vendor/jquery.ui.widget.js"></script>';
			$html .= '<script type="text/javascript" src="'.__PS_BASE_URI__.$admin_webpath
				.'/themes/'.$bo_theme.'/js/jquery.iframe-transport.js"></script>';
				$html .= '<script type="text/javascript" src="'.__PS_BASE_URI__.$admin_webpath
				.'/themes/'.$bo_theme.'/js/jquery.fileupload.js"></script>';
		}
		else
		{
			$html = '';
			$this->getContext()->controller->addJs(__PS_BASE_URI__.$admin_webpath
				.'/themes/'.$bo_theme.'/js/vendor/jquery.ui.widget.js');
			//$context->controller->addJs('http://blueimp.github.io/JavaScript-Load-Image/js/load-image.min.js');
			$this->getContext()->controller->addJs(__PS_BASE_URI__.$admin_webpath
				.'/themes/'.$bo_theme.'/js/jquery.iframe-transport.js');
			$this->getContext()->controller->addJs(__PS_BASE_URI__.$admin_webpath
				.'/themes/'.$bo_theme.'/js/jquery.fileupload.js');
			/*$context->controller->addJs(__PS_BASE_URI__.$admin_webpath
				.'/themes/'.$bo_theme.'/js/jquery.fileupload-image.js');*/

			$this->getContext()->controller->addJs(__PS_BASE_URI__.'/js/vendor/spin.js');
			$this->getContext()->controller->addJs(__PS_BASE_URI__.'/js/vendor/ladda.js');

		}

		if ($this->useAjax())
			$this->setTemplate(self::DEFAULT_AJAX_TEMPLATE);

		$template = $this->getContext()->smarty->createTemplate(
			$this->getTemplateFile($this->getTemplate()), $this->getContext()->smarty
		);

		$template->assign(array(
			'id'            => $this->getId(),
			'name'          => $this->getName(),
			'url'           => $this->getUrl(),
			'multiple'      => $this->isMultiple(),
			'display_image' => $this->displayImage(),
			'image'         => $this->getImage(),
			'thumb'         => $this->getThumb(),
			'size'          => $this->getSize(),
			'delete_url'    => $this->getDeleteUrl(),
			'title'         => $this->getTitle()
		));

		$html .= $template->fetch();
		return $html;
	}

	public function useAjax()
	{
		return (isset($this->_use_ajax) && $this->_use_ajax);
	}
}
