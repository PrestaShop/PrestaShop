{extends $layout}

{block name="content"}
  <section id="main">

    {block name="supplier_header"}
      <h1>{l s="Suppliers"}</h1>
    {/block}

    {block name="supplier-miniature"}
      <ul>
        {foreach from=$suppliers item=supplier}
          {include file="./supplier-miniature.tpl" brand=$supplier}
        {/foreach}
      </ul>
    {/block}

  </section>

{/block}
