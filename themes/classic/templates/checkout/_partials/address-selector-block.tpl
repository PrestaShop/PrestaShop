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
        <a  class="edit-address"
            data-link-action="edit-address"
            href="{url entity='order' params=['id_address' => $address.id, 'editAddress' => $type, 'token' => $token]}">
          <i class="material-icons edit">&#xE254;</i>{l s='Edit' d='Shop.Theme.Actions'}
        </a>
        <a  class="delete-address"
            data-link-action="delete-address"
          href="{url entity='order' params=['id_address' => $address.id, 'deleteAddress' => true, 'token' => $token]}">
          <i class="material-icons delete">&#xE872;</i>{l s='Delete' d='Shop.Theme.Actions'}
        </a>
      {/if}
    </footer>
  </article>
{/foreach}
{if $interactive}
  <p>
    <button class="ps-hidden-by-js form-control-submit center-block" type="submit">{l s='Save' d='Shop.Theme.Actions'}</button>
  </p>
{/if}
