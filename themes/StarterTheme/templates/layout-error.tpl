<!doctype html>
<html lang="">

  <head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    {block name="head_seo"}
      <title>{block name="head_seo_title"}{/block}</title>
      <meta name="description" content="{block name='head_seo_description'}{/block}" />
      <meta name="keywords" content="{block name='head_seo_keywords'}{/block}" />
    {/block}
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    {* StarterTheme: favicon.ico and apple icons *}
  </head>

  <body>
    <!--[if lt IE 8]>
    <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->

    {block name="content"}
      <p>Hello world! This is HTML5 Boilerplate.</p>
    {/block}

    <!-- Load JS files here -->

  </body>

</html>
