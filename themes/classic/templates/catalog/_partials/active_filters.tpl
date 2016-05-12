{if $activeFilters|count}
  <section class="active_filters">
  <h1 class="h4 active-filter-title">{l s='Enabled filters'}</h1>
    <ul>
      {foreach from=$activeFilters item="filter"}
        <li class="filter-block">{l s='%1$s: ' sprintf=[$filter.facetLabel]} {$filter.label} <a class="js-search-link" href="{$filter.nextEncodedFacetsURL}"><i class="material-icons close">&#xE5CD;</i></a></li>
      {/foreach}
    </ul>
  </section>
{/if}
