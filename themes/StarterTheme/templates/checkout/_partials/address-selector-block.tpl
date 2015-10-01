{foreach $addresses as $address}
  <article id="address-{$address.id}" class="address-item">
    <header class="h4">
      {$address.alias}
    </header>

    <label class="radio-block">
        <input type="radio" name="{$name}" value="{$address.id}" />
        {$address.formatted}
    </label>

    <footer>
      <a href="{url entity="address" id=$address.id}">{l s='Edit'}</a>
    </footer>
  </article>
{/foreach}
