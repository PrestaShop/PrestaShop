<!-- block seach mobile -->
{if isset($hook_mobile)}
<div id="search_block_top" data-role="fieldcontain">
	<form method="get" action="{$link->getPageLink('search')}" id="searchbox">
    <p >
        <input type="hidden" name="controller" value="search" />
	    <input type="hidden" name="orderby" value="position" />
		<input type="hidden" name="orderway" value="desc" />
        		<input class="search_query" type="search" id="search_query_top" name="search_query" placeholder="{l s='Search' mod='blocksearch'}" value="{if isset($smarty.get.search_query)}{$smarty.get.search_query|htmlentities:$ENT_QUOTES:'utf-8'|stripslashes}{/if}" />
		<a href="javascript:document.getElementById('searchbox').submit();">{l s='Search' mod='blocksearch'}</a>
</p>
	</form>
</div>
{else}
<!-- Block search module TOP -->
<div id="search_block_top">
	<form method="get" action="{$link->getPageLink('search')}" id="searchbox">
		<p>
			<input type="hidden" name="controller" value="search" />
			<input type="hidden" name="orderby" value="position" />
			<input type="hidden" name="orderway" value="desc" />
			<input class="search_query" type="text" id="search_query_top" name="search_query" value="{if isset($smarty.get.search_query)}{$smarty.get.search_query|htmlentities:$ENT_QUOTES:'utf-8'|stripslashes}{/if}" />
			<a href="javascript:document.getElementById('searchbox').submit();">{l s='Search' mod='blocksearch'}</a>
	</p>
	</form>
</div>
{include file="$self/blocksearch-instantsearch.tpl"}
{/if}
<!-- /Block search module TOP -->
