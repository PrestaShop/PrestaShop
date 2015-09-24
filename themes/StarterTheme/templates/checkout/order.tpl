<!doctype html>
<html lang="{$language.iso_code}">

  <head>
    {block name="head"}
      {include file="_partials/head.tpl"}
    {/block}
  </head>

  <body id="{$page.page_name}">

    {block name="header"}
      {include file="checkout/_partials/header.tpl"}
    {/block}

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
          <section id="content" class="page-content page-order">
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

    {block name="footer"}
      {include file="checkout/_partials/footer.tpl"}
    {/block}

  </body>

</html>
