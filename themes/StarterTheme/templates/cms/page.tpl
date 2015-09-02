{extends "page.tpl"}

{block name="content"}

  {block name="page_title"}
    <h1>{$cms.meta_title}</h1>
  {/block}

  {block name="page_content"}
    {$cms.content}
  {/block}

{/block}
