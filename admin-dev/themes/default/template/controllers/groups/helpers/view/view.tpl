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
*  @version  Release: $Revision: 9596 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{extends file="helpers/view/view.tpl"}

{block name="override_tpl"}

	<fieldset>
		<ul>
			<li><span style="font-weight: bold; font-size: 13px; color:#000;">{l s='Name:'}</span> {$group->name[$language->id]}</li>
		<li><span style="font-weight: bold; font-size: 13px; color:#000;">{l s='Discount: %d%%' sprintf=$group->reduction}</span></li>
		<li><span style="font-weight: bold; font-size: 13px; color:#000;">{l s='Current category discount:'}</span>
			{if !$categorieReductions}
				{l s='None'}
			{else}
				<table cellspacing="0" cellpadding="0" class="table" style="margin-top:10px">
					{foreach $categorieReductions key=key item=category }
						<tr class="alt_row">
							<td>{$category.path}</td>
							<td>{l s='Discount: %d%%' sprintf=$category.reduction}</td>
						</tr>
					{/foreach}
				</table>
			{/if}
			</li>
			
		<li><span style="font-weight: bold; font-size: 13px; color:#000;">{l s='Price display method:'}</span>
			{if $group->price_display_method}
				{l s='Tax excluded'}
			{else}
				{l s='Tax included'}
			{/if}
		</li>
		<li><span style="font-weight: bold; font-size: 13px; color:#000;">{l s='Show prices:'}</span> {if $group->show_prices}{l s='Yes'}{else}{l s='No'}{/if}
		</li>
		</ul>
	</fieldset>
	<h2>{l s='Members of this customer group'}</h2>
	{$customerList}

{/block}