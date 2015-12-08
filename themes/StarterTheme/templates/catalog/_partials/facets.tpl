<form id="search_filters">
  {foreach from=$facets item="facet"}
    <section class="facet">
      <h1 class="h3">{$facet.label}</h1>
      {if $facet.multipleSelectionAllowed}
        <ul>
          {foreach from=$facet.filters item="filter"}
            <li>
              <label>
                <input type="checkbox">
                {$filter.label}
              </label>
            </li>
          {/foreach}
        </ul>
      {else}
        NOT IMPLEMENTED YET
      {/if}
    </section>
  {/foreach}
</form>
