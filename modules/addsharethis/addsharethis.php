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

if (!defined('_PS_VERSION_'))
    exit;

class AddShareThis extends Module
{
	public function __construct()
	{
		$this->name = 'addsharethis';
		$this->author = 'Custom';
		$this->tab = 'front_office_features';
		$this->need_instance = 0;
		$this->_directory = dirname(__FILE__);
		parent::__construct();	
		$this->displayName = $this->l('Add Sharethis');
		$this->description = $this->l('Display social count button on the home page');
	}

	public function install()
	{
		return (
			parent::install() &&
			$this->registerHook('Extraright') &&
			$this->registerHook('header') &&
			Configuration::updateValue('CONF_ROW', 'ea22d519-9f98-4018-99a9-5b5f1b100fa8') &&
			Configuration::updateValue('ADDTHISSHARE_TWITTER', 1) &&
			Configuration::updateValue('ADDTHISSHARE_GOOGLE', 1) &&
			Configuration::updateValue('ADDTHISSHARE_PINTEREST', 1) &&
			Configuration::updateValue('ADDTHISSHARE_FACEBOOK', 1)
		);
	}

	public function uninstall()
	{
		return (
			parent::uninstall() &&
			Configuration::deleteByName('CONF_ROW') &&
			Configuration::deleteByName('ADDTHISSHARE_TWITTER') &&
			Configuration::deleteByName('ADDTHISSHARE_GOOGLE') &&
			Configuration::deleteByName('ADDTHISSHARE_PINTEREST') &&
			Configuration::deleteByName('ADDTHISSHARE_FACEBOOK') &&
			$this->unregisterHook('Extraright') &&
			$this->unregisterHook('header')
		);
	}
	
	public function getContent()
	{
		$this->_html .= '<h2>'.$this->displayName.'<span style=" float:right;"></span></h2><div class="clear"></div>';
		if (Tools::isSubmit('submitCog'))
		{
			$this->updateCog();
			Configuration::updateValue('CONF_ROW', Tools::getValue('conf_row'));
		}

		$this->_html .= '
		<fieldset class="space" id="cogField">
			<legend><img src="'.$this->_path.'logo.png" alt="" title="" /> '.$this->l('Configuration').'</legend>
			<form id="cogForm" name="cogForm" method="post" action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'">
			<br/>
			<em>'.$this->l('(Key in your account statistic http://sharethis.com)').'</em>
				<div class="clearfix"></div><br/><br/>
				<label>'.$this->l('Publisher Pub Key:').'</label>
				<div class="margin-form">
					<input type="text" name="conf_row" value="1" id="conf_row" size="60" value="'.Tools::safeOutput(Tools::getValue('conf_row', Configuration::get('CONF_ROW'))).'" />
				</div><br/><br/>
				<div class="margin-form">
					<input style="margin:-8px 20px 0 0;" value="1" type="checkbox" name="Twitter" id="Twitter"'.(Configuration::get('ADDTHISSHARE_TWITTER') ? ' checked="checked"' : '').' />
					<img  src="'.$this->_path.'img/twitter.gif" />
					</div>
				<div class="margin-form">
					<input style="margin:-8px 20px 0 0;" value="1" type="checkbox" name="Google" id="Google"'.(Configuration::get('ADDTHISSHARE_GOOGLE') ? ' checked="checked"' : '').' />
					<img src="'.$this->_path.'img/google.gif" />
					</div>
				<div class="margin-form">
					<input style="margin:-8px 20px 0 0;" value="1" type="checkbox" name="Pinterest" id="Pinterest" '.(Configuration::get('ADDTHISSHARE_PINTEREST') ? ' checked="checked"' : '').' />
					<img src="'.$this->_path.'img/pinterest.gif" />
					</div>
				<div class="margin-form">
					<input style="margin:-8px 20px 0 0;" value="1" type="checkbox" name="Facebook" id="Facebook"'.(Configuration::get('ADDTHISSHARE_FACEBOOK') ? ' checked="checked"' : '').' />
					<img src="'.$this->_path.'img/facebook.gif" />
					</div>
				<br/><br/><div class="margin-form">
					<input type="submit" class="button" name="submitCog" id="submitCog" value="'.$this->l('Save').'" />
				</div>
			</form>
		</fieldset>';

		return $this->_html;
  }

	public function updateCog()
	{
		Configuration::updateValue('ADDTHISSHARE_TWITTER', (int)Tools::getValue('Twitter'));
		Configuration::updateValue('ADDTHISSHARE_GOOGLE', (int)Tools::getValue('Google'));
		Configuration::updateValue('ADDTHISSHARE_PINTEREST', (int)Tools::getValue('Pinterest'));
		Configuration::updateValue('ADDTHISSHARE_FACEBOOK', (int)Tools::getValue('Facebook'));
	}

	public function hookDisplayHeader($params)
	{
		$id_lang = (int)$this->context->language->id;
		$product = new Product((int)Tools::getValue('id_product'), false, $id_lang);
		$productLink = $this->context->link->getProductLink($product);
		$images = $product->getImages((int)$id_lang);

		foreach ($images AS $k => $image)
			if ($image['cover'])
			{
				$cover['id_image'] = (int)$product->id.'-'.(int)$image['id_image'];
				$cover['legend'] = $image['legend'];
				break;
			}

		if (!isset($cover))
			$cover = array('id_image' => Language::getIsoById((int)$id_lang).'-default', 'legend' => 'No picture');

		$this->context->smarty->assign(array(
			'cover' => $cover,
			'product' => $product,
			'productLink' => $productLink,
			'this_path' => $this->_path
			)
		);

		return $this->display(__FILE__, 'addsharethis_header.tpl');
	}

	public function hookExtraRight($params)
	{
		if (Configuration::get('ADDTHISSHARE_TWITTER'))
			$data['twitter'] = '<span class="st_twitter_hcount sharebtn" displayText="Tweet"></span>';

		if (Configuration::get('ADDTHISSHARE_GOOGLE'))
			$data['google'] = '<span class="st_googleplus_hcount" displayText="Google +"></span>';

		if (Configuration::get('ADDTHISSHARE_PINTEREST'))
			$data['pinterest'] = '<span class="st_pinterest_hcount sharebtn" displayText="Pinterest"></span>';

		if (Configuration::get('ADDTHISSHARE_FACEBOOK'))
			$data['facebook'] = '<span class="st_facebook_hcount sharebtn" displayText="Facebook"></span>';

		$this->context->smarty->assign(array(
			'addsharethis_data' => $data,
			'conf_row' => Configuration::get('CONF_ROW')
			)
		);

		return $this->display(__FILE__, 'addsharethis.tpl');
	} 
		
	public function hookLeftColumn($params)
	{
		return $this->hookExtraRight($params);
	}

	public function hookFooter($params)
	{
		return $this->hookExtraRight($params);
	}
	
	public function hookHome($params)
	{
		return $this->hookExtraRight($params);
	}

	public function hookExtraleft($params)
	{
		return $this->hookExtraRight($params);
	}

	public function hookProductActions($params)
	{
		return $this->hookExtraRight($params);
	}
	
	public function hookProductFooter($params)
	{
		return $this->hookExtraRight($params);
	}
}