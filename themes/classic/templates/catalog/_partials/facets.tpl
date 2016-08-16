  <div id="search_filters">
    <h4 class="text-uppercase h6 hidden-sm-down">{l s='Filter By' d='Shop.Theme.Actions'}</h4>
    {foreach from=$facets item="facet"}
      {if $facet.displayed}
        <section class="facet">
          <h1 class="h6 facet-title">{$facet.label}</h1>
          <span class="pull-xs-right hidden-md-up">
            {assign var=_expand_id value=10|mt_rand:100000}
            <span data-target="#facet_{$_expand_id}" data-toggle="collapse" class="navbar-toggler collapse-icons">
              <i class="material-icons add">&#xE313;</i>
              <i class="material-icons remove">&#xE316;</i>
            </span>
          </span>
          {if $facet.widgetType !== 'dropdown'}
            <ul id="facet_{$_expand_id}" class="collapse">
              {foreach from=$facet.filters item="filter"}
                {if $filter.displayed}
                  <li>
                    <label class="facet-label{if $filter.active} active {/if}">
                      {if $facet.multipleSelectionAllowed}
                        <span class="custom-checkbox">
                          <input
                            data-search-url="{$filter.nextEncodedFacetsURL}"
                            type="checkbox"
                            {if $filter.active } checked {/if}
                          >
                          {if isset($filter.properties.color)}
                            <span class="color" style="background-color:{$filter.properties.color}"></span>
                            {elseif isset($filter.properties.texture)}
                              <span class="color texture" style="background-image:url({$filter.properties.texture})"></span>
                            {else}
                            <span {if !$js_enabled} class="ps-shown-by-js" {/if}><i class="material-icons checkbox-checked">&#xE5CA;</i></span>
                          {/if}
                        </span>
                      {else}
                        <span class="custom-checkbox">
                          <input
                            data-search-url="{$filter.nextEncodedFacetsURL}"
                            type="radio"
                            name="filter {$facet.label}"
                            {if $filter.active } checked {/if}
                          >
                          <span {if !$js_enabled} class="ps-shown-by-js" {/if}><i class="material-icons checkbox-checked">&#xE5CA;</i></span>
                        </span>
                      {/if}

                      <a
                        href="{$filter.nextEncodedFacetsURL}"
                        class="_gray-darker search-link js-search-link"
                      >
                        {$filter.label}
                        {if $filter.magnitude}
                          <span class="magnitude">({$filter.magnitude})</span>
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
                <option disabled selected hidden>{l s='(no filter)' d='Shop.Theme'}</option>
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
                  {l s='Filter' d='Shop.Theme.Actions'}
                </button>
              {/if}
            </form>
          {/if}
        </section>
      {/if}
    {/foreach}
  </div>
