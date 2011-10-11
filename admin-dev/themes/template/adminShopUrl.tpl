{*
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
*  @version  Release: $Revision: 8897 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if isset($tab_form)}

	<script type="text/javascript">
		//<![CDATA[
		function fillShopUrl()
		{
			var domain = $('#domain').val();
			var physical = $('#physical_uri').val();
			var virtual = $('#virtual_uri').val();
			url = ((domain) ? domain : '???');
			if (physical)
			url += '/'+physical;
			if (virtual)
			url += '/'+virtual;
			url = url.replace(/\/+/g, "/");
			$('#final_url').val('http://'+url);
		};
	
		var shopUrl = {$tab_form['jsShopUrl']};
		
		function checkMainUrlInfo(shopID)
		{
			if (!shopID)
				shopID = $('#id_shop').val();
	
			if (!shopUrl[shopID])
			{
				$('#main_off').attr('disabled', true);
				$('#main_on').attr('checked', true);
				$('#mainUrlInfo').css('display', 'block');
				$('#mainUrlInfoExplain').css('display', 'none');
			}
			else
			{
				$('#main_off').attr('disabled', false);
				$('#mainUrlInfo').css('display', 'none');
				$('#mainUrlInfoExplain').css('display', 'block');
			}
		}
	
		$().ready(function()
		{
			fillShopUrl();
			checkMainUrlInfo();
			$('#domain, #physical_uri, #virtual_uri').keyup(fillShopUrl);
		});
		
		//]]>
	</script>
	
	<form action="{$tab_form['current']}&submitAdd{$tab_form['table']}=1&token={$tab_form['token']}" method="post">
		{if $tab_form['id']}<input type="hidden" name="id_{$tab_form['table']}" value="{$tab_form['id']}" />{/if}
		<fieldset>
			<legend>{l s ='Shop Url'}</legend>
			<label for="domain">{l s ='Domain'}</label>
			<div class="margin-form">
				<input type="text" name="domain" id="domain" value="{$tab_form['domain']}" />
			</div>
			<label for="domain">{l s ='Domain SSL'}</label>
			<div class="margin-form">
				<input type="text" name="domain_ssl" id="domain_ssl" value="{$tab_form['domain_ssl']}" />
			</div>
			<label for="physical_uri">{l s ='Physical URI'}</label>
			<div class="margin-form">
				<input type="text" name="physical_uri" id="physical_uri" value="{$tab_form['physical_uri']}" />
				<p>{l s ='Physical folder of your store on your server. Leave this field empty if your store is installed on root path.'}</p>
			</div>
			<label for="virtual_uri">{l s ='Virtual URI'}</label>
			<div class="margin-form">
				<input type="text" name="virtual_uri" id="virtual_uri" value="{$tab_form['virtual_uri']}" />
				<p>{l s ='This virtual folder must not exist on your server and is used to associate an URI to a shop.'}<br /><b>{l s ='URL rewriting must be activated on your server to use this feature.'}</b></p>
			</div>
			<label>{l s ='Your final URL will be'}</label>
			<div class="margin-form">
				<input type="text" readonly="readonly" id="final_url" style="width: 400px" /> 
			</div>
			<label for="id_shop">{l s ='Shop'}</label>
			<div class="margin-form">
				<select name="id_shop" id="id_shop" onchange="checkMainUrlInfo(this.value)">
				{foreach $tab_form['getTree'] AS $gID => $gData}
					<optgroup label="{$gData['name']}">
					{foreach $gData['shops'] as $sID => $sData}
						<option value="{$sID}" {if {$tab_form['id_shop']} ==  $sID} selected="selected"{/if}>{$sData['name']}</option>
						{/foreach}
					</optgroup>
				{/foreach}
				</select>
			</div>
			<label>{l s ='Main URL:'}</label>
			<div class="margin-form">
				<input type="radio" name="main" id="main_on" value="1" {if $tab_form['main']} checked="checked"{/if}/>
				<label class="t" for="main_on"><img src="../img/admin/enabled.gif" alt="{l s ='Enabled'}" title="{l s ='Enabled'}" /></label>
				<input type="radio" name="main" id="main_off" value="0" {if !$tab_form['main']} checked="checked"{/if} />
				<label class="t" for="main_off"><img src="../img/admin/disabled.gif" alt="{l s ='Disabled'}" title="{l s ='Disabled'}" /></label>
				<p>{l s ='If you set this url as main url for selected shop, all urls set to this shop will be redirected to this url (you can only have one main url per shop).'}</p>
				<p id="mainUrlInfo">{l s ='Since the selected shop has no main url, you have to set this url as main'}</p>
				<p id="mainUrlInfoExplain">{l s ='The selected shop has already a main url, if you set this one as main url, the older one will be set as normal url'}</p>
			</div>
			<label>{l s ='Status:'} </label>
			<div class="margin-form">
				<input type="radio" name="active" id="active_on" value="1" {if $tab_form['active']} checked="checked"{/if} />
				<label class="t" for="active_on"> <img src="../img/admin/enabled.gif" alt="{l s ='Enabled'}" title="{l s ='Enabled'}" /></label>
				<input type="radio" name="active" id="active_off" value="0" {if !$tab_form['active']} checked="checked"{/if} />
				<label class="t" for="active_off"> <img src="../img/admin/disabled.gif" alt="{l s ='Disabled'}" title="{l s ='Disabled'}" /></label>
				<p>{l s ='Enable or disable URL'}</p>
			</div>
			<div class="margin-form">
				<input type="submit" value="{l s ='   Save   '}" name="submitAdd{$tab_form['table']}" class="button" />
			</div>
			<div class="small"><sup>*</sup> {l s ='Required field'}</div>
		</fieldset>
	</form>
	
{/if}

{$content}