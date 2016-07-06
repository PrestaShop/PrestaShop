<nav class="pagination">
  <div class="col-md-4">
    {l s='Showing %from%-%to% of %total% item(s)' d='Shop.Theme.Catalog' sprintf=['%from%' => $pagination.items_shown_from ,'%to%' => $pagination.items_shown_to, '%total%' => $pagination.total_items]}
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
                <i class="material-icons">&#xE314;</i>{l s='Previous' d='Shop.Theme.Actions'}
              {elseif $page.type === 'next'}
                {l s='Next' d='Shop.Theme.Actions'}<i class="material-icons">&#xE315;</i>
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
