{if $MENU != ''}
	</div>

	<!-- Menu -->
	<div class="sf-contener clearfix">
		<ul class="sf-menu clearfix">
			{$MENU}
			{if $MENU_SEARCH}
				<li class="sf-search noBack" style="float:right">
					<form id="searchbox" action="search.php" method="get">
						<input type="hidden" value="position" name="orderby"/>
						<input type="hidden" value="desc" name="orderway"/>
						<input type="text" name="search_query" value="{if isset($smarty.get.search_query)}{$smarty.get.search_query}{/if}" />
					</form>
				</li>
			{/if}
		</ul>
		<div class="sf-right">&nbsp;</div>

	<!--/ Menu -->
{/if}