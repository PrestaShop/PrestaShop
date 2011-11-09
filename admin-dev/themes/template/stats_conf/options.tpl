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
*  @version  Release: $Revision: 9540 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{extends file="helper/options/options.tpl"}

{block name="start_field_block"}
	<div class="margin-form">
	{if $field['type'] == 'selectEngine'}
		<script type="text/javascript">var engineDescriptions = {$list_engine_description_stats_js};</script>
		<select name="{$key}" {if isset($field['js']) === true} onchange="{$field['js']}"{/if} id="{$key}">
		{foreach $field.list as $k => $option}
			<option value="{if isset($option['cast'])}{$option[$field.identifier]}{else}{$option[$field['identifier']]}{/if}"
				{if $field.value == $option[$field['identifier']]} selected="selected"{/if}>
				{$option.name}
			</option>
		{/foreach}
		</select>
		<div id="render_engine_description">{$list_engine_description_stats[$field.value]}</div>
	{elseif $field['type'] == 'selectGrid'}
		<script type="text/javascript">var gridDescriptions = {$list_engine_description_grid_js};</script>
		<select name="{$key}" {if isset($field['js']) === true} onchange="{$field['js']}"{/if} id="{$key}">
		{foreach $field.list as $k => $option}
			<option value="{if isset($option['cast'])}{$option[$field.identifier]}{else}{$option[$field['identifier']]}{/if}"
				{if $field.value == $option[$field['identifier']]} selected="selected"{/if}>
				{$option.name}
			</option>
		{/foreach}
		</select>
		<div id="render_grid_description">{$list_engine_description_grid[$field.value]}</div>
	{/if}
{/block}

{block name="end_field_block"}</div>{/block}