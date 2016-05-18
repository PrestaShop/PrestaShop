{function name="menu" nodes=[] depth=0 parent=null}
    {if $depth === 1}
      <a class="top-menu-link" href="{$node.url nofilter}" title="{$node.label}">{$node.label}</a>
    {/if}
    {if $nodes|count}
      <ul data-depth="{$depth}" class="top-menu">
        {foreach from=$nodes item=node}
          {if $node.children|count}
            <li class="{$node.type}{if $node.current} current {/if}">
              <a class="{if $depth >= 0}dropdown-item{/if}{if $depth === 1} dropdown-submenu{/if}"
                 {if $depth === 0} data-toggle="dropdown" {/if}
                   href="{$node.url nofilter}" data-depth="{$depth}"
                 {if $node.open_in_new_window} target="_blank" {/if}
              >
                {$node.label}
              </a>
              <div {if $depth === 0} class="dropdown-menu sub-menu" {/if}>
                {menu nodes=$node.children depth=$node.depth parent=$node}
              </div>
            </li>
          {else}
            <li>
              <a href="{$node.url nofilter}" data-depth="{$depth}"
                 {if $node.open_in_new_window} target="_blank" {/if}
              >
                {$node.label}
              </a>
            </li>
          {/if}
        {/foreach}
      </ul>
    {/if}
{/function}

<div class="menu col-md-8 js-top-menu">
    {menu nodes=$menu.children}
</div>
