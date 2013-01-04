{*
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
*}
<tbody>
{if count($list)}
{foreach $list AS $index => $tr}
	<tr
	{if $position_identifier}id="tr_{$tr.id_attribute_group}_{$tr.$identifier}_{if isset($tr.position['position'])}{$tr.position['position']}{else}0{/if}"{/if}
	{if $index is odd}class="alt_row"{/if}
	{if isset($tr.color) && $color_on_bg}style="background-color: {$tr.color}"{/if}
	>
		<td class="center">
			{if {$has_bulk_actions}}
				{if isset($list_skip_actions.delete)}
					{if !in_array($tr.$identifier, $list_skip_actions.delete)}
						<input type="checkbox" name="{$table}Box[]" value="{$tr.$identifier}" class="noborder" />
					{/if}
				{else}
					<input type="checkbox" name="{$table}Box[]" value="{$tr.$identifier}" class="noborder" />
				{/if}
			{/if}
		</td>
		{foreach $fields_display AS $key => $params}
			<td
				{if isset($params.position)}
					id="td_{if $id_category}{$id_category}{else}0{/if}_{$tr.$identifier}"
				{/if}
				class="{if !$no_link}pointer{/if}
				{if isset($params.position) && $order_by == 'position'} dragHandle{/if}
				{if isset($params.align)} {$params.align}{/if}"

			{if (!isset($params.position) && !$no_link)}
				onclick="document.location = '{$current_index}&{$identifier}={$tr.$identifier}{if $view}&view{else}&update{/if}{$table}&token={$token}'">
			{else}
				>
			{/if}
			{if isset($params.prefix)}{$params.prefix}{/if}
			{if isset($params.color) && isset($tr[$params.color])}
				<span class="color_field" style="background-color: {$tr.color}">
			{/if}
			{if isset($params.active)}
				{$tr.$key}
			{elseif isset($params.activeVisu)}
				<img src="../img/admin/{if $tr.$key}enabled.gif{else}disabled.gif{/if}"
				alt="{if $tr.$key}{l s='Enabled'}{else}{l s='Disabled'}{/if}" title="{if $tr.$key}{l s='Enabled'}{else}{l s='Disabled'}{/if}" />
			{elseif isset($params.position)}
				{if $order_by == 'position' && $order_way != 'DESC'}
					<a href="{$tr.$key.position_url_down}" {if !($tr.$key.position != $positions[count($positions) - 1])}style="display: none;"{/if}>
						<img src="../img/admin/{if $order_way == 'ASC'}down{else}up{/if}.gif" alt="{l s='Down'}" title="{l s='Down'}" />
					</a>

					<a href="{$tr.$key.position_url_up}" {if !($tr.$key.position != $positions.0)}style="display: none;"{/if}>
						<img src="../img/admin/{if $order_way == 'ASC'}up{else}down{/if}.gif" alt="{l s='Up'}" title="{l s='Up'}" />
					</a>
				{else}
					{$tr.$key.position + 1}
				{/if}
			{elseif isset($params.image)}
				{$tr.$key}
			{elseif (isset($params.icon))}
				<img src="../img/admin/{$tr[$key]}" alt="{$tr[$key]}" title="{$tr[$key]}" />
			{elseif isset($params.price)}
				{$tr.$key}
			{elseif isset($params.float)}
				{$tr.$key}
			{elseif isset($params.type) && $params.type == 'date'}
				{$tr.$key}
			{elseif isset($params.type) && $params.type == 'datetime'}
				{$tr.$key}
			{* If type is 'editable', an input is created *}
			{elseif isset($params.type) && $params.type == 'editable' && isset($tr.id)}
				<input type="text" name="{$key}_{$tr.id}" value="{$tr.$key|escape:'htmlall':'UTF-8'}" class="{$key}" />
			{elseif isset($params.callback)}
				{$tr.$key}
			{elseif isset($tr.$key) && $key == 'color'}
				{if isset($tr.$key['texture']) && $tr.$key['texture']|count_characters > 1}
					<img src="{$tr.$key.texture}" alt="" />
				{else}
					<div style="float: left; width: 18px; height: 12px; border: 1px solid #996633; background-color: {$tr.$key}; margin-right: 4px;"></div>
				{/if}
			{elseif isset($tr.$key)}
				{$tr.$key|escape:'htmlall':'UTF-8'}
			{else}
				--
			{/if}
			{if isset($params.suffix)}{$params.suffix}{/if}
			{if isset($params.color) && isset($tr.color)}
				</span>
			{/if}
			</td>
		{/foreach}

	{if $shop_link_type}
		<td class="center" title="{$tr.shop_name}">
			{if isset($tr.shop_short_name)}
				{$tr.shop_short_name}
			{else}
				{$tr.shop_name}
			{/if}</td>
	{/if}
	{if $has_actions}
		<td class="center" style="white-space: nowrap;">
			{foreach $actions AS $action}
				{if isset($tr.$action)}
					{$tr.$action}
				{/if}
			{/foreach}
		</td>
	{/if}
	</tr>
{/foreach}
{else}
	<tr><td class="center" colspan="{count($fields_display) + 2}">{l s='No items found'}</td></tr>
{/if}
</tbody>
