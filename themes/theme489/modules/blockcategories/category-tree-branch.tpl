<li {if isset($last) && $last == 'true'}class="last"{/if}>
	<a href="{$node.link|escape:'htmlall':'UTF-8'}" {if isset($currentCategoryId) && $node.id == $currentCategoryId}class="selected"{/if} title="{$node.desc|truncate:30:'...'|escape:'htmlall':'UTF-8'}">{$node.name|escape:'htmlall':'UTF-8'}</a>
	{if $node.children|@count > 0}
		<ul>
		{foreach from=$node.children item=child name=categoryTreeBranch}
			{if $smarty.foreach.categoryTreeBranch.last}
				{include file="$branche_tpl_path" node=$child last='true'}
			{else}
				{include file="$branche_tpl_path" node=$child last='false'}
			{/if}
		{/foreach}
		</ul>
	{/if}
</li>
