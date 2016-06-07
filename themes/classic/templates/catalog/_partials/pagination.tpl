<nav class="pagination">
  <div class="col-md-4">
    {l s='Showing %s-%s of %s item(s)' d='Shop.Theme.Catalog' sprintf=[$pagination.items_shown_from ,$pagination.items_shown_to, $pagination.total_items]}
  </div>
  <div class="col-md-6">
    <ul class="page-list clearfix text-xs-center">
      {foreach from=$pagination.pages item="page"}
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
                {l s='Previous' d='Shop.Theme.Actions'}
              {elseif $page.type === 'next'}
                {l s='Next' d='Shop.Theme.Actions'}
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
