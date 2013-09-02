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

<script type="text/javascript">
$(function() {
	$('body').highlight('{$query}');
});
</script>

{if $show_toolbar}
	{include file="toolbar.tpl" toolbar_btn=$toolbar_btn toolbar_scroll=$toolbar_scroll title=$title}
	<div class="leadin">{block name="leadin"}{/block}</div>
{/if}


{if isset($features)}
	<div class="panel">
	{if !$features}
		<h3>{l s='No features matching your query'} : {$query}</h3>
	{else}
		<h3>{l s='Features matching your query'} : {$query}</h3>
		<table class="table">
			<tbody>
			{foreach $features key=key item=feature}
				{foreach $feature key=k item=val name=feature_list}
					<tr>
						<td><strong>{if $smarty.foreach.feature_list.first}{$key}{/if}</strong></td>

						<td>
							<a href="{$val.link}">{$val.value}</a>
						</td>
					</tr>
				{/foreach}
			{/foreach}
			</tbody>
		</table>
	{/if}
	</div>
{/if}

{if isset($modules)}
	<div class="panel">
	{if !$modules}
		<h3>{l s='No modules matching your query'} : {$query}</h3>
	{else}
		<h3>{l s='Modules matching your query'} : {$query}</h3>
		<table class="table">
			<tbody>
			{foreach $modules key=key item=module}
				<tr>
					<td><strong><a href="{$module->linkto|escape:'htmlall':'UTF-8'}">{$module->displayName}</a></strong></td>
					<td><a href="{$module->linkto|escape:'htmlall':'UTF-8'}">{$module->description}</a></td>
				</tr>
			{/foreach}
		</tbody>
		</table>
	{/if}
	</div>
{/if}

{if isset($categories)}
	<div class="panel">
	{if !$categories}
		<h3>{l s='No categories matching your query'} : {$query}</h3>
	{else}
		<h3>{l s='Categories matching your query'} : {$query}</h3>
		<table cellspacing="0" cellpadding="0" class="table">
			{foreach $categories key=key item=category}
				<tr class="alt_row">
					<td>{$category}</td>
				</tr>
			{/foreach}
		</table>
	{/if}
	</div>
{/if}

{if isset($products)}
	<div class="panel">
	{if !$products}
		<h3>{l s='There are no products matching your query'} : {$query}</h3>
	{else}
		<h3>{l s='Products matching your query'} : {$query}</h3>
		{$products}
	{/if}
	</div>
{/if}

{if isset($customers)}
	<div class="panel">
	{if !$customers}
		<h3>{l s='There are no customers matching your query'} : {$query}</h3>
	{else}
		<h3>{l s='Customers matching your query'} : {$query}</h3>
		{$customers}
	{/if}
	</div>
{/if}

{if isset($orders)}
	<div class="panel">
	{if !$orders}
		<h3>{l s='There are no orders matching your query'} : {$query}</h3>
	{else}
		<h3>{l s='Orders matching your query'} : {$query}</h3>
		{$orders}
	{/if}
	</div>
{/if}