{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
<div class="panel">
	{if isset($header)}{$header}{/if}
	{if isset($nodes)}
	<ul id="{$id|escape:'html':'UTF-8'}" class="cattree tree">
		{$nodes}
	</ul>
	{/if}
</div>
<script type="text/javascript">
	var currentToken="{$token|@addslashes}";
	{if isset($use_checkbox) && $use_checkbox == true}
		function checkAllAssociatedCategories($tree)
		{
			$tree.find(":input[type=checkbox]").each(
				function()
				{
					$(this).prop("checked", true);
					$(this).parent().addClass("tree-selected");
				}
			);
		}

		function uncheckAllAssociatedCategories($tree)
		{
			$tree.find(":input[type=checkbox]").each(
				function()
				{
					$(this).prop("checked", false);
					$(this).parent().removeClass("tree-selected");
				}
			);
		}
	{/if}
	{if isset($use_search) && $use_search == true}
		$("#{$id|escape:'html':'UTF-8'}-categories-search").on("typeahead:selected", function(obj, datum) {
		    $("#{$id|escape:'html':'UTF-8'}").find(":input").each(
				function()
				{
					if ($(this).val() == datum.id_category)
					{
						{if (!(isset($use_checkbox) && $use_checkbox == true))}
							$("#{$id|escape:'html':'UTF-8'} label").removeClass("tree-selected");
						{/if}
						$(this).prop("checked", true);
						$(this).parent().addClass("tree-selected");
						$(this).parents('ul.tree').each(function(){
							$(this).show();
							$(this).prev().find('.icon-folder-close').removeClass('icon-folder-close').addClass('icon-folder-open');
						});
					}
				}
			);
		});
	{/if}
	$(function () {
		$("#{$id|escape:'html':'UTF-8'}").tree("collapseAll");

		{if isset($selected_categories)}
			{assign var=imploded_selected_categories value='","'|implode:$selected_categories}
			var selected_categories = new Array("{$imploded_selected_categories}");

			$("#{$id|escape:'html':'UTF-8'}").find(":input").each(
				function()
				{
					if ($.inArray($(this).val(), selected_categories) != -1)
					{
						$(this).prop("checked", true);
						$(this).parent().addClass("tree-selected");
						$(this).parents('ul.tree').each(function(){
							$(this).show();
							$(this).prev().find('.icon-folder-close').removeClass('icon-folder-close').addClass('icon-folder-open');
						});
					}
				}
			);
		{/if}
	});
</script>
