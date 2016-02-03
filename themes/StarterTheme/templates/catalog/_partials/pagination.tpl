<nav class="pagination">
  {l s='Showing %s-%s of %s item(s)' sprintf=[$pagination.items_shown_from ,$pagination.items_shown_to, $pagination.total_items]}
  <ul>
    {foreach from=$pagination.pages item="page"}
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
