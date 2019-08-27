<div class="variant-links">
  {foreach from=$variants item=variant}
    <a href="{$variant.url}"
       class="{$variant.type}"
       title="{$variant.name}"
       aria-label="{$variant.name}"
      {if $variant.html_color_code} style="background-color: {$variant.html_color_code}" {/if}
      {if $variant.texture} style="background-image: url({$variant.texture})" {/if}
    ></a>
  {/foreach}
  <span class="js-count count"></span>
</div>
