<?php
/*
* 2007-2011 PrestaShop
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 7040 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class BlockAdvertising extends Module
{
	public $adv_link;

	/**
	 * adv_title contains string for title and alt attributes
	 *
	 * @var mixed
	 */
	public $adv_title;
	/**
	 * adv_img contains url to the image to display.
	 *
	 * @var mixed
	 */
	public $adv_img;
	
	public $use_global = true;

	/**
	 * adv_imgname is the filename of the image to display
	 * @TODO make it configurable for SEO, but need a function to clean filename
	 * @var string
	 */
	public $adv_imgname = 'advertising_custom';

	public function __construct()
	{
		$this->name = 'blockadvertising';
		$this->tab = 'advertising_marketing';
		$this->version = '0.4';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Block advertising');
		$this->description = $this->l('Adds a block to display an advertisement.');
		
		if (Shop::getContext() == Shop::CONTEXT_SHOP)
		{	
			if (defined('_PS_ADMIN_DIR_') || file_exists(_PS_MODULE_DIR_.$this->name.'/'.$this->adv_imgname.'-'.(int)$this->context->shop->id.'.'.Configuration::get('BLOCKADVERT_IMG_EXT')))
			{
				$this->adv_imgname .= '-'.(int)$this->context->shop->id;
				$this->use_global = false;
				$advert_link = Configuration::get('BLOCKADVERT_LINK');
				$advert_title = Configuration::get('BLOCKADVERT_TITLE');
			}
			else
				$this->use_global = true;
		}

		if ($this->use_global)
		{
			$advert_link = Configuration::getGlobalValue('BLOCKADVERT_LINK');
			$advert_title = Configuration::getGlobalValue('BLOCKADVERT_TITLE');
		}
		
		if (!file_exists(_PS_MODULE_DIR_.$this->name.'/'.$this->adv_imgname.'.'.Configuration::get('BLOCKADVERT_IMG_EXT')))
		{
			$this->adv_imgname = 'advertising';
			if (!file_exists(_PS_MODULE_DIR_.$this->name.'/advertising.jpg'))
				$this->adv_imgname = '';
			else
				Configuration::updateValue('BLOCKADVERT_IMG_EXT', 'jpg');
		}

		if (!empty($this->adv_imgname))
			$this->adv_img = Tools::getMediaServer($this->name)._MODULE_DIR_.$this->name.'/'.$this->adv_imgname.'.'.Configuration::get('BLOCKADVERT_IMG_EXT');

		$this->adv_link = htmlentities($advert_link, ENT_QUOTES, 'UTF-8');
		$this->adv_title = htmlentities($advert_title, ENT_QUOTES, 'UTF-8');
	}


	public function install()
	{
		Configuration::updateValue('BLOCKADVERT_LINK', 'http://www.prestashop.com');
		if (!parent::install())
			return false;
		if (!$this->registerHook('leftColumn'))
			return false;
		return true;
	}

	/**
	 * _deleteCurrentImg delete current image, (so this will use default image)
	 *
	 * @return void
	 */
	private function _deleteCurrentImg()
	{
		if ($this->adv_imgname == 'advertising')
			return;
		if (file_exists(_PS_MODULE_DIR_.$this->name.'/'.$this->adv_imgname.'.'.Configuration::get('BLOCKADVERT_IMG_EXT')))
			unlink(_PS_MODULE_DIR_.$this->name.'/'.$this->adv_imgname.'.'.Configuration::get('BLOCKADVERT_IMG_EXT'));
		$this->adv_imgname = $this->adv_imgname == 'advertising_custom'?'advertising':'';
	}
	/**
	 * postProcess update configuration
	 * @TODO adding alt and title attributes for <img> and <a>
	 * @var string
	 * @return void
	 */
	public function postProcess()
	{
		$errors = '';
		if (Tools::isSubmit('submitDeleteImgConf'))
			$this->_deleteCurrentImg();

		if (Tools::isSubmit('submitAdvConf'))
		{
			if (isset($_FILES['adv_img']) && isset($_FILES['adv_img']['tmp_name']) && !empty($_FILES['adv_img']['tmp_name']))
			{
				if ($error = ImageManager::validateUpload($_FILES['adv_img'], Tools::convertBytes(ini_get('upload_max_filesize'))))
					$errors .= $error;
				elseif ($dot_pos = strrpos($_FILES['adv_img']['name'], '.'))
				{
					// as checkImage tell us it's a good image, we'll just copy the extension

					$this->_deleteCurrentImg();
					$this->adv_imgname = 'advertising';
					$ext = substr($_FILES['adv_img']['name'], $dot_pos + 1);
					$newname = 'advertising_custom'.'-'.(int)$this->context->shop->id;
					if (!move_uploaded_file($_FILES['adv_img']['tmp_name'], _PS_MODULE_DIR_.$this->name.'/'.$newname.'.'.$ext))
						$errors .= $this->l('Error move uploaded file');
					else
						$this->adv_imgname = $newname;

					Configuration::updateValue('BLOCKADVERT_IMG_EXT', $ext);
					$this->adv_img = Tools::getMediaServer($this->name)._MODULE_DIR_.$this->name.'/'.$this->adv_imgname.'.'.Configuration::get('BLOCKADVERT_IMG_EXT');
				}
			}
			if ($link = Tools::getValue('adv_link'))
			{
				Configuration::updateValue('BLOCKADVERT_LINK', $link);
				$this->adv_link = htmlentities($link, ENT_QUOTES, 'UTF-8');
			}

			if ($title = Tools::getValue('adv_title'))
			{
				Configuration::updateValue('BLOCKADVERT_TITLE', $title);
				$this->adv_title = htmlentities($title, ENT_QUOTES, 'UTF-8');
			}
		}
		if ($errors)
			echo $this->displayError($errors);
	}

	/**
	 * getContent used to display admin module form
	 *
	 * @return void
	 */
	public function getContent()
	{
		$this->postProcess();
		$output = '<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post" enctype="multipart/form-data">
							<fieldset>
								<legend>'.$this->l('Advertising block configuration').'</legend>
								<a href="'.$this->adv_link.'" target="_blank" title="'.$this->adv_title.'">';
								if ($this->adv_img)
								{
									$output .= '<img src="'.$this->context->link->protocol_content.$this->adv_img.'" alt="'.$this->adv_title.'" title="'.$this->adv_title.'" style="height:163px;margin-left: 100px;width:163px"/>';
									$output .= '<input class="button" type="submit" name="submitDeleteImgConf" value="'.$this->l('Delete image').'" style=""/>';
								}
								else
									$output .= '<div style="margin-left: 100px;width:163px;"/>'.$this->l('no image').'</div>';
								$output .= '
								</a>
								<br/>
								<br/>
								<label for="adv_img">'.$this->l('Change image').'&nbsp;&nbsp;</label><input id="adv_img" type="file" name="adv_img" />
								( '.$this->l('Image will be displayed as 155x163').' )
								<br/>
								<br class="clear"/>
								<label for="adv_link">'.$this->l('Image link').'&nbsp;&nbsp;</label><input id="adv_link" type="text" name="adv_link" value="'.$this->adv_link.'" />
								<br class="clear"/>
								<br/>
								<label for="adv_title">'.$this->l('Title').'&nbsp;&nbsp;</label><input id="adv_title" type="text" name="adv_title" value="'.$this->adv_title.'" />
								<br class="clear"/>
								<br/>
								<input class="button" type="submit" name="submitAdvConf" value="'.$this->l('validate').'" style="margin-left: 200px;"/>
							</fieldset>
						</form>';
		return $output;
	}

	/**
	* Returns module content
	*
	* @param array $params Parameters
	* @return string Content
	*/
	public function hookRightColumn($params)
	{
		$this->smarty->assign(array(
			'image' =>		$this->context->link->protocol_content.$this->adv_img,
			'adv_link' =>	$this->adv_link,
			'adv_title' =>	$this->adv_title,
		));

		return $this->display(__FILE__, 'blockadvertising.tpl');
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


