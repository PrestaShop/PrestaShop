{include file="$tpl_dir./breadcrumb.tpl"}
{include file="$tpl_dir./errors.tpl"}
{if isset($category)}
	{if $category->id AND $category->active}
		<h1>
			{strip}
				{$category->name|escape:'htmlall':'UTF-8'}
				{if isset($categoryNameComplement)}
					{$categoryNameComplement|escape:'htmlall':'UTF-8'}
				{/if}
				<span class="category-product-count">
					{include file="$tpl_dir./category-count.tpl"}
				</span>
			{/strip}
		</h1>
		{if $scenes}
			<!-- Scenes -->
			{include file="$tpl_dir./scenes.tpl" scenes=$scenes}
{*
		{else}
			<!-- Category image -->
			{if $category->id_image}
			<div class="align_center">
				<img src="{$link->getCatImageLink($category->link_rewrite, $category->id_image, 'category_default')}" alt="{$category->name|escape:'htmlall':'UTF-8'}" title="{$category->name|escape:'htmlall':'UTF-8'}" id="categoryImage" width="{$categorySize.width}" height="{$categorySize.height}" />
			</div>
			{/if}
*}
		{/if}
		{if $category->description}
				{if strlen($category->description) > 120}
				<p id="category_description_short" class="cat_desc  bordercolor bgcolor">{$category->description|truncate:120}<a href="#" onclick="$('#category_description_short').hide(); $('#category_description_full').show(); $(this).hide(); return false;" class="lnk_more">{l s='More'}</a></p>
				<p id="category_description_full"  class="cat_desc  bordercolor bgcolor" style="display:none">{$category->description}</p>
				{else}
				<p>{$category->description}</p>
				{/if}
			
		{/if}
		{if isset($subcategories)}
		<!-- Subcategories -->
		<div id="subcategories" class="clearfix">
			<h2>{l s='Subcategories'}</h2>
			<ul>
			{foreach from=$subcategories item=subcategory}
				<li>
					<a class="bgcolor bordercolor" href="{$link->getCategoryLink($subcategory.id_category, $subcategory.link_rewrite)|escape:'htmlall':'UTF-8'}" title="{$subcategory.name|escape:'htmlall':'UTF-8'}">
						{if $subcategory.id_image}
							<img class="bordercolor" src="{$link->getCatImageLink($subcategory.link_rewrite, $subcategory.id_image, 'medium_default')}" alt="" />
						{else}
							<img src="{$img_cat_dir}default-medium_default.jpg" alt="" />
						{/if}
					<span>{$subcategory.name|escape:'htmlall':'UTF-8'|truncate:20:'...'}</span>
					</a>
                    <strong></strong>
				</li>
			{/foreach}
			</ul>
		</div>
		{/if}
		{if $products}
			{include file="$tpl_dir./product-sort.tpl"}
			{include file="$tpl_dir./product-list.tpl" products=$products}
			{include file="$tpl_dir./product-compare.tpl"}
			{include file="$tpl_dir./pagination.tpl"}
		{elseif !isset($subcategories)}
		<p class="warning">{l s='There are no products in this category.'}</p>
		{/if}
	{elseif $category->id}
		<p class="warning">{l s='This category is currently unavailable.'}</p>
	{/if}
{/if}