<li>
  <a id="{$node.id}" href="{$node.url}" title="{$node.label}">{$node.label}</a>
  {if isset($node.children) && $node.children|@count > 0}
    <ul>
      {foreach $node.children as $child}
        {include file='cms/_partials/sitemap-tree-branch.tpl' node=$child}
      {/foreach}
    </ul>
  {/if}
</li>
