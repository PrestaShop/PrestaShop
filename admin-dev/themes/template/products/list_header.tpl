{extends file="helper/list/list_header.tpl"}
{block name=leadin}
{if isset($category_tree)}
	<script type="text/javascript">
		$(document).ready(function(){
			$('#go_to_categ').bind('change', function(){
				var base_url = '{$base_url}';
				if (this.value !== "")
					location.href = base_url + '&id_category=' + parseInt(this.value);
				else
					location.href = base_url;
			});
		});
	</script>
	{l s='Go to category:'}
	<select id="go_to_categ" name="go_to_categ">
	{foreach from=$category_tree item=categ}
		<option value="{$categ->id}" {if $categ->selected}selected="selected"{/if} >
			{$categ->dashes}{$categ->name}
		</option>
	{/foreach}
	</select>
{/if}
{/block}
