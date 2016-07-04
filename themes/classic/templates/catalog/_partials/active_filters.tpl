{if $activeFilters|count}
  <section class="active_filters">
  <h1 class="h6 active-filter-title">{l s='Active filters' d='Shop.Theme'}</h1>
    <ul>
      {foreach from=$activeFilters item="filter"}
        <li class="filter-block">{l s='%1$s: ' d='Shop.Theme.Catalog' sprintf=[$filter.facetLabel]} {$filter.label} <a class="js-search-link" href="{$filter.nextEncodedFacetsURL}"><i class="material-icons close">&#xE5CD;</i></a></li>
      {/foreach}
    </ul>
  </section>
{/if}
