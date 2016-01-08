{extends file="$layout"}

{block name="content"}

  <section id="main">

    {block name="page_header_container"}
      <header class="page-header">
        {block name="page_header"}
          <h1>{block name="page_title"}{/block}</h1>
        {/block}
      </header>
    {/block}

    {block name="page_content_container"}
      <section id="content" class="page-content">
        {block name="page_content"}
          <!-- Page content -->
        {/block}
      </section>
    {/block}

    {block name="page_footer_container"}
      <footer class="page-footer">
        {block name="page_footer"}
          <!-- Footer content -->
        {/block}
      </footer>
    {/block}

  </section>

{/block}
