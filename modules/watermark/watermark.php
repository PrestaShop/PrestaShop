<?php
/*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class Watermark extends Module
{
	private $_html = '';
	private $_postErrors = array();
	private $xaligns = array('left', 'middle', 'right');
	private $yaligns = array('top', 'middle', 'bottom');
	private $yAlign;
	private $xAlign;
	private $transparency;
	private $imageTypes = array();
	private	$watermarkTypes;

	public function __construct()
	{
		$this->name = 'watermark';
		$this->tab = 'administration';
		$this->version = '0.3';
		$this->author = 'PrestaShop';
		
		parent::__construct();

		$config = Configuration::getMultiple(array('WATERMARK_TYPES', 'WATERMARK_Y_ALIGN', 'WATERMARK_X_ALIGN', 'WATERMARK_TRANSPARENCY'));
		if (!isset($config['WATERMARK_TYPES']))
			$config['WATERMARK_TYPES'] = '';
		$tmp = explode(',', $config['WATERMARK_TYPES']);
		foreach (ImageType::getImagesTypes('products') as $type)
		    if (in_array($type['id_image_type'], $tmp))
				$this->imageTypes[] = $type;
		
		$this->yAlign = isset($config['WATERMARK_Y_ALIGN']) ? $config['WATERMARK_Y_ALIGN'] : '';
		$this->xAlign = isset($config['WATERMARK_X_ALIGN']) ? $config['WATERMARK_X_ALIGN'] : '';
		$this->transparency = isset($config['WATERMARK_TRANSPARENCY']) ? $config['WATERMARK_TRANSPARENCY'] : 60;

		$this->displayName = $this->l('Watermark');
		$this->description = $this->l('Protect image by watermark.');
		$this->confirmUninstall = $this->l('Are you sure you want to delete your details ?');
		if (!isset($this->transparency) || !isset($this->xAlign) || !isset($this->yAlign))
			$this->warning = $this->l('Watermark image must be uploaded in order for this module to work correctly.');
	}

	public function install()
	{
		$this->writeHtaccessSection();
		if (!parent::install() || !$this->registerHook('watermark'))
			return false;
		Configuration::updateValue('WATERMARK_TRANSPARENCY', 60);
		Configuration::updateValue('WATERMARK_Y_ALIGN', 'bottom');
		Configuration::updateValue('WATERMARK_X_ALIGN', 'right');
		return true;
	}

	public function uninstall()
	{
		$this->removeHtaccessSection();
		return (parent::uninstall()
			&& Configuration::deleteByName('WATERMARK_TYPES')
			&& Configuration::deleteByName('WATERMARK_TRANSPARENCY')
			&& Configuration::deleteByName('WATERMARK_Y_ALIGN')
			&& Configuration::deleteByName('WATERMARK_X_ALIGN'));
	}

	private function _postValidation()
	{
		$yalign = Tools::getValue('yalign');
		$xalign = Tools::getValue('xalign');
		$transparency = (int)(Tools::getValue('transparency'));
		
		$types = ImageType::getImagesTypes('products');
		$id_image_type = array();
		foreach ($types as $type)
			if (!is_null(Tools::getValue('WATERMARK_TYPES_'.(int)$type['id_image_type'])))
				$id_image_type['WATERMARK_TYPES_'.(int)$type['id_image_type']] = true;
		
		if (empty($transparency))
			$this->_postErrors[] = $this->l('Transparency required.');
		elseif ($transparency < 1 || $transparency > 100)
			$this->_postErrors[] = $this->l('Transparency is not in allowed range.');

		if (empty($yalign))
			$this->_postErrors[] = $this->l('Y-Align is required.');
		elseif (!in_array($yalign, $this->yaligns))
			$this->_postErrors[] = $this->l('Y-Align is not in allowed range.');
		
		if (empty($xalign))
			$this->_postErrors[] = $this->l('X-Align is required.');
		elseif (!in_array($xalign, $this->xaligns))
			$this->_postErrors[] = $this->l('X-Align is not in allowed range.');
		if (!count($id_image_type))
			$this->_postErrors[] = $this->l('At least one image type is required.');

		if (isset($_FILES['PS_WATERMARK']['tmp_name']) && !empty($_FILES['PS_WATERMARK']['tmp_name']))
		{
			if (!ImageManager::isRealImage($_FILES['PS_WATERMARK']['tmp_name'], $_FILES['PS_WATERMARK']['type'], array('image/gif')))
				$this->_postErrors[] = $this->l('Image must be in GIF format.');
		}
		
		return !count($this->_postErrors) ? true : false;
	}

	private function _postProcess()
	{
		$types = ImageType::getImagesTypes('products');
		$id_image_type = array();
		foreach ($types as $type)
			if (Tools::getValue('WATERMARK_TYPES_'.(int)$type['id_image_type']))
				$id_image_type[] = $type['id_image_type'];

		Configuration::updateValue('WATERMARK_TYPES', implode(',', $id_image_type));
		Configuration::updateValue('WATERMARK_Y_ALIGN', Tools::getValue('yalign'));
		Configuration::updateValue('WATERMARK_X_ALIGN', Tools::getValue('xalign'));
		Configuration::updateValue('WATERMARK_TRANSPARENCY', Tools::getValue('transparency'));

		if (Shop::getContext() == Shop::CONTEXT_SHOP)
			$str_shop = '-'.(int)$this->context->shop->id;
		else
			$str_shop = '';
		//submited watermark
		if (isset($_FILES['PS_WATERMARK']) && !empty($_FILES['PS_WATERMARK']['tmp_name']))
		{
			/* Check watermark validity */
			if ($error = ImageManager::validateUpload($_FILES['PS_WATERMARK']))
				$this->_errors[] = $error;
			/* Copy new watermark */
			elseif (!copy($_FILES['PS_WATERMARK']['tmp_name'], dirname(__FILE__).'/watermark'.$str_shop.'.gif'))
				$this->_errors[] = sprintf($this->l('An error occurred while uploading watermark: %1$s to %2$s'), $_FILES['PS_WATERMARK']['tmp_name'], dirname(__FILE__).'/watermark'.$str_shop.'.gif');
		}

		if ($this->_errors)
			foreach ($this->_errors as $error)
				$this->_html .= $this->displayError($this->l($error));
		else
			$this->_html .= $this->displayConfirmation($this->l('Settings updated'));
	}
	
	public function getAdminDir()
	{
		$admin_dir = str_replace('\\', '/', _PS_ADMIN_DIR_);
		$admin_dir = explode('/', $admin_dir);
		$len = count($admin_dir);
		return $len > 1 ? $admin_dir[$len - 1] : _PS_ADMIN_DIR_;
	}
	
	public function removeHtaccessSection()
	{
		$key1 = "\n# start ~ module watermark section";
		$key2 = "# end ~ module watermark section\n";
		$path = _PS_ROOT_DIR_ . '/.htaccess';
		if (file_exists($path) && is_writable($path)) {
			$s = file_get_contents($path);
			$p1 = strpos($s, $key1);
			$p2 = strpos($s, $key2, $p1);
			if ($p1 === false || $p2 === false) return false;
			$s = substr($s, 0, $p1) . substr($s, $p2 + strlen($key2));
			file_put_contents($path, $s);
		}
		return true;
	}

	public function writeHtaccessSection()
	{
		$admin_dir = $this->getAdminDir();
		$source = "\n# start ~ module watermark section
Options +FollowSymLinks
RewriteEngine On
RewriteCond expr \"! %{HTTP_REFERER} -strmatch '*://%{HTTP_HOST}*/$admin_dir/*'\"
RewriteRule [0-9/]+/[0-9]+\\.jpg$ - [F]
# end ~ module watermark section\n";

		$path = _PS_ROOT_DIR_ . '/.htaccess';
		file_put_contents($path, $source, FILE_APPEND);
	}

	public function getContent()
	{
		//Modify htaccess to prevent downlaod of original pictures
		$this->removeHtaccessSection();
		$this->writeHtaccessSection();
		
		$this->_html = '';

		if (Tools::isSubmit('btnSubmit'))
		{
			$this->_postValidation();
			if (!count($this->_postErrors))
				$this->_postProcess();
			else
				foreach ($this->_postErrors as $err)
					$this->_html .= $this->displayError($err);
		}

		$this->_html .= $this->renderForm();

		return $this->_html;
	}

	// Retrocompatibility
	public function hookwatermark($params)
	{
		$this->hookActionWatermark($params);
	}

	public function hookActionWatermark($params)
	{
		$image = new Image($params['id_image']);
		$image->id_product = $params['id_product'];
		$file = _PS_PROD_IMG_DIR_.$image->getExistingImgPath().'-watermark.jpg';

		$str_shop = '-'.(int)$this->context->shop->id;
		if (Shop::getContext() != Shop::CONTEXT_SHOP || !Tools::file_exists_cache(dirname(__FILE__).'/watermark'.$str_shop.'.gif'))
			$str_shop = '';

		//first make a watermark image
		$return = $this->watermarkByImage(_PS_PROD_IMG_DIR_.$image->getExistingImgPath().'.jpg', dirname(__FILE__).'/watermark'.$str_shop.'.gif', $file, 23, 0, 0, 'right');

		//go through file formats defined for watermark and resize them
		foreach ($this->imageTypes as $imageType)
		{
			$newFile = _PS_PROD_IMG_DIR_.$image->getExistingImgPath().'-'.stripslashes($imageType['name']).'.jpg';
			if (!ImageManager::resize($file, $newFile, (int)$imageType['width'], (int)$imageType['height']))
				$return = false;
		}
		return $return;
	}

	private function watermarkByImage($imagepath, $watermarkpath, $outputpath)
	{
		$Xoffset = $Yoffset = $xpos = $ypos = 0;
		if (!$image = imagecreatefromjpeg($imagepath))
			return false;
		if (!$imagew = imagecreatefromgif($watermarkpath))
			die ($this->l('The watermark image is not a real gif, please CONVERT the image.'));
		list($watermarkWidth, $watermarkHeight) = getimagesize($watermarkpath); 
		list($imageWidth, $imageHeight) = getimagesize($imagepath); 
		if ($this->xAlign == 'middle')
			$xpos = $imageWidth / 2 - $watermarkWidth / 2 + $Xoffset;
		if ($this->xAlign == 'left')
			$xpos = 0 + $Xoffset;
		if ($this->xAlign == 'right')
			$xpos = $imageWidth - $watermarkWidth - $Xoffset;
		if ($this->yAlign == 'middle')
			$ypos = $imageHeight / 2 - $watermarkHeight / 2 + $Yoffset;
		if ($this->yAlign == 'top')
			$ypos = 0 + $Yoffset;
		if ($this->yAlign == 'bottom')
			$ypos = $imageHeight - $watermarkHeight - $Yoffset;
		if (!imagecopymerge($image, $imagew, $xpos, $ypos, 0, 0, $watermarkWidth, $watermarkHeight, $this->transparency))
			return false;
		return imagejpeg($image, $outputpath, 100); 
	}
	
	public function renderForm()
	{
		$types = ImageType::getImagesTypes('products');
		foreach ($types as $key => $type)
			$types[$key]['label'] =  $type['name'].' ('.$type['width'].' x '.$type['height'].')';

		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs'
				),
				'description' => $this->l('Once you have set up the module, regenerate the images using the "Images" tool in Preferences. However, the watermark will be added automatically to new images.'),
				'input' => array(
					array(
						'type' => 'file',
						'label' => $this->l('Watermark file:'),
						'name' => 'PS_WATERMARK',
						'desc' => $this->l('Must be in GIF format'),
						'thumb' => '../modules/'.$this->name.'/'.$this->name.'.gif?t='.rand(0, time()),
					),
					array(
						'type' => 'text',
						'label' => $this->l('Watermark transparency (0-100)'),
						'name' => 'transparency',
						'class' => 'fixed-width-md',
					),
					array(
						'type' => 'select',
						'label' => $this->l('Watermark X align:'),
						'name' => 'xalign',
						'class' => 'fixed-width-md',
						'options' => array(
							'query' => array(
								array(
									'id' => 'left',
									'name' => $this->l('left')
								),
								array(
									'id' => 'middle',
									'name' => $this->l('middle')
								),
								array(
									'id' => 'right',
									'name' => $this->l('right')
								)
							),
							'id' => 'id',
							'name' => 'name',
						)
					),
					array(
						'type' => 'select',
						'label' => $this->l('Watermark X align:'),
						'name' => 'yalign',
						'class' => 'fixed-width-md',
						'options' => array(
							'query' => array(
								array(
									'id' => 'top',
									'name' => $this->l('top')
								),
								array(
									'id' => 'middle',
									'name' => $this->l('middle')
								),
								array(
									'id' => 'bottom',
									'name' => $this->l('bottom')
								)
							),
							'id' => 'id',
							'name' => 'name',
						)
					),
					array(
						'type' => 'checkbox',
						'name' => 'WATERMARK_TYPES',
						'label' => $this->l('Choose image types for watermark protection:'),
						'values' => array(
							'query' => $types,
							'id' => 'id_image_type',
							'name' => 'label'
						)
					),
				),
				'submit' => array(
					'title' => $this->l('Save'),
					'class' => 'btn btn-default')
			),
		);

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table =  $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'btnSubmit';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm(array($fields_form));
	}
	
	public function getConfigFieldsValues()
	{
		$config_fields = array(
			'PS_WATERMARK' => Tools::getValue('PS_WATERMARK', Configuration::get('PS_WATERMARK')),
			'transparency' => Tools::getValue('transparency', Configuration::get('WATERMARK_TRANSPARENCY')),
			'xalign' => Tools::getValue('xalign', Configuration::get('WATERMARK_X_ALIGN')),
			'yalign' => Tools::getValue('yalign', Configuration::get('WATERMARK_Y_ALIGN')),
		);
		//get all images type available 
		$types = ImageType::getImagesTypes('products');
		$id_image_type = array();
		foreach ($types as $type)
			$id_image_type[] = $type['id_image_type'];
		
		//get images type from $_POST
		$id_image_type_post = array();
		foreach ($id_image_type as $id)
			if (Tools::getValue('WATERMARK_TYPES_'.(int)$id))
				$id_image_type_post['WATERMARK_TYPES_'.(int)$id] = true;

		//get images type from Configuration
		$id_image_type_config = array();
		if ($confs = Configuration::get('WATERMARK_TYPES'))
			$confs = explode(',', Configuration::get('WATERMARK_TYPES'));
		else
			$confs = array();

		foreach ($confs as $conf)
			$id_image_type_config['WATERMARK_TYPES_'.(int)$conf] = true;
		
		//return only common values and value from post
		if (Tools::isSubmit('btnSubmit'))
			$config_fields = array_merge($config_fields, array_intersect($id_image_type_post, $id_image_type_config));
		else
			$config_fields = array_merge($config_fields, $id_image_type_config);
		
		return $config_fields;
	}
}