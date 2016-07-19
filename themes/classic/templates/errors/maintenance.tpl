{extends file='layouts/layout-error.tpl'}

{block name='content'}

  <section id="main">

    {block name='page_header_container'}
      <header class="page-header">
        <div class="logo"><img src="{$shop.logo}" alt="logo"></div>
        {$HOOK_MAINTENANCE nofilter}
        {block name='page_header'}
          <h1>{block name='page_title'}{l s='We\'ll be back soon.' d='Shop.Theme'}{/block}</h1>
        {/block}
      </header>
    {/block}

    {block name='page_content_container'}
      <section id="content" class="page-content page-maintenance">
        {block name='page_content'}
          {$maintenance_text nofilter}
        {/block}
      </section>
    {/block}

    {block name='page_footer_container'}

    {/block}

  </section>

{/block}
