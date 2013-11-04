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

{if !$nb_results}
	<h2>{l s='There are no results matching your query "%s".' sprintf=$query}</h2>
{else}
	<h2>
	{if $nb_results == 1}
		{l s='1 result matches your query "%s".' sprintf=$query}
	{else}
		{l s='%d results match your query "%s".' sprintf=[$nb_results|intval, $query]}
	{/if}
	</h2>

	{if isset($features)}
	<div class="panel">
		<h3>
			{if $features|@count == 1}
				{l s='1 feature'}
			{else}
				{l s='%d features' sprintf=$features|@count}
			{/if}
		</h3>
		<table class="table">
			<tbody>
			{foreach $features key=key item=feature}
				{foreach $feature key=k item=val name=feature_list}
					<tr>
						<td><a href="{$val.link}"{if $smarty.foreach.feature_list.first}><strong>{$key}</strong>{/if}</a></td>
						<td><a href="{$val.link}">{$val.value}</a></td>
					</tr>
				{/foreach}
			{/foreach}
			</tbody>
		</table>
	</div>
	{/if}

	{if isset($modules) && $modules}
	<div class="panel">
		<h3>
			{if $modules|@count == 1}
				{l s='1 module'}
			{else}
				{l s='%d modules' sprintf=$modules|@count}
			{/if}
		</h3>
		<table class="table">
			<tbody>
			{foreach $modules key=key item=module}
				<tr>
					<td><a href="{$module->linkto|escape:'htmlall':'UTF-8'}"><strong>{$module->displayName}</strong></a></td>
					<td><a href="{$module->linkto|escape:'htmlall':'UTF-8'}">{$module->description}</a></td>
				</tr>
			{/foreach}
		</tbody>
		</table>
	</div>
	{/if}

	{if isset($categories) && $categories}
	<div class="panel">
		<h3>
			{if $categories|@count == 1}
				{l s='1 category'}
			{else}
				{l s='%d categories' sprintf=$categories|@count}
			{/if}
		</h3>
		<table cellspacing="0" cellpadding="0" class="table">
			{foreach $categories key=key item=category}
				<tr>
					<td>{$category}</td>
				</tr>
			{/foreach}
		</table>
	</div>
	{/if}

	{if isset($products) && $products}
	<div class="panel">
		<h3>
			{if $products|@count == 1}
				{l s='1 product'}
			{else}
				{l s='%d products' sprintf=$products|@count}
			{/if}
		</h3>
		{$products}
	</div>
	{/if}

	{if isset($customers) && $customers}
	<div class="panel">
		<h3>
			{if $customers|@count == 1}
				{l s='1 customer'}
			{else}
				{l s='%d customers' sprintf=$customers|@count}
			{/if}
		</h3>
		{$customers}
	</div>
	{/if}

	{if isset($orders) && $orders}
	<div class="panel">
		<h3>
			{if $orders|@count == 1}
				{l s='1 order'}
			{else}
				{l s='%d orders' sprintf=$orders|@count}
			{/if}
		</h3>
		{$orders}
	</div>
	{/if}

	{if isset($addons) && $addons}
	<div class="panel">
		<h3>
			{if $addons|@count == 1}
				{l s='1 addon'}
			{else}
				{l s='%d addons' sprintf=$addons|@count}
			{/if}
		</h3>
		<table class="table">
			<tbody>
			{foreach $addons key=key item=addon}
				<tr>
					<td><a href="{$addon.href|escape:'htmlall':'UTF-8'}" target="_blank"><strong><i class="icon-external-link-sign"></i> {$addon.title|escape:'htmlall':'UTF-8'}</strong></a></td>
					<td><a href="{$addon.href|escape:'htmlall':'UTF-8'}" target="_blank">{$addon.description|truncate:256:'...'|escape:'htmlall':'UTF-8'}</a></td>
				</tr>
			{/foreach}
		</tbody>
			<tfoot>
				<tr>
					<td colspan="2" class="text-center"><a href="http://addons.prestashop.com/search.php?search_query={$query|urlencode}" target="_blank"><strong>{l s='Show more results...'}</strong></a></td>
				</tr>
			</tfoot>
		</table>
	</div>
	{/if}

{/if}
