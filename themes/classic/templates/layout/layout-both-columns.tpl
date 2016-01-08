<!doctype html>
<html lang="{$language.iso_code}">

  <head>
    {block name='head'}
      {include file='_partials/head.tpl'}
    {/block}
  </head>

  <body id="{$page.page_name}">

    <main class="container-fluid">
      <header id="header">
        {block name='header'}
          {include file='_partials/header.tpl'}
        {/block}
      </header>

      {block name='notifications'}
        {include file='_partials/notifications.tpl'}
      {/block}

      <div id="wrapper">
        {block name='breadcrumb'}
          {include file='_partials/breadcrumb.tpl'}
        {/block}

        {block name="left_column"}
          <div id="left-column">
            {hook h="displayLeftColumn"}
          </div>
        {/block}

        {block name="right_column"}
          <div id="right-column">
            {hook h="displayRightColumn"}
          </div>
        {/block}

        {block name="content_wrapper"}
          <div id="content-wrapper" class="left-column right-column">
            {block name="content"}
              <p>Hello world! This is HTML5 Boilerplate.</p>
            {/block}
          </div>
        {/block}

      </div>

      <footer id="footer">
        {block name="footer"}
          {include file="_partials/footer.tpl"}
        {/block}
      </footer>

    </main>
    <!-- Load JS files here -->
    {*StarterTheme: This is useful for module debugging, but should be removed before end release.*}
    <script src="http://code.jquery.com/jquery-migrate-1.2.1.js"></script>

  </body>

</html>
