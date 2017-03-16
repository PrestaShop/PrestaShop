<div class="variant-links">
  {foreach from=$variants item=variant}
    <a href="{$variant.url}"
       class="{$variant.type}"
       title="{$variant.name}"
       {*
          TODO:
            put color in a data attribute for use with attr() as soon as browsers support it,
            see https://developer.mozilla.org/en/docs/Web/CSS/attr
        *}
      {if $variant.html_color_code} style="background-color: {$variant.html_color_code}" {/if}
      {if $variant.texture} style="background-image: url({$variant.texture})" {/if}
    ><span class="sr-only">{$variant.name}</span></a>
  {/foreach}
  <span class="js-count count"></span>
</div>
