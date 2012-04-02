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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 9856 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if isset($product->id)}

	<h4>{l s='Accounting'}</h4>
	<div class="separation"></div>
	{if !empty($error)}
		<div class="hint" style="display:block">{$error}</div>
	{else}
			<div class="hint" style="display:block">
				{l s='Configure the account number of the product for each zone, if a field is empty, it will use the default one of the shop set in the Accounting Management tab'}
			</div>
			<h3>{l s='Account number'}</h3>
			<br />
				{foreach from=$productAccountNumberList['zones'] key=id_zone item=currentZone}
					<label>{$currentZone['name']}</label>
					<div class="margin-form">
						<input type="text" name="zone_{$id_zone}" value="{$currentZone['account_number']|htmlentities}" />
					</div>
				{/foreach}
			</form>
			<div style="text-align:left; font-size:11px;">
				<i>{l s='These fields are used for the accounting export'}</i>
			</div>
			<div class="separation"></div>
	{/if}

{/if}