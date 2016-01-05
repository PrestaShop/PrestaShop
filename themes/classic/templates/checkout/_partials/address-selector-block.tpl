<form method="POST" action="{$urls.pages.order}" class="address-selector js-address-selector">
  {foreach $addresses as $address}
    <article id="{$name|classname}-address-{$address.id}" class="address-item">
      <header class="h4">
        {$address.alias}
      </header>

      <label class="radio-block">
          <input type="radio" name="{$name}" value="{$address.id}" {if $address.id == $selected}checked{/if} />
          {$address.formatted nofilter}
      </label>

      <footer>
        {if $interactive}
          <a data-link-action="edit-address" href="?editAddress={$type}&amp;id_address={$address.id}">
            {l s='Edit'}
          </a>
        {/if}
      </footer>
    </article>
  {/foreach}
  {if $interactive}
    <p>
      <button class="ps-hidden-by-js" type="submit">{l s='Save'}</button>
    </p>
  {/if}
</form>
