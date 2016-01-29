{if $activeFilters|count}
  <section class="active_filters">
  <h1 class="h3">{l s='Active Filters'}</h1>
    <ul>
      {foreach from=$activeFilters item="filter"}
        <li>{l s='%1$s: ' sprintf=[$filter.facetLabel]} {$filter.label} <a  class="js-search-link" href="{$filter.nextEncodedFacetsURL}">{l s='Remove'}</a></li>
      {/foreach}
    </ul>
  </section>
{/if}
