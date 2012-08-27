<?php
/*
* 2007-2012 PrestaShop 
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class StatsData extends Module
{
    public function __construct()
    {
        $this->name = 'statsdata';
        $this->tab = 'analytics_stats';
        $this->version = 1.0;
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

        parent::__construct();
		
        $this->displayName = $this->l('Data mining for statistics');
        $this->description = $this->l('This module must be enabled if you want to use Statistics.');
    }

	public function install()
	{
		return (parent::install() AND $this->registerHook('footer') AND $this->registerHook('authentication') AND $this->registerHook('createAccount'));
	}
	
	public function getContent()
	{
		if (Tools::isSubmit('submitStatsData'))
		{
			Configuration::updateValue('PS_STATSDATA_CUSTOMER_PAGESVIEWS', (int)Tools::getValue('PS_STATSDATA_CUSTOMER_PAGESVIEWS'));
			Configuration::updateValue('PS_STATSDATA_PAGESVIEWS', (int)Tools::getValue('PS_STATSDATA_PAGESVIEWS'));
			Configuration::updateValue('PS_STATSDATA_PLUGINS', (int)Tools::getValue('PS_STATSDATA_PLUGINS'));
			echo '<div class="conf">'.$this->l('Configuration updated').'</div>';
		}
	
		return '<form action="'.Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']).'" method="post">
		<fieldset><legend><img src="../modules/'.$this->name.'/logo.gif" /> '.$this->l('Settings').'</legend>
			<label>'.$this->l('Save page views for each customer').'</label>
			<div class="margin-form">
				<input type="radio" name="PS_STATSDATA_CUSTOMER_PAGESVIEWS" id="PS_STATSDATA_CUSTOMER_PAGESVIEWS_on" value="1" '.(Tools::getValue('PS_STATSDATA_CUSTOMER_PAGESVIEWS', Configuration::get('PS_STATSDATA_CUSTOMER_PAGESVIEWS')) ? 'checked="checked"' : '').' />
				<label class="t" for="PS_STATSDATA_CUSTOMER_PAGESVIEWS_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'" /></label>
				<input type="radio" name="PS_STATSDATA_CUSTOMER_PAGESVIEWS" id="PS_STATSDATA_CUSTOMER_PAGESVIEWS_off" value="0" '.(Tools::getValue('PS_STATSDATA_CUSTOMER_PAGESVIEWS', Configuration::get('PS_STATSDATA_CUSTOMER_PAGESVIEWS')) ? '' : 'checked="checked"').' />
				<label class="t" for="PS_STATSDATA_CUSTOMER_PAGESVIEWS_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'" /></label>
				<p>'.$this->l('Customer page views uses a lot of CPU resources and database space.').'</p>
			</div>
			<div class="clear">&nbsp;</div>
			<label>'.$this->l('Save global page views').'</label>
			<div class="margin-form">
				<input type="radio" name="PS_STATSDATA_PAGESVIEWS" id="PS_STATSDATA_PAGESVIEWS_on" value="1" '.(Tools::getValue('PS_STATSDATA_PAGESVIEWS', Configuration::get('PS_STATSDATA_PAGESVIEWS')) ? 'checked="checked"' : '').' />
				<label class="t" for="PS_STATSDATA_PAGESVIEWS_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'" /></label>
				<input type="radio" name="PS_STATSDATA_PAGESVIEWS" id="PS_STATSDATA_PAGESVIEWS_off" value="0" '.(Tools::getValue('PS_STATSDATA_PAGESVIEWS', Configuration::get('PS_STATSDATA_PAGESVIEWS')) ? '' : 'checked="checked"').' />
				<label class="t" for="PS_STATSDATA_PAGESVIEWS_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'" /></label>
				<p>'.$this->l('Global page views uses less resources than customer\'s, but uses resources nonetheless.').'</p>
			</div>
			<div class="clear">&nbsp;</div>
			<label>'.$this->l('Plug-ins detection').'</label>
			<div class="margin-form">
				<input type="radio" name="PS_STATSDATA_PLUGINS" id="PS_STATSDATA_PLUGINS_on" value="1" '.(Tools::getValue('PS_STATSDATA_PLUGINS', Configuration::get('PS_STATSDATA_PLUGINS')) ? 'checked="checked"' : '').' />
				<label class="t" for="PS_STATSDATA_PLUGINS_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'" /></label>
				<input type="radio" name="PS_STATSDATA_PLUGINS" id="PS_STATSDATA_PLUGINS_off" value="0" '.(Tools::getValue('PS_STATSDATA_PLUGINS', Configuration::get('PS_STATSDATA_PLUGINS')) ? '' : 'checked="checked"').' />
				<label class="t" for="PS_STATSDATA_PLUGINS_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'" /></label>
				<p>'.$this->l('Plug-ins detection loads an extra 20kb javascript file for new visitors.').'</p>
			</div>
			<div class="clear">&nbsp;</div>
			<input type="submit" class="button" name="submitStatsData" value="'.$this->l('Update').'" />
		</fieldset>';
	}

	public function hookFooter($params)
	{
		$html = '';
		if (!isset($params['cookie']->id_guest))
		{
			Guest::setNewGuest($params['cookie']);
			
			if (Configuration::get('PS_STATSDATA_PLUGINS'))
			{
				// Ajax request sending browser information
				$token = sha1($params['cookie']->id_guest._COOKIE_KEY_);
				$html .= '
				<script type="text/javascript" src="'._PS_JS_DIR_.'pluginDetect.js"></script>
				<script type="text/javascript">
					plugins = new Object;
					
					plugins.adobe_director = (PluginDetect.getVersion("Shockwave") != null) ? 1 : 0;
					plugins.adobe_flash = (PluginDetect.getVersion("Flash") != null) ? 1 : 0;
					plugins.apple_quicktime = (PluginDetect.getVersion("QuickTime") != null) ? 1 : 0;
					plugins.windows_media = (PluginDetect.getVersion("WindowsMediaPlayer") != null) ? 1 : 0;
					plugins.sun_java = (PluginDetect.getVersion("java") != null) ? 1 : 0;
					plugins.real_player = (PluginDetect.getVersion("RealPlayer") != null) ? 1 : 0;
					
					$(document).ready(
						function() {
							navinfo = new Object;
							navinfo = { screen_resolution_x: screen.width, screen_resolution_y: screen.height, screen_color:screen.colorDepth};
							for (var i in plugins)
								navinfo[i] = plugins[i];
							navinfo.type = "navinfo";
							navinfo.id_guest = "'.(int)$params['cookie']->id_guest.'";
							navinfo.token = "'.$token.'";
							$.post("'.Context::getContext()->link->getPageLink('statistics').'", navinfo);
						}
					);
				</script>';
			}
		}
		
		// Record the guest path then increment the visit counter of the page
		$tokenArray = Connection::setPageConnection($params['cookie']);
		ConnectionsSource::logHttpReferer();
		if (Configuration::get('PS_STATSDATA_PAGESVIEWS'))
			Page::setPageViewed($tokenArray['id_page']);
		
		if (Configuration::get('PS_STATSDATA_CUSTOMER_PAGESVIEWS'))
		{
			// Ajax request sending the time spend on the page
			$token = sha1($tokenArray['id_connections'].$tokenArray['id_page'].$tokenArray['time_start']._COOKIE_KEY_);
			$html .= '
			<script type="text/javascript">
				var time_start;
				$(window).load(
					function() {
						time_start = new Date();
					}
				);
				$(window).unload(
					function() {
						var time_end = new Date();
						var pagetime = new Object;
						pagetime.type = "pagetime";
						pagetime.id_connections = "'.(int)$tokenArray['id_connections'].'";
						pagetime.id_page = "'.(int)$tokenArray['id_page'].'";
						pagetime.time_start = "'.$tokenArray['time_start'].'";
						pagetime.token = "'.$token.'";
						pagetime.time = time_end-time_start;
						$.post("'.Context::getContext()->link->getPageLink('statistics').'", pagetime);
					}
				);
			</script>';
		}

		return $html;
	}
	
	public function hookCreateAccount($params)
	{
		return $this->hookAuthentication($params);
	}
	
	public function hookAuthentication($params)
	{
		// Update or merge the guest with the customer id (login and account creation)
		$guest = new Guest($params['cookie']->id_guest);
		$result = Db::getInstance()->getRow('
		SELECT `id_guest`
		FROM `'._DB_PREFIX_.'guest`
		WHERE `id_customer` = '.(int)($params['cookie']->id_customer));

		if ((int)($result['id_guest']))
		{
			// The new guest is merged with the old one when it's connecting to an account
			$guest->mergeWithCustomer($result['id_guest'], $params['cookie']->id_customer);
			$params['cookie']->id_guest = $guest->id;
		}
		else
		{
			// The guest is duplicated if it has multiple customer accounts
			$method = ($guest->id_customer) ? 'add' : 'update';
			$guest->id_customer = $params['cookie']->id_customer;
			$guest->{$method}();
		}
	}
}


