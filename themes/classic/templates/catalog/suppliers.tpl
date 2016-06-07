{extends file=$layout}

{block name='content'}
  <section id="main">

    {block name='supplier_header'}
      <h1>{l s='Suppliers' d='Shop.Theme.Catalog'}</h1>
    {/block}

    {block name='supplier-miniature'}
      <ul>
        {foreach from=$suppliers item=supplier}
          {include file='catalog/_partials/miniatures/supplier.tpl' brand=$supplier}
        {/foreach}
      </ul>
    {/block}

  </section>

{/block}
