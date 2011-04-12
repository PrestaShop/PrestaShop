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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_CAN_LOAD_FILES_'))
	exit;
	
class BlockAdvertising extends Module
{
	public $adv_link;
	/**
	 * adv_img contains url to the image to display.
	 * 
	 * @var mixed
	 */
	public $adv_img;

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
		$this->version = 0.2;
		$this->author = 'PrestaShop';

		parent::__construct();

		$this->displayName = $this->l('Block advertising');
		$this->description = $this->l('Adds a block to display an advertisement.');

		$current_dir = defined('__DIR__')?__DIR__:dirname(__FILE__);
		if (!file_exists($current_dir.'/'.$this->adv_imgname.'.'.Configuration::get('BLOCKADVERT_IMG_EXT')))
			$this->adv_img = Tools::getMediaServer($this->name)._MODULE_DIR_.$this->name.'/advertising.jpg';
		else
			$this->adv_img = Tools::getMediaServer($this->name)._MODULE_DIR_.$this->name.'/'.$this->adv_imgname.'.'.Configuration::get('BLOCKADVERT_IMG_EXT');
		$this->adv_link = htmlentities(Configuration::get('BLOCKADVERT_LINK'), ENT_QUOTES, 'UTF-8');
	}


	public function install()
	{
		Configuration::updateValue('BLOCKADVERT_LINK', 'http://www.prestashop.com');
		if (!parent::install())
			return false;
		if (!$this->registerHook('leftColumn') OR !$this->registerHook('rightColumn'))
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
		// can work before 5.3
		$current_dir=defined(__DIR__)?__DIR__:dirname(__FILE__);

		if(file_exists($current_dir.'/'.$this->adv_imgname.'.'.Configuration::get('BLOCKADVERT_IMG_EXT')))
			unlink($current_dir.'/'.$this->adv_imgname.'.'.Configuration::get('BLOCKADVERT_IMG_EXT'));
	}
	/**
	 * postProcess update configuration
	 * @TODO adding alt and title attributes for <img> and <a>
	 * @var string
	 * @return void
	 */
	public function postProcess()
	{
		global $currentIndex;

		$errors = '';
		if (Tools::isSubmit('submitDeleteImgConf'))
			$this->_deleteCurrentImg();	

		if (Tools::isSubmit('submitAdvConf'))
		{
			$file = false;
			if (isset($_FILES['adv_img']) AND isset($_FILES['adv_img']['tmp_name']) AND !empty($_FILES['adv_img']['tmp_name']))
			{
				if ($error = checkImage($_FILES['adv_img'], 4000000))
					$errors .= $error;
				elseif ($dot_pos = strrpos($_FILES['adv_img']['name'],'.'))
				{
					// __DIR__ exists since php 5.3
					$current_dir = defined(__DIR__)?__DIR__:dirname(__FILE__);
					// as checkImage tell us it's a good image, we'll just copy the extension
					$ext=substr($_FILES['adv_img']['name'], $dot_pos+1);
					$newname=$this->adv_imgname.'.'.$ext;

					$this->_deleteCurrentImg();

					if (!move_uploaded_file($_FILES['adv_img']['tmp_name'], $current_dir.'/'.$newname))
						$errors .= $this->l('Error move uploaded file');

					Configuration::updateValue('BLOCKADVERT_IMG_EXT',$ext);
					$this->adv_img = Tools::getMediaServer($this->name)._MODULE_DIR_.$this->name.'/'.$this->adv_imgname.'.'.Configuration::get('BLOCKADVERT_IMG_EXT');
				}
			}

			if ($link = Tools::getValue('adv_link'))
			{
				Configuration::updateValue('BLOCKADVERT_LINK', $link);
				$this->adv_link = htmlentities($link, ENT_QUOTES, 'UTF-8');
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
		global $protocol_content;
		
		$this->postProcess();
		$output = '';
		$output .= '
<form action="'.$_SERVER['REQUEST_URI'].'" method="post" enctype="multipart/form-data">
<fieldset><legend>'.$this->l('Advertising block configuration').'</legend>
<a href="'.$this->adv_link.'" target="_blank" title="'.$this->l('Advertising').'">';
		if ($this->adv_img)
			$output .= '<img src="'.$protocol_content.$this->adv_img.'" alt="'.$this->l('Advertising image').'" style="height:163px;margin-left: 100px;width:163px"/>';
		else
			$output .= $this->l('no image');
		$output .= '</a>';
		if ($this->adv_img)
			$output .= '<input class="button" type="submit" name="submitDeleteImgConf" value="'.$this->l('Delete image').'" style=""/>';
		$output .= '<br/>
<br/>
<label for="adv_img">'.$this->l('Change image').'&nbsp;&nbsp;</label><input id="adv_img" type="file" name="adv_img" />
( '.$this->l('Image will be displayed as 155x163').' )
<br/>
<br class="clear"/>
<label for="adv_link">'.$this->l('Image link').'&nbsp;&nbsp;</label><input id="adv_link" type="text" name="adv_link" value="'.$this->adv_link.'" />
<br class="clear"/>
<br/>
<input class="button" type="submit" name="submitAdvConf" value="'.$this->l('validate').'" style="margin-left: 200px;"/>
</fieldset>
</form>
';
		return $output;
	}

	/**
	* Returns module content
	*
	* @param array $params Parameters
	* @return string Content
	*/
	function hookRightColumn($params)
	{
		global $smarty, $protocol_content;

		Tools::addCSS(($this->_path).'blockadvertising.css', 'all');
		$smarty->assign('image', $protocol_content.$this->adv_img);
		$smarty->assign('adv_link', $this->adv_link);

		return $this->display(__FILE__, 'blockadvertising.tpl');
	}

	function hookLeftColumn($params)
	{
		return $this->hookRightColumn($params);
	}

}


