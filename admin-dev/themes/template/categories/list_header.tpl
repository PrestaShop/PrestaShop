{extends file="helper/list/list_header.tpl"}
{block name=leadin}
<div class="cat_bar2">
	{if count($categories_tree) == 0}
		&nbsp;<img src="../img/admin/home.gif" alt="" /> {l s='Home'}
	{else}
			&nbsp;<img src="../img/admin/home.gif" alt="" /> {l s='Home'}&nbsp;>&nbsp;

		{foreach $categories_tree key=key item=category}
				{$category.name}
		{/foreach}
	{/if}
</div>
{/block}
