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

class BlockPaymentLogo extends Module
{
	public function __construct()
	{
		$this->name = 'blockpaymentlogo';
		$this->tab = 'front_office_features';
		$this->version = '0.2';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Payment logo block.');
		$this->description = $this->l('This block will display all of your payment logos.');
	}

	public function install()
	{
		Configuration::updateValue('PS_PAYMENT_LOGO_CMS_ID', 0);
		if (!parent::install())
			return false;
		if (!$this->registerHook('leftColumn'))
			return false;
		if (!$this->registerHook('header'))
			return false;
		return true;
	}

	public function uninstall()
	{
		Configuration::deleteByName('PS_PAYMENT_LOGO_CMS_ID');
		return parent::uninstall();
	}

	public function getContent()
	{
		$html = '
		<h2>'.$this->l('Payment logo.').'</h2>
		';

		if (Tools::isSubmit('submitConfiguration'))
			if (Validate::isUnsignedInt(Tools::getValue('id_cms')))
			{
				Configuration::updateValue('PS_PAYMENT_LOGO_CMS_ID', (int)(Tools::getValue('id_cms')));
				$this->_clearCache('blockpaymentlogo.tpl');
				$html .= $this->displayConfirmation($this->l('The settings have been updated.'));
			}

		$cmss = CMS::listCms($this->context->language->id);

		if (!count($cmss))
			$html .= $this->displayError($this->l('No CMS page is available.'));
		else
		{
			$html .= '
			<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post">
				<fieldset>
					<legend><img src="'.$this->_path.'/logo.gif" alt="" /> '.$this->l('Configure').'</legend>
					<label>'.$this->l('Page CMS for link').':</label>
					<div class="margin-form">
						<select name="id_cms"><option value="0">('.$this->l('Select a page').')</option>';
			foreach ($cmss as $cms)
				$html .= '<option value="'.$cms['id_cms'].'"'.(Configuration::get('PS_PAYMENT_LOGO_CMS_ID') == $cms['id_cms'] ? ' selected="selected"' : '').'>'.$cms['meta_title'].'</option>';
			$html .= '</select>
					</div>
					<p class="center"><input class="button" type="submit" name="submitConfiguration" value="'.$this->l('Save settings').'" /></p>
				</fieldset>
			</form>
			';
		}
		return $html;
	}

	/**
	* Returns module content
	*
	* @param array $params Parameters
	* @return string Content
	*/
	public function hookLeftColumn($params)
	{
		if (Configuration::get('PS_CATALOG_MODE'))
			return;

		if (!$this->isCached('blockpaymentlogo.tpl', $this->getCacheId()))
		{
			if (!Configuration::get('PS_PAYMENT_LOGO_CMS_ID'))
				return;
			$cms = new CMS(Configuration::get('PS_PAYMENT_LOGO_CMS_ID'), $this->context->language->id);
			if (!Validate::isLoadedObject($cms))
				return;
			$this->smarty->assign('cms_payement_logo', $cms);
		}

		return $this->display(__FILE__, 'blockpaymentlogo.tpl', $this->getCacheId());
	}

	public function hookRightColumn($params)
	{
		return $this->hookLeftColumn($params);
	}

	public function hookFooter($params)
	{
		return $this->hookLeftColumn($params);
	}
	public function hookHeader($params)
	{
		if (Configuration::get('PS_CATALOG_MODE'))
			return;
		$this->context->controller->addCSS(($this->_path).'blockpaymentlogo.css', 'all');
	}

}


