{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
{if count($categories) && isset($categories)}
	<script type="text/javascript">
		var inputName = '{$categories.input_name|@addcslashes:'\''}';
		var use_radio = {if $categories.use_radio}1{else}0{/if};
		var selectedCat = {$categories.selected_cat|@implode|intval};
		var selectedLabel = '{$categories.trads.selected|@addcslashes:'\''}';
		var home = '{$categories.trads.Root.name|@addcslashes:'\''}';
		var use_radio = {if $categories.use_radio}1{else}0{/if};
		var use_context = {if isset($categories.use_context)}1{else}0{/if};
	</script>
<div class="panel">
	<div class="category-filter panel-heading">
		<a href="#" id="collapse_all" class="btn btn-link"><i class="icon-collapse-alt icon-large"></i> {$categories.trads['Collapse all']}</a>
		<a href="#" id="expand_all" class="btn btn-link"><i class="icon-expand-alt icon-large"></i> {$categories.trads['Expand all']}</a>
		{if !$categories.use_radio}
		<a href="#" id="check_all" class="btn btn-link"><i class="icon-check-sign"></i> {$categories.trads['Check all']}</a>
		<a href="#" id="uncheck_all" class="btn btn-link"><i class="icon-check-empty"></i> {$categories.trads['Uncheck all']}</a>
		{/if}
		{if $categories.use_search}
			<span>
				{$categories.trads.search}:&nbsp;
				<form method="post" id="filternameForm">
					<input type="text" name="search_cat" id="search_cat"/>
				</form>
			</span>
		{/if}
	</div>
	{assign var=home_is_selected value=false}
	{foreach $categories.selected_cat AS $cat}
		{if is_array($cat)}
			{if $cat.id_category != $categories.trads.Root.id_category}
				<input {if in_array($cat.id_category, $categories.disabled_categories)}disabled="disabled"{/if} type="hidden" name="{$categories.input_name}" value="{$cat.id_category}"/>
			{else}
				{assign var=home_is_selected value=true}
			{/if}
		{else}
			{if $cat != $categories.trads.Root.id_category}
				<input {if in_array($cat, $categories.disabled_categories)}disabled="disabled"{/if} type="hidden" name="{$categories.input_name}" value="{$cat}"/>
			{else}
				{assign var=home_is_selected value=true}
			{/if}
		{/if}
	{/foreach}
	<ul id="categories-treeview" class="filetree">
		<li id="{$categories.trads.Root.id_category}" class="hasChildren">
			<span class="folder">
				{if $categories.top_category->id != $categories.trads.Root.id_category}
					<input type="{if !$categories.use_radio}checkbox{else}radio{/if}"
							name="{$categories.input_name}"
							value="{$categories.trads.Root.id_category}"
							{if $home_is_selected}checked="checked"{/if}
							onclick="clickOnCategoryBox($(this));"/>
						<span class="category_label">{$categories.trads.Root.name}</span>
				{else}
					&nbsp;
				{/if}
			</span>
			<ul>
				<li><span class="placeholder">&nbsp;</span></li>
		  	</ul>
		</li>
	</ul>
	{if $categories.use_radio}
	<script type="text/javascript">
		searchCategory();
	</script>
	{/if}
</div>
{/if}
