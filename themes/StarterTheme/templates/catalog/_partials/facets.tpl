<div id="search_filters">
  {foreach from=$facets item="facet"}
    {if $facet.displayed}
      <section class="facet">
        <h1 class="h3">{$facet.label}</h1>
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

                  {if $filter.label}
                    <a
                      href="{$filter.nextEncodedFacetsURL}"
                      class="js-search-link"
                    >
                      {$filter.label}
                      {if $filter.magnitude}
                        <span class="magnitude">{$filter.magnitude}</span>
                      {/if}
                    </a>
                  {/if}
                </label>
              </li>
            {/if}
          {/foreach}
        </ul>
      </section>
    {/if}
  {/foreach}
</div>
