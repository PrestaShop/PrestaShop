<section id="js-active-search-filters" class="{if $activeFilters|count}active_filters{else}hide{/if}">
  <h1 class="h6 active-filter-title">{l s='Active filters' d='Shop.Theme'}</h1>
  {if $activeFilters|count}
    <ul>
      {foreach from=$activeFilters item="filter"}
        <li class="filter-block">{l s='%1$s: ' d='Shop.Theme.Catalog' sprintf=[$filter.facetLabel]} {$filter.label} <a class="js-search-link" href="{$filter.nextEncodedFacetsURL}"><i class="material-icons close">&#xE5CD;</i></a></li>
      {/foreach}
    </ul>
  {/if}
</section>
