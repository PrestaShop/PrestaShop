<!doctype html>
<html lang="{$language.iso_code}">

  <head>
    {block name="head"}
      {include file="_partials/head.tpl"}
    {/block}
  </head>

  <body id="{$page.page_name}">

    <header id="header">
      {block name="header"}
        {include file="_partials/header.tpl"}
      {/block}
    </header>

    {* StarterTheme: Manage columns *}

    {block name="content"}
      <p>Hello world! This is HTML5 Boilerplate.</p>
    {/block}

    <footer id="footer">
      {block name="footer"}
        {include file="_partials/footer.tpl"}
      {/block}
    </footer>

    <!-- Load JS files here -->

  </body>

</html>
