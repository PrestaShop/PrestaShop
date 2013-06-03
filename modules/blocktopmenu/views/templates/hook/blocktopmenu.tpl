{if count($menu_items) > 0}
	<!-- Menu -->
	<div class="sf-contener clearfix">
		<ul class="sf-menu clearfix">
			{foreach from=$menu_items item="link"}
				<li {if $link.selected}class="sfHoverForce"{/if}>
					<a href="{$link.href}" {if isset($link.new_window) && $link.new_window}target="_blank"{/if}>
						{$link.label}
					</a>
					{if isset($link.submenu) && is_array($link.submenu) && count($link.submenu) > 0}
						{include file="$self/views/templates/hook/blocktopmenu-submenu.tpl" links=$link.submenu}
					{/if}
				</li>
			{/foreach}
			{if $MENU_SEARCH}
				<li class="sf-search noBack" style="float:right">
					<form id="searchbox" action="{$link->getPageLink('search')}" method="get">
						<p>
							<input type="hidden" name="controller" value="search" />
							<input type="hidden" value="position" name="orderby"/>
							<input type="hidden" value="desc" name="orderway"/>
							<input type="text" name="search_query" value="{if isset($smarty.get.search_query)}{$smarty.get.search_query|escape:'htmlall':'UTF-8'}{/if}" />
						</p>
					</form>
				</li>
			{/if}
		</ul>
	</div>
	<div class="sf-right">&nbsp;</div>
	<!--/ Menu -->
{/if}