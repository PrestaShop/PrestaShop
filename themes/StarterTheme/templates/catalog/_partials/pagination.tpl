<nav class="pagination">
  <ul>
    {foreach from=$pagination item="page"}
      <li {if $page.current} class="current" {/if}>
        {if $page.type === 'spacer'}
          <span class="spacer">&hellip;</span>
        {else}
          <form method="GET">
            {if $ps_search_encoded_facets}
              <input type="hidden" name="q" value="{$ps_search_encoded_facets}">
            {/if}
            {if $ps_search_sort_order}
              <input type="hidden" name="order" value="{$ps_search_sort_order}">
            {/if}
            <button type="submit" name="page" value="{$page.page}" {if !$page.clickable} disabled {/if}>
              {if $page.type === 'previous'}
                {l s='Previous'}
              {elseif $page.type === 'next'}
                {l s='Next'}
              {else}
                {$page.page}
              {/if}
            </button>
          </form>
        {/if}
      </li>
    {/foreach}
  </ul>
</nav>
