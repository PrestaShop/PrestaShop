<nav class="pagination">
  <div class="col-md-3">
    {l s='Showing'}
  </div>
  <div class="col-md-6">
    <ul class="page-list clearfix text-xs-center">
      {foreach from=$pagination item="page"}
        <li {if $page.current} class="current" {/if}>
          {if $page.type === 'spacer'}
            <span class="spacer">&hellip;</span>
          {else}
            <a
              rel="nofollow"
              href="{$page.url}"
              class="{if $page.type === 'previous'}previous {elseif $page.type === 'next'}next {/if}{['disabled' => !$page.clickable, 'js-search-link' => true]|classnames}"
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
  </div>
</nav>
