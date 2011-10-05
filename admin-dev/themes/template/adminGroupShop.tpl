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
	$().ready(function()
	{
		$('input[name=share_order]').attr('disabled', true);
		$('input[name=share_customer], input[name=share_stock]').click(function()
		{
			var disabled = ($('input[name=share_customer]').attr('checked') && $('input[name=share_stock]').attr('checked')) ? false : true;
			$('input[name=share_order]').attr('disabled', disabled);
			if (disabled)
				$('#share_order_off').attr('checked', true);
		});
	});
</script>


<form action="{$tab_form['current']}&submitAdd{$tab_form['table']}=1&token={$tab_form['token']}" method="post">
		{if $tab_form['id']}<input type="hidden" name="id_{$tab_form['table']}" value="{$tab_form['id']}" />{/if}
	<fieldset>
		<legend>{l s ='GroupShop'}</legend>
		<div class="hint" name="help_box" style="display:block;">{l s ='You can\'t edit GroupShop when you have more than one Shop'}</div><br />
		<label for="name">{l s ='GroupShop name'}</label>
		<div class="margin-form">
			<input type="text" name="name" id="name" value="{$tab_form['name']}" />
		</div>
		<label for="share_customer">{l s ='Share customers'}</label>
		<div class="margin-form">
			<input type="radio" name="share_customer" {$tab_form['disabled']} id="share_customer_on" value="1" {if $tab_form['share_customer']} checked="checked"{/if}/>
			<label class="t" for="share_customer_on"> <img src="../img/admin/enabled.gif" alt="{l s ='Enabled'}" title="{l s ='Enabled'}" /></label>
			<input type="radio" name="share_customer" {$tab_form['disabled']} id="share_customer_off" value="0" {if !$tab_form['share_customer']} checked="checked"{/if}/>
			<label class="t" for="ashare_customer_off"> <img src="../img/admin/disabled.gif" alt="{l s ='Disabled'}" title="{l s ='Disabled'}" /></label>
			<p>{l s ='Share customers between shops of this group'}</p>
		</div>
		<label for="share_stock">{l s ='Share stock'}</label>
		<div class="margin-form">
			<input type="radio" name="share_stock" {$tab_form['disabled']} id="share_stock_on" value="1" {if $tab_form['share_stock']} checked="checked"{/if}/>
			<label class="t" for="share_stock_on"> <img src="../img/admin/enabled.gif" alt="{l s ='Enabled'}" title="{l s ='Enabled'}" /></label>
			<input type="radio" name="share_stock" {$tab_form['disabled']} id="share_stock_off" value="0" {if !$tab_form['share_stock']} checked="checked"{/if} />
			<label class="t" for="share_stock_off"> <img src="../img/admin/disabled.gif" alt="{l s ='Disabled'}" title="{l s ='Disabled'}" /></label>
			<p>{l s ='Sare stock between shops of this group'}</p>
		</div>
		<label for="share_order">{l s ='Share orders'}</label>
		<div class="margin-form">
			<input type="radio" name="share_order" {$tab_form['disabled']} id="share_order_on" value="1" {if $tab_form['share_order']} checked="checked"{/if}/>
			<label class="t" for="share_order_on"> <img src="../img/admin/enabled.gif" alt="{l s ='Enabled'}" title="{l s ='Enabled'}" /></label>
			<input type="radio" name="share_order" {$tab_form['disabled']} id="share_order_off" value="0"  {if !$tab_form['share_order']} checked="checked"{/if}/>
			<label class="t" for="share_order_off"> <img src="../img/admin/disabled.gif" alt="{l s ='Disabled'}" title="{l s ='Disabled'}" /></label>
			<p>{l s ='Share orders and carts between shops of this group (you can share orders only if you share customers and stock)'}</p>
		</div>
		<label>{l s ='Status:'} </label>
		<div class="margin-form">
			<input type="radio" name="active" id="active_on" value="1" {if $tab_form['active']} checked="checked"{/if}/>
			<label class="t" for="active_on"> <img src="../img/admin/enabled.gif" alt="{l s ='Enabled'}" title="{l s ='Enabled'}" /></label>
			<input type="radio" name="active" id="active_off" value="0" {if !$tab_form['active']} checked="checked"{/if}/>
			<label class="t" for="active_off"> <img src="../img/admin/disabled.gif" alt="{l s ='Disabled'}" title="{l s ='Disabled'}" /></label>
			<p>{l s ='Enable or disable shop'}</p>
		</div>
		<div class="margin-form">
			<input type="submit" value="{l s ='   Save   '}" name="submitAdd{$tab_form['table']}" class="button" />
		</div>
		<div class="small"><sup>*</sup> {l s ='Required field'}</div>
	</fieldset><br />

	<fieldset><legend>{l s ='Import data from another group shop'}</legend>
	<label>{l s ='Import data from another group shop'}</label>
	<div class="margin-form">
		<input type="checkbox" value="1" {if $tab_form['checked']} checked="checked"{/if} name="useImportData" onclick="$('#importList').slideToggle('slow')" /> 
		{l s ='Duplicate data from group shop'}
		 <select name="importFromShop">
		{foreach $tab_form['getTree'] as $gID => $gData}
			<option value="{$gID}" {if $gID == $tab_form['defaultGroup']} selected="selected"{/if}">{$gData['name']}</option>
		{/foreach}
		</select>
		<div id="importList" style="{if !$tab_form['checked']}display: none{/if}"><ul>
		{foreach $tab_form['importData'] as $table => $lang}
			<li><label><input type="checkbox" name="importData[{$table}]" checked="checked" /> {$lang}</label></li>
		{/foreach}
		</ul></div>
		<p>{l s ='Use this option to associate data (products, modules, etc.) the same way as the selected shop'}</p>
	</div><div class="margin-form">
			<input type="submit" value="{l s ='   Save   '}" name="submitAdd{$tab_form['table']}" class="button" />
		</div>
	</fieldset>
</form>
{/if}

{$content}