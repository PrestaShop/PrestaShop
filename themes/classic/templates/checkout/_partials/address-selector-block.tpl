{**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{foreach $addresses as $address}
  <article
    class="address-item{if $address.id == $selected} selected{/if}"
    id="{$name|classname}-address-{$address.id}"
  >
    <header class="h4">
      <label class="radio-block">
        <span class="custom-radio">
          <input
            type="radio"
            name="{$name}"
            value="{$address.id}"
            {if $address.id == $selected}checked{/if}
          >
          <span></span>
        </span>
        <span class="address-alias h4">{$address.alias}</span>
        <div class="address">{$address.formatted nofilter}</div>
      </label>
    </header>
    <hr>
    <footer class="address-footer">
      {if $interactive}
        <a
          class="edit-address text-muted"
          data-link-action="edit-address"
          href="{url entity='order' params=['id_address' => $address.id, 'editAddress' => $type, 'token' => $token]}"
        >
          <i class="material-icons edit">&#xE254;</i>{l s='Edit' d='Shop.Theme.Actions'}
        </a>
        <a
          class="delete-address text-muted"
          data-link-action="delete-address"
          href="{url entity='order' params=['id_address' => $address.id, 'deleteAddress' => true, 'token' => $token]}"
        >
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
