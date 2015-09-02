{extends "layout.tpl"}

{block name="content"}

  <section id="main">

    {block name="page_header_before"}{/block}
    {block name="page_header"}
      <header class="page_header">
        <h1>{$page.title}</h1>
      </header>
    {/block}
    {block name="page_header_after"}{/block}

    {block name="page_content_before"}{/block}
    {block name="page_content"}
      <section id="content" class="page_content">
        <!-- Page content -->
      </section>
    {/block}
    {block name="page_content_after"}{/block}

    {block name="page_content_before"}{/block}
    {block name="page_content"}
      <footer class="page_footer">
        <!-- Footer content -->
      </footer>
    {/block}
    {block name="page_content_after"}{/block}

  </section>

{/block}
