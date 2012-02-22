{*
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 9856 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if !empty($error)}
	<div class="hint" style="display:block">{$error}</div>
{else}
	<div class="toolbarBox">
		{include file="toolbar.tpl" toolbar_btn=$toolbar_btn}
		<div class="pageTitle">
			<h3>
				<span id="current_obj" style="font-weight: normal;">{$title|default:'&nbsp;'}</span>
			</h3>
		</div>
	</div>
	<fieldset>
		<legend>{l s='Account number'}</legend>
		<div class="hint" style="display:block">
			{l s='Configure the account number by zone for:'} <b>{$shop_details['name']}</b>
		</div>
		<br />
		<form id="{$table}_form" method="POST" action="{$smarty.server.REQUEST_URI}">
			<label>{l s='Default number for this shop'}</label>
				<div class="margin-form">
				<input type="text" name="default_account_number" value="{$shop_details['default_account_number']|htmlentities}" />
				<p>{l s='If a zone field is empty it will use this default number.'}</p>
			</div>
			{foreach from=$shop_details['zones'] key=id_zone item=currentZone}
				<label>{$currentZone['name']}</label>
				<div class="margin-form">
					<input type="text" name="zone_{$id_zone}" value="{$currentZone['account_number']|escape:htmlall|htmlentities}" />

				</div>
			{/foreach}
			<div class="margin-form">
				<input type="submit" class="button" id="{$table}_form_submit_btn" name="UpdateNumbers" value="{l s='Save'}"/>
			</div>
		</form>
		<div class="separation"></div>
	</fieldset>
{/if}
