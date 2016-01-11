{function name="menu" nodes=[] depth=0}
  {strip}
    {if $nodes|count}
      <ul data-depth="{$depth}" class="top-menu{if $depth === 0} js-top-menu{/if}">
        {foreach from=$nodes item=node}
          <li class="{$node.type}{if $node.current} current{/if}">
            <a href="{$node.url nofilter}" {if $node.open_in_new_window} target="_blank" {/if} data-depth="{$depth}">{$node.label}</a>
            <div>
              {menu nodes=$node.children depth=$node.depth}
            </div>
          </li>
        {/foreach}
      </ul>
    {/if}
  {/strip}
{/function}

<div class="menu">
    {menu nodes=$menu.children}
</div>
