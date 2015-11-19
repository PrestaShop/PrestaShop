<nav class="pagination">
  <ul>
    {foreach from=$pagination item="page"}
      <li {if $page.current} class="current" {/if}>
        {if $page.type === 'spacer'}
          <span class="spacer">&hellip;</span>
        {else}
          <form method="GET">
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
