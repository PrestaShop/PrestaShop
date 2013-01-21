{if $MENU != ''}
<div id="topmenu">
	<ul class="sf-menu clearfix">
		{$MENU}
		{if $MENU_SEARCH}
		<li class="sf-search noBack">
			<form id="searchbox" action="search.php" method="get">
				<input type="hidden" value="position" name="orderby"/>
				<input type="hidden" value="desc" name="orderway"/>
				<input type="text" name="search_query" value="{if isset($smarty.get.search_query)}{$smarty.get.search_query|escape:'htmlall':'UTF-8'}{/if}" />
			</form>
		</li>
		{/if}
	</ul>
</div>
{/if}