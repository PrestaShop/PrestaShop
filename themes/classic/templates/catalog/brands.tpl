{extends file=$layout}

{block name='content'}
  <section id="main">

    {block name='brand_header'}
      <h1>{l s='Brands' d='Shop.Theme.Catalog'}</h1>
    {/block}

    {block name='brand_miniature'}
      <ul>
        {foreach from=$brands item=brand}
          {include file='catalog/_partials/miniatures/brand.tpl' brand=$brand}
        {/foreach}
      </ul>
    {/block}

  </section>

{/block}
