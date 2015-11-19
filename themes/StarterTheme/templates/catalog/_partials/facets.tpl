<div id="search_filters">
  {foreach from=$facets item="facet"}
    <section class="facet">
      <h1 class="h3">{$facet.label}</h1>
      {if $facet.multipleSelectionAllowed}
        <ul>
          {foreach from=$facet.filters item="filter"}
            <li>
              <label>
                <input type="checkbox" {if $filter.active } checked {/if}>
                <a href="{$filter.nextEncodedFacetsURL}">{$filter.label} <span class="magnitude">{$filter.magnitude}</span></a>
              </label>
            </li>
          {/foreach}
        </ul>
      {else}
        NOT IMPLEMENTED YET
      {/if}
    </section>
  {/foreach}
</div>
