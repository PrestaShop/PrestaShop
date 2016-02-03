{if $activeFilters|count}
  <section class="active_filters _margin-bottom-medium">
  <h1 class="h3 _gray-darker active-filter-title _margin-right-small">{l s='Enabled filters'}</h1>
    <ul>
      {foreach from=$activeFilters item="filter"}
        <li class="filter-block _gray-darker _margin-right-small _margin-bottom-small">{l s='%1$s: ' sprintf=[$filter.facetLabel]} {$filter.label} <a class="js-search-link" href="{$filter.nextEncodedFacetsURL}"><i class="material-icons close">&#xE5CD;</i></a></li>
      {/foreach}
    </ul>
  </section>
{/if}
