{extends file="$layout"}

{block name="content"}
  <section id="main">

    {block name="manufacturer_header"}
      <h1>{l s="Manufacturers"}</h1>
    {/block}

    {block name="manufacturer_miniature"}
      <ul>
        {foreach from=$manufacturers item=manufacturer}
          {include file="catalog/manufacturer-miniature.tpl" brand=$manufacturer}
        {/foreach}
      </ul>
    {/block}

  </section>

{/block}
