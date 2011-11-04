{extends file="helper/list/list_header.tpl"}
{block name=leadin}
{l s='Go to category:'}
<select id="go_to_categ" name="go_to_categ">
{foreach from=$category_tree item=categ}
	<option value="{$categ->id}" {if $categ->selected}selected="selected"{/if} >
		{$categ->dashes}{$categ->name} ({$categ->id})
	</option>
{/foreach}
</select>
{/block}
