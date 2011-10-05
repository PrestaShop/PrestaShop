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
	
	<form action="{$tab_form['current']}&submitAdd{$tab_form['table']}=1&token={$tab_form['token']}" method="post">
		{if $tab_form['id']}<input type="hidden" name="id_{$tab_form['table']}" value="{$tab_form['id']}" />{/if}
		<fieldset>
			<legend><img src="../img/admin/world.gif" />{l s ='States'}</legend>
			<label>{l s ='Name:'} </label>
			<div class="margin-form">
				<input type="text" size="30" maxlength="32" name="name" value="{$tab_form['name']}" /> <sup>*</sup>
				<p class="clear">{l s ='State name to display in addresses and on invoices'}</p>
			</div>
			<label>{l s ='ISO code:'} </label>
			<div class="margin-form">
				<input type="text" size="5" maxlength="4" name="iso_code" value="{$tab_form['iso_code']}" style="text-transform:uppercase;" /> <sup>*</sup>
				<p>{l s ='1 to 4 letter ISO code (search on Wikipedia if you don\'t know)'}</p>
			</div>
			<label>{l s ='Country:'} </label>
			<div class="margin-form">
				<select name="id_country">
					{foreach $tab_form['countries'] AS $country}
						<option value="{$country['id_country']}" {if $tab_form['id_country'] == $country['id_country']}selected="selected"{/if}>{$country['name']}</option>
					{/foreach}
				</select>
				<p>{l s ='Country where state, region or city is located'}</p>
			</div>
			<label>{l s ='Zone:'} </label>
			<div class="margin-form">
				<select name="id_zone">
					{foreach $tab_form['zones'] AS $zone}
						<option value="{$zone['id_zone']}"' {if $tab_form['id_zone'] == $zone['id_zone']}selected="selected"{/if}>{$zone['name']}</option>
					{/foreach}
				</select>
				<p>{l s ='Geographical zone where this state is located'}<br />{l s ='Used for shipping'}</p>
			</div>
			<label>{l s ='Status:'} </label>
			<div class="margin-form">
				<input type="radio" name="active" id="active_on" value="1" {if !$tab_form['id'] || $tab_form['active']} checked="checked"{/if}/>
				<label class="t" for="active_on"> <img src="../img/admin/enabled.gif" alt="{l s ='Enabled'}" title="{l s ='Enabled'}" /></label>
				<input type="radio" name="active" id="active_off" value="0" {if $tab_form['id'] || !$tab_form['active']} checked="checked"{/if}/>
				<label class="t" for="active_off"> <img src="../img/admin/disabled.gif" alt="{l s ='Disabled'}" title="{l s ='Disabled'}" /></label>
				<p>{l s ='Enabled or disabled'}</p>
			</div>
			<div class="margin-form">
				<input type="submit" value="{l s ='   Save   '}" name="submitAdd$tab_form['table']}" class="button" />
			</div>
			<div class="small"><sup>*</sup> {l s ='Required field'}</div>
		</fieldset>
	</form>
	
{/if}

{$content}