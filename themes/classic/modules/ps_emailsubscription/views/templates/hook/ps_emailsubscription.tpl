{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="block_newsletter col-md-7">
  <div class="row">
    <p class="col-md-5">{l s='Get our latest news and special sales' d='Shop.Theme'}</p>

    <div class="col-md-7">
      <form action="{$urls.pages.index}#footer" method="post">
        <div class="row">
          <div class="col-md-8">
            <input
              name="email"
              type="text"
              value="{$value}"
              placeholder="{l s='Your email address' d='Shop.Forms.Labels'}"
            >
          </div>
          <div class="col-md-4">
            <input
              class="btn btn-primary"
              name="submitNewsletter"
              type="submit"
              value="{l s='Subscribe' d='Shop.Theme.Actions'}"
            >
            <input type="hidden" name="action" value="0">
          </div>
          <div class="col-md-12">
              {if $need_confirmation}
                <span class="custom-checkbox">
                  <input type="checkbox" name="confirm-optin" value="1" required>
                  <span><i class="material-icons checkbox-checked"></i></span>
                  <label>
                    {l
                      s='I want to receive the free newsletter and have read and accepted the [1]conditions[/1].'
                      tags=['<a data-toggle="modal" data-target="#ps_emailsubscription-modal">']
                      d='Shop.Theme'
                    }
                  </label>
                </span>
              {/if}
              {if $msg}
                <p class="text-warning notification {if $nw_error}notification-error{else}notification-success{/if}">
                  {$msg}
                </p>
              {/if}
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
