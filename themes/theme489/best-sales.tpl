{capture name=path}{l s='Top sellers'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}
<h1>{l s='Top sellers'}</h1>
{if $products}
	<div class="sortPagiBar clearfix">
	{include file="$tpl_dir./product-sort.tpl"}
	</div>
	{include file="./product-compare.tpl"}
	{include file="$tpl_dir./product-list.tpl" products=$products}
	{include file="$tpl_dir./pagination.tpl"}
	{include file="./product-compare.tpl"}
{else}
	<p class="warning">{l s='No top sellers.'}</p>
{/if}
