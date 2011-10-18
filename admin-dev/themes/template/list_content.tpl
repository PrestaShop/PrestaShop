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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{foreach $list AS $index => $tr}
	<tr
	{if $is_dnd_identifier}id="tr_{$id_category}_{$tr.$identifier}_{$tr.position}"{/if}
	{if $index is odd}class="alt_row"{/if}
	{if isset($tr.color) && $color_on_bg}style="background-color: {$tr.color}"{/if}
	>
		<td class="center">
			{if $bulk_actions AND array_key_exists('delete', $list_skip_actions) AND !in_array($tr.$identifier, $list_skip_actions['delete'])}
				<input type="checkbox" name="{$table}Box[]" value="{$tr.$identifier}" class="noborder" />
			{/if}
		</td>
		{foreach $fields_display AS $key => $params}
			<td
				{if isset($params.position)}
					id="td_{if $id_category}$id_category{else}0{/if}_{$tr.$identifier}"
				{/if}
				class="{if !$no_link}pointer{/if}
					   {if isset($params.position) && $order_by == 'position'} dragHandle{/if}
					   {if isset($params.align)} {$params.align}{/if}"

			{if (!isset($params.position) && !$no_link)}
				onclick="document.location = '{$current_index}&{$identifier}={$tr.$identifier}{if $view}&view{else}&update{/if}{$table}&token={$token}'">{if isset($params.prefix)}{$params.prefix}{/if}
			{else}
				>
			{/if}
			{if isset($params.active)}
			    {$tr.$key}
			{elseif isset($params.activeVisu)}
				<img src="../img/admin/{if $tr.$key}enabled.gif{else}disabled.gif{/if}"
				alt="{if $tr.$key}{l s='Enabled'}{else}{l s='Disabled'}{/if}" title="{if $tr.$key}{l s='Enabled'}{else}{l s='Disabled'}{/if}" />
			{elseif isset($params.position)}
				{if $orderBy == 'position' && $orderWay != 'DESC'}
					<a {if !($tr.$key.position != $positions[sizeof($positions) - 1])}style="display: none;"{/if} href="{$tr.$key.position_url_down}">
							<img src="../img/admin/{if $orderWay == 'ASC'}down{else}up{/if}.gif"
							alt="{l s='Down'}" title="{l s='Down'}" /></a>

					<a {if !($tr.$key.position != $positions.0)}style="display: none;"{/if} href="{$tr.$key.position_url_up}">
							<img src="../img/admin/{if $orderWay == 'ASC'}up{else}down{/if}.gif"
							alt="{l s='Up'}" title="{l s='Up'}" /></a>';
				{else}
					{$tr.$key.position} + 1
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
			{elseif isset($tr.$key)}
				{$tr.$key|escape:'htmlall':'UTF-8'}
			{else}
				--
			{/if}
			{if isset($params.suffix)}{$params.suffix}{/if}
			</td>
		{/foreach}

	{if $shop_link_type}
		<td class="center" {if $name != $tr.shop_name}title="$tr.shop_name"{/if}>{$tr.shop_short_name}</td>
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