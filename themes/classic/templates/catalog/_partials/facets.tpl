<div id="search_filters" class="_margin-bottom-medium">
  <h4 class="h5 facets-title">{l s='Filter By'}</h4>
  {foreach from=$facets item="facet"}
    {if $facet.displayed}
      <section class="facet">
        <h1 class="h6 facet-title">{$facet.label}</h1>
        {if $facet.widgetType !== 'dropdown'}
          <ul>
            {foreach from=$facet.filters item="filter"}
              {if $filter.displayed}
                <li>
                  <label class="facet-label">
                    {if $facet.multipleSelectionAllowed}
                      <input
                        id="facet-checkbox"
                        data-search-url="{$filter.nextEncodedFacetsURL}"
                        type="checkbox"
                        {if $filter.active } checked {/if}
                      >
                      <span {if !$js_enabled} class="ps-shown-by-js" {/if}><i class="material-icons check">&#xE5CA;</i></span>
                    {else}
                      <input
                        id="facet-checkbox"
                        data-search-url="{$filter.nextEncodedFacetsURL}"
                        type="radio"
                        name="filter {$facet.label}"
                        {if $filter.active } checked {/if}
                      >
                      <span {if !$js_enabled} class="ps-shown-by-js" {/if}><i class="material-icons check">&#xE5CA;</i></span>
                    {/if}

                    <a
                      href="{$filter.nextEncodedFacetsURL}"
                      class="_gray-darker search-link js-search-link {if $filter.active} active {/if}"
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
            {if !$js_enabled}
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
