<!doctype html>
<html lang="">

  <head>
    {block name="head"}
      {include file="_partials/head.tpl"}
    {/block}
  </head>

  <body>
    <!--[if lt IE 8]>
    <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->

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
