{extends "page.tpl"}

{block name="page_title"}
  {l s="Sitemap"}
{/block}

{block name="page_content_container"}
  <div id="sitemap-tree" class="sitemap">
    <div class="tree-top">
      <a href="{$urls.base_url}" title="{$shop.name}"></a>
    </div>
    <ul class="tree">
      {foreach $sitemap as $item}
        {if isset($item.children)}
          {foreach $item.children as $child}
            {include file="cms/_partials/sitemap-tree-branch.tpl" node=$child}
          {/foreach}
        {/if}
      {/foreach}
    </ul>
  </div>
{/block}
