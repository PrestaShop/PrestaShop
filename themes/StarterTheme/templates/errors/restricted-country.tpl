{extends file="layout/layout-error.tpl"}

{block name="content"}

  <section id="main">

    {block name="page_header_container"}
      <header class="page-header">
        {block name="page_header"}
          <h1>{block name="page_title"}{$shop_name}{/block}</h1>
        {/block}
      </header>
    {/block}

    {block name="page_content_container"}
      <section id="content" class="page-content page-restricted">
        {block name="page_content"}
          <h2>{l s="403 Forbidden"}</h2>
          <div class="logo"><img src="{$logo_url}" alt="logo" /></div>
          <p>{l s="You cannot access this store from your country. We apologize for the inconvenience."}</p>
        {/block}
      </section>
    {/block}

    {block name="page_footer_container"}

    {/block}

  </section>

{/block}
