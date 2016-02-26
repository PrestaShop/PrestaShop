<form method="POST" action="{$urls.pages.order}" class="address-selector js-address-selector">
  {foreach $addresses as $address}
    <article id="{$name|classname}-address-{$address.id}" class="address-item{if $address.id == $selected} selected{/if}">
      <header class="h4">
        <label class="radio-block">
          <span class="custom-radio">
            <input type="radio" name="{$name}" value="{$address.id}" {if $address.id == $selected}checked{/if}>
            <span></span>
          </span>
          <span class="address-alias">{$address.alias}</span>
          <div class="address">{$address.formatted nofilter}</div>
        </label>
      </header>
      <hr>
      <footer class="address-footer">
        {if $interactive}
          <a class="edit-address" data-link-action="edit-address" href="{$address.url_order_address}&amp;editAddress={$type}">
            <i class="material-icons edit">&#xE254;</i>{l s='Edit'}
          </a>
          <a class="delete-address" data-link-action="delete-address" href="{$address.url_order_address}&amp;deleteAddress=1">
            <i class="material-icons delete">&#xE872;</i>{l s='Delete'}
          </a>
        {/if}
      </footer>
    </article>
  {/foreach}
  {if $interactive}
    <p>
      <button class="ps-hidden-by-js form-control-submit center-block" type="submit">{l s='Save'}</button>
    </p>
  {/if}
</form>
