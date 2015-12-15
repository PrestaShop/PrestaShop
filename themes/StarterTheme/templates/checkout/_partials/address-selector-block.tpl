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
      <a href="{url entity="address" id=$address.id params=['back' => $urls.pages.order]}"
          data-link-action="edit-address"
          data-entity-id="{$address.id}">
        {l s='Edit'}
      </a>
    </footer>
  </article>
{/foreach}
<p>
  <button class="ps-hidden-by-js" type="submit">{l s='Save'}</button>
</p>
