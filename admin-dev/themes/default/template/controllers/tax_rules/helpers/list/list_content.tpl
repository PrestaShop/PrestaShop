{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *}
<tbody>
	{foreach $list AS $index => $tr}
		<tr
		{if $position_identifier}id="tr_{$id_category}_{$tr.$identifier}_{$tr.position['position']}"{/if}
		{if isset($tr.color) && $color_on_bg}style="background-color: {$tr.color}"{/if} >
			{if $bulk_actions && $has_bulk_actions}
				<td class="text-center">
					{assign var=bulkActionPossible value=true}
					{foreach $list_skip_actions as $key => $value}
						{if in_array($tr.$identifier, $value) == true}
							{assign var=bulkActionPossible value=false}
						{/if}
					{/foreach}
					{if $bulkActionPossible == true}
						<input type="checkbox" name="{$table}Box[]" value="{$tr.$identifier}" class="noborder" />
					{/if}
				</td>
			{/if}
			{foreach $fields_display AS $key => $params}
				<td
					{if isset($params.position)}
						id="td_{if $id_category}{$id_category}{else}0{/if}_{$tr.$identifier}"
					{/if}
					class="{strip}{if !$no_link}pointer{/if}
						   {if isset($params.position) && $order_by == 'position'} dragHandle{/if}
						   {if isset($params.align)} {$params.align}{/if}{/strip}"

				{if (!isset($params.position) && !$no_link)}
					onclick="document.location = '{$current_index|addslashes|escape:'html':'UTF-8'}&amp;{$identifier|escape:'html':'UTF-8'}={$tr.$identifier|escape:'html':'UTF-8'}{if $view}&amp;view{else}&amp;update{/if}{$table}&amp;token={$token|escape:'html':'UTF-8'}'">{if isset($params.prefix)}{$params.prefix}{/if}
				{else}
					>
				{/if}
				{if isset($params.active)}
				    {$tr.$key}
				{elseif isset($params.activeVisu)}
					<img src="../img/admin/{if $tr.$key}enabled.gif{else}disabled.gif{/if}"
					alt="{if $tr.$key}{l s='Enabled' d='Admin.Global'}{else}{l s='Disabled' d='Admin.Global'}{/if}" title="{if $tr.$key}{l s='Enabled' d='Admin.Global'}{else}{l s='Disabled' d='Admin.Global'}{/if}" />
				{elseif isset($params.position)}
					{if $order_by == 'position' && $order_way != 'DESC'}
						<a href="{$tr.$key.position_url_down}" {if !($tr.$key.position != $positions[count($positions) - 1])}style="display: none;"{/if}>
							<img src="../img/admin/{if $order_way == 'ASC'}down{else}up{/if}.gif" alt="{l s='Down' d='Admin.International.Feature'}" title="{l s='Down' d='Admin.International.Feature'}" />
						</a>

						<a href="{$tr.$key.position_url_up}" {if !($tr.$key.position != $positions.0)}style="display: none;"{/if}>
							<img src="../img/admin/{if $order_way == 'ASC'}up{else}down{/if}.gif" alt="{l s='Up' d='Admin.International.Feature'}" title="{l s='Up' d='Admin.International.Feature'}" />
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
				{elseif isset($params.callback)}
					{$tr.$key}
				{elseif isset($tr.$key)}
					{if $key == 'behavior'}
						{if $tr.$key == constant('PrestaShop\PrestaShop\Core\Domain\TaxRule\TaxRuleSettings::BEHAVIOR_TAX_ONLY')}
							{l s='This tax only' d='Admin.International.Feature'}
						{elseif $tr.$key == constant('PrestaShop\PrestaShop\Core\Domain\TaxRule\TaxRuleSettings::BEHAVIOR_COMBINE')}
							{l s='Combine' d='Admin.International.Feature'}
						{elseif $tr.$key == constant('PrestaShop\PrestaShop\Core\Domain\TaxRule\TaxRuleSettings::BEHAVIOR_ONE_AFTER_ANOTHER')}
							{l s='One after another' d='Admin.International.Feature'}
						{/if}
					{elseif $key == 'rate'}
						{$tr.$key|string_format:"%.3f"}%
					{elseif $key == 'zipcode'}
						{if $tr.$key == '0 - 0'}
							--
						{else}
							{$tr.$key|escape:'html':'UTF-8'}
						{/if}
					{else}
						{$tr.$key|escape:'html':'UTF-8'}
					{/if}
				{else}
					--
				{/if}
				{if isset($params.suffix)}{$params.suffix}{/if}
				</td>
			{/foreach}

		{if $shop_link_type}
			<td {if $name != $tr.shop_name}title="{$tr.shop_name}"{/if}>{if isset($tr.shop_short_name)}{$tr.shop_short_name}{else}{$tr.shop_name}{/if}</td>
		{/if}
		{if $has_actions}
			<td class="text-right fixed-width-lg">
				{assign var='compiled_actions' value=array()}
				{foreach $actions AS $key => $action}
					{if isset($tr.$action)}
						{if $key == 0}
							{assign var='action' value=$action}
						{/if}
						{$compiled_actions[] = $tr.$action}
					{/if}
				{/foreach}
				{if $compiled_actions|count > 0}
					{if $compiled_actions|count > 1}<div class="btn-group-action">{/if}
					<div class="btn-group pull-right">
						{$compiled_actions[0]|regex_replace:'/class\s*=\s*"(\w*)"/':'class="$1 btn btn-default"'}
						{if $compiled_actions|count > 1}
						<button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
							<span class="caret"></span>&nbsp;
						</button>
							<ul class="dropdown-menu">
							{foreach $compiled_actions AS $key => $action}
								{if $key != 0}
								<li>
									{$action}
								</li>
								{/if}
							{/foreach}
							</ul>
						{/if}
					</div>
					{if $compiled_actions|count > 1}</div>{/if}
				{/if}
			</td>
		{/if}
		</tr>
	{/foreach}
</tbody>
