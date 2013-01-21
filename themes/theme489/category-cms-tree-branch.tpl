<li {if isset($last) && $last == 'true'}class="last"{/if}>
	<strong><a href="{$node.link|escape:'htmlall':'UTF-8'}" title="{$node.name|escape:'htmlall':'UTF-8'}">{$node.name|escape:'htmlall':'UTF-8'}</a></strong>
	{if isset($node.children) && $node.children|@count > 0}
		<ul>
		{foreach from=$node.children item=child name=categoryCmsTreeBranch}
			{if isset($child.children) && $child.children|@count > 0 || isset($child.cms) && $child.cms|@count > 0}
				{if $smarty.foreach.categoryCmsTreeBranch.last && $node.cms|@count == 0}
					{include file="$tpl_dir./category-cms-tree-branch.tpl" node=$child last='true'}
				{else}
					{include file="$tpl_dir./category-cms-tree-branch.tpl" node=$child}
				{/if}
			{/if}
		{/foreach}
		{if isset($node.cms) && $node.cms|@count > 0}
			{foreach from=$node.cms item=cms name=cmsTreeBranch}
				<li {if $smarty.foreach.cmsTreeBranch.last}class="last"{/if} ><a href="{$cms.link|escape:'htmlall':'UTF-8'}" title="{$cms.meta_title|escape:'htmlall':'UTF-8'}">{$cms.meta_title|escape:'htmlall':'UTF-8'}</a></li>
			{/foreach}
		{/if}
		</ul>
	{elseif isset($node.cms) && $node.cms|@count > 0}
		<ul>
		{foreach from=$node.cms item=cms name=cmsTreeBranch}
			<li {if $smarty.foreach.cmsTreeBranch.last}class="last"{/if} ><a href="{$cms.link|escape:'htmlall':'UTF-8'}" title="{$cms.meta_title|escape:'htmlall':'UTF-8'}">{$cms.meta_title|escape:'htmlall':'UTF-8'}</a></li>
		{/foreach}
		</ul>
	{/if}
</li>
