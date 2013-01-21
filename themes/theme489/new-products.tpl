{capture name=path}{l s='New products'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}
<h1>{l s='New products'}</h1>
{if $products}
	<div class="sortPagiBar clearfix">
	{include file="./product-sort.tpl"}
	</div>
	{include file="./product-compare.tpl"}
	{include file="./product-list.tpl" products=$products}
	{include file="./pagination.tpl"}
	{include file="./product-compare.tpl"}
{else}
	<p class="warning">{l s='No new products.'}</p>
{/if}