{if isset($category)}
	{if $category->id AND $category->active}
{capture assign='page_title'}
	{strip}
		{$category->name|escape:'htmlall':'UTF-8'}
		{if isset($categoryNameComplement)}
			{$categoryNameComplement|escape:'htmlall':'UTF-8'}
		{/if}
	{/strip}
{/capture}
{include file='./page-title.tpl'}
	<div data-role="content" id="content">
		{if $category->description}
			<div class="category_desc clearfix">
				{if !empty($category->short_description)}
					<p>{$category->short_description}</p>
					<p class="hide_desc">{$category->description}</p>
					<a href="#" data-theme="a" data-role="button" data-mini="true" data-inline="true" data-icon="arrow-d" class="lnk_more" onclick="$(this).prev().slideDown('slow'); $(this).hide(); return false;" data-ajax="false">{l s='More'}</a>
				{else}
					<p>{$category->description}</p>
				{/if}
			</div>
			<hr width="99%" align="center" size="2" class="margin_less"/>
		{/if}
		<div class="clearfix">
			{include file="./category-product-sort.tpl" container_class="container-sort"}
			<p class="nbr_result">{include file="$tpl_dir./category-count.tpl"}</p>
		</div>
		{* layered ? *}
		{* ===================================== *}
		{*<p><a href="layered.html" data-ajax="false">Affiner la recherche</a></p>*}
		{* ===================================== *}
		<hr width="99%" align="center" size="2" class="margin_less"/>
		{include file="./pagination.tpl"}
		{include file="./category-product-list.tpl" products=$products}
		{include file="./pagination.tpl"}
		{include file='./sitemap.tpl'}
	{elseif $category->id}
		<p class="warning">{l s='This category is currently unavailable.'}</p>
	{/if}
	</div><!-- #content -->
{/if}