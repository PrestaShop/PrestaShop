<nav class="pagination">
  <ul>
    {foreach from=$pagination item="page"}
      <li {if $page.current} class="current" {/if}>
        {if $page.type === 'spacer'}
          <span class="spacer">&hellip;</span>
        {else}
          <a
            rel="nofollow"
            href="{$page.url}"
            class="{['disabled' => !$page.clickable, 'js-search-link' => true]|classnames}"
          >
            {if $page.type === 'previous'}
              {l s='Previous'}
            {elseif $page.type === 'next'}
              {l s='Next'}
            {else}
              {$page.page}
            {/if}
          </a>
        {/if}
      </li>
    {/foreach}
  </ul>
</nav>
