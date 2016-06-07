{extends file='layouts/layout-error.tpl'}

{block name='content'}

  <section id="main">

    {block name='page_header_container'}
      <header class="page-header">
        <div class="logo"><img src="{$shop.logo}" alt="logo"></div>
        {hook h='displayMaintenance'}
        {block name='page_header'}
          <h1>{block name='page_title'}{l s='We\'ll be back soon.' d='Shop.Theme'}{/block}</h1>
        {/block}
      </header>
    {/block}

    {block name='page_content_container'}
      <section id="content" class="page-content page-maintenance">
        {block name='page_content'}
          {l s='We are currently updating our shop and will be back really soon.' d='Shop.Theme'}
          <br>
          {l s='Thanks for your patience.' d='Shop.Theme'}
        {/block}
      </section>
    {/block}

    {block name='page_footer_container'}

    {/block}

  </section>

{/block}
