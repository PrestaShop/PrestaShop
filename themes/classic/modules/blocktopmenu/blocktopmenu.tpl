{function name="menu" nodes=[] depth=0 parent=null}
  {strip}
    {if $depth === 1}
      <a href="{$node.url nofilter}">{$node.label}</a>
    {/if}
    {if $nodes|count}
      <ul aria-labelledby="dLabel" data-depth="{$depth}" class="top-menu">
        {foreach from=$nodes item=node}
          {if $node.children|count}
            <li class="{$node.type}{if $node.current} current {/if}">
              <a {if $depth === 0} data-toggle="dropdown" {/if}
                 class = "{if $depth >= 0}dropdown-item{/if}{if $depth === 1} dropdown-submenu{/if}"
                 href  = "{$node.url nofilter}"
                 {if $node.open_in_new_window} target="_blank" {/if}
                 data-depth="{$depth}"
              >
                {$node.label}
              </a>
              <div {if $depth === 0} class="dropdown-menu sub-menu" {/if}>
                {menu nodes=$node.children depth=$node.depth parent=$node}
              </div>
            </li>
          {else}
            <a href  = "{$node.url nofilter}"
               {if $node.open_in_new_window} target="_blank" {/if}
               data-depth="{$depth}"
            >
              {$node.label}
            </a>
          {/if}
        {/foreach}
      </ul>
    {/if}
  {/strip}
{/function}

<div class="menu col-md-8 js-top-menu">
    {menu nodes=$menu.children}
</div>
