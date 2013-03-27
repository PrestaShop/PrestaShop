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

class BlockAdvertising extends Module
{
	/* Title associated to the image */
	public $adv_title;
	
	/* Link associated to the image */
	public $adv_link;
	
	/* Name of the image without extension */
	public $adv_imgname;
	
	/* Image path with extension */
	public $adv_img;

	public function __construct()
	{
		$this->name = 'blockadvertising';
		$this->tab = 'advertising_marketing';
		$this->version = '0.5';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Block advertising');
		$this->description = $this->l('Adds an advertisement block to selected sections of your e-commerce webiste.');
		
		$this->initialize();
	}

	/*
	 * Set the properties of the module, like the link to the image and the title (contextual to the current shop context)
	 */
	protected function initialize()
	{
		$this->adv_imgname = 'advertising';
		if ((Shop::getContext() == Shop::CONTEXT_GROUP  || Shop::getContext() == Shop::CONTEXT_SHOP)
			&& file_exists(_PS_MODULE_DIR_.$this->name.'/'.$this->adv_imgname.'-g'.$this->context->shop->getContextShopGroupID().'.'.Configuration::get('BLOCKADVERT_IMG_EXT')))
			$this->adv_imgname .= '-g'.$this->context->shop->getContextShopGroupID();
		if (Shop::getContext() == Shop::CONTEXT_SHOP
			&& file_exists(_PS_MODULE_DIR_.$this->name.'/'.$this->adv_imgname.'-s'.$this->context->shop->getContextShopID().'.'.Configuration::get('BLOCKADVERT_IMG_EXT')))
			$this->adv_imgname .= '-s'.$this->context->shop->getContextShopID();

		$this->adv_img = Tools::getMediaServer($this->name)._MODULE_DIR_.$this->name.'/'.$this->adv_imgname.'.'.Configuration::get('BLOCKADVERT_IMG_EXT');
		$this->adv_link = htmlentities(Configuration::get('BLOCKADVERT_LINK'), ENT_QUOTES, 'UTF-8');
		$this->adv_title = htmlentities(Configuration::get('BLOCKADVERT_TITLE'), ENT_QUOTES, 'UTF-8');
	}
	
	public function install()
	{
		Configuration::updateGlobalValue('BLOCKADVERT_LINK', 'http://www.prestashop.com/');
		Configuration::updateGlobalValue('BLOCKADVERT_TITLE', 'PrestaShop');
		// Try to update with the extension of the image that exists in the module directory
		foreach (scandir(_PS_MODULE_DIR_.$this->name) as $file)
			if (in_array($file, array('advertising.jpg', 'advertising.gif', 'advertising.png')))
				Configuration::updateGlobalValue('BLOCKADVERT_IMG_EXT', substr($file, strrpos($file, '.') + 1));

		return (parent::install() && $this->registerHook('leftColumn'));
	}
	
	public function uninstall()
	{
		Configuration::deleteByName('BLOCKADVERT_LINK');
		Configuration::deleteByName('BLOCKADVERT_TITLE');
		Configuration::deleteByName('BLOCKADVERT_IMG_EXT');
		return (parent::uninstall());
	}

	/**
	 * delete the contextual image (it is not allowed to delete the default image)
	 *
	 * @return void
	 */
	private function _deleteCurrentImg()
	{
		// Delete the image file
		if ($this->adv_imgname != 'advertising' && file_exists(_PS_MODULE_DIR_.$this->name.'/'.$this->adv_imgname.'.'.Configuration::get('BLOCKADVERT_IMG_EXT')))
			unlink(_PS_MODULE_DIR_.$this->name.'/'.$this->adv_imgname.'.'.Configuration::get('BLOCKADVERT_IMG_EXT'));
		
		// Update the extension to the global value or the shop group value if available
		Configuration::deleteFromContext('BLOCKADVERT_IMG_EXT');
		Configuration::updateValue('BLOCKADVERT_IMG_EXT', Configuration::get('BLOCKADVERT_IMG_EXT'));

		// Reset the properties of the module
		$this->initialize();
	}

	public function postProcess()
	{
		if (Tools::isSubmit('submitDeleteImgConf'))
			$this->_deleteCurrentImg();

		$errors = '';
		if (Tools::isSubmit('submitAdvConf'))
		{
			if (isset($_FILES['adv_img']) && isset($_FILES['adv_img']['tmp_name']) && !empty($_FILES['adv_img']['tmp_name']))
			{
				if ($error = ImageManager::validateUpload($_FILES['adv_img'], Tools::convertBytes(ini_get('upload_max_filesize'))))
					$errors .= $error;
				else
				{
					Configuration::updateValue('BLOCKADVERT_IMG_EXT', substr($_FILES['adv_img']['name'], strrpos($_FILES['adv_img']['name'], '.') + 1));

					// Set the image name with a name contextual to the shop context
					$this->adv_imgname = 'advertising';
					if (Shop::getContext() == Shop::CONTEXT_GROUP)
						$this->adv_imgname = 'advertising'.'-g'.(int)$this->context->shop->getContextShopGroupID();
					elseif (Shop::getContext() == Shop::CONTEXT_SHOP)
						$this->adv_imgname = 'advertising'.'-s'.(int)$this->context->shop->getContextShopID();

					// Copy the image in the module directory with its new name
					if (!move_uploaded_file($_FILES['adv_img']['tmp_name'], _PS_MODULE_DIR_.$this->name.'/'.$this->adv_imgname.'.'.Configuration::get('BLOCKADVERT_IMG_EXT')))
						$errors .= $this->l('File upload error.');
				}
			}
			
			// If the link is not set, then delete it in order to use the next default value (either the global value or the group value)
			if ($link = Tools::getValue('adv_link'))
				Configuration::updateValue('BLOCKADVERT_LINK', $link);
			elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP)
				Configuration::deleteFromContext('BLOCKADVERT_LINK');
				
			// If the title is not set, then delete it in order to use the next default value (either the global value or the group value)
			if ($title = Tools::getValue('adv_title'))
				Configuration::updateValue('BLOCKADVERT_TITLE', $title);
			elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP)
				Configuration::deleteFromContext('BLOCKADVERT_TITLE');
			
			// Reset the module properties
			$this->initialize();
			$this->_clearCache('blockadvertising.tpl');
		}
		if ($errors)
			echo $this->displayError($errors);
	}

	/**
	 * getContent used to display admin module form
	 *
	 * @return string content
	 */
	public function getContent()
	{
		$this->postProcess();
		$output = '
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post" enctype="multipart/form-data">
			<fieldset>
				<legend>'.$this->l('Advertising block configuration').'</legend>';
		if ($this->adv_img)
		{
			$output .= '
			<a href="'.$this->adv_link.'" target="_blank" title="'.$this->adv_title.'">
				<img src="'.$this->context->link->protocol_content.$this->adv_img.'" alt="'.$this->adv_title.'" title="'.$this->adv_title.'"
					style="width:155px;height:163px;margin-left:100px"/>
			</a>';
			if ($this->adv_imgname == 'advertising')
				$output .= $this->l('You cannot delete the default image (but you can change it below).');
			else
				$output .= '<input class="button" type="submit" name="submitDeleteImgConf" value="'.$this->l('Delete image').'" style=""/>';
		}
		else
			$output .= '<div style="margin-left: 100px;width:163px;">'.$this->l('No image').'</div>';
		$output .= '<br/><br/>
				<label for="adv_img">'.$this->l('Change image').'&nbsp;&nbsp;</label>
				<div class="margin-form">
					<input id="adv_img" type="file" name="adv_img" />
					<p>'.$this->l('Image will be displayed as 155x163').'</p>
				</div>
				<br class="clear"/>
				<label for="adv_link">'.$this->l('Image link').'</label>
				<div class="margin-form">
					<input id="adv_link" type="text" name="adv_link" value="'.$this->adv_link.'" style="width:250px" />
				</div>
				<br class="clear"/>
				<label for="adv_title">'.$this->l('Title').'</label>
				<div class="margin-form">
					<input id="adv_title" type="text" name="adv_title" value="'.$this->adv_title.'" style="width:250px" />
				</div>
				<br class="clear"/>
				<div class="margin-form">
					<input class="button" type="submit" name="submitAdvConf" value="'.$this->l('Validate').'"/>
				</div>
				<br class="clear"/>
			</fieldset>
		</form>';
		return $output;
	}

	public function hookRightColumn($params)
	{
		if (!$this->isCached('blockadvertising.tpl', $this->getCacheId()))
			$this->smarty->assign(array(
				'image' => $this->context->link->protocol_content.$this->adv_img,
				'adv_link' => $this->adv_link,
				'adv_title' => $this->adv_title,
			));

		return $this->display(__FILE__, 'blockadvertising.tpl', $this->getCacheId());
	}

	public function hookLeftColumn($params)
	{
		return $this->hookRightColumn($params);
	}

	public function hookHeader($params)
	{
		$this->context->controller->addCSS($this->_path.'blockadvertising.css', 'all');
	}
}