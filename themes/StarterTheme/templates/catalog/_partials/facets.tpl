<div id="search_filters">
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
  {foreach from=$facets item="facet"}
    {if $facet.displayed}
      <section class="facet">
        <h1 class="h3">{$facet.label}</h1>
        {if $facet.widgetType !== 'dropdown'}
          <ul>
            {foreach from=$facet.filters item="filter"}
              {if $filter.displayed}
                <li>
                  <label>
                    {if $facet.multipleSelectionAllowed}
                      <input
                        data-search-url="{$filter.nextEncodedFacetsURL}"
                        {if !$jsEnabled} class="ps-shown-by-js" {/if}
                        type="checkbox"
                        {if $filter.active } checked {/if}
                      >
                    {else}
                      <input
                        data-search-url="{$filter.nextEncodedFacetsURL}"
                        {if !$jsEnabled} class="ps-shown-by-js" {/if}
                        type="radio"
                        name="filter {$facet.label}"
                        {if $filter.active } checked {/if}
                      >
                    {/if}

                    <a
                      href="{$filter.nextEncodedFacetsURL}"
                      class="js-search-link"
                    >
                      {$filter.label}
                      {if $filter.magnitude}
                        <span class="magnitude">{$filter.magnitude}</span>
                      {/if}
                    </a>
                  </label>
                </li>
              {/if}
            {/foreach}
          </ul>
        {else}
          <form>
            <input type="hidden" name="order" value="{$sort_order}">
            <select name="q">
              <option disabled selected hidden>{l s='(no filter)'}</option>
              {foreach from=$facet.filters item="filter"}
                {if $filter.displayed}
                  <option
                    {if $filter.active}
                      selected
                      value="{$smarty.get.q}"
                    {else}
                      value="{$filter.nextEncodedFacets}"
                    {/if}
                  >
                    {$filter.label}
                    {if $filter.magnitude}
                      ({$filter.magnitude})
                    {/if}
                  </option>
                {/if}
              {/foreach}
            </select>
            {if !$jsEnabled}
              <button class="ps-hidden-by-js" type="submit">
                {l s='Filter'}
              </button>
            {/if}
          </form>
        {/if}
      </section>
    {/if}
  {/foreach}
</div>
