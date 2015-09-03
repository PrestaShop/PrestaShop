{extends "page.tpl"}

{block name="head_seo_title"}{$cms.meta_title}{/block}
{block name='head_seo_description'}{$cms.meta_description}{/block}
{block name='head_seo_keywords'}{$cms.meta_keywords}{/block}

{block name="page_title"}
  {$cms.meta_title}
{/block}

{block name="page_content_container"}
  <section id="content" class="page-content page-cms page-cms-{$cms.id}">
    {$cms.content}
  </section>
{/block}
