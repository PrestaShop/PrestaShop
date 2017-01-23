{**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{extends file='page.tpl'}

{block name='page_title'}
  {l s='Reset your password' d='Shop.Theme.Customeraccount'}
{/block}

{block name='page_content'}
    <form action="{$urls.pages.password}" method="post">

      <section class="form-fields renew-password">

        <div class="email">
          {l
            s='Email address: %email%'
            d='Shop.Theme.Customeraccount'
            sprintf=['%email%' => $customer_email|stripslashes]}
        </div>

        <div class="container-fluid">
          <div class="row form-group">
            <label class="form-control-label col-md-3 offset-md-2">{l s='New password' d='Shop.Forms.Labels'}</label>
            <div class="col-md-4">
              <input class="form-control" type="password" data-validate="isPasswd" name="passwd" value="">
            </div>
          </div>

          <div class="row form-group">
            <label class="form-control-label col-md-3 offset-md-2">{l s='Confirmation' d='Shop.Forms.Labels'}</label>
            <div class="col-md-4">
              <input class="form-control" type="password" data-validate="isPasswd" name="confirmation" value="">
            </div>
          </div>

          <input type="hidden" name="token" id="token" value="{$customer_token}">
          <input type="hidden" name="id_customer" id="id_customer" value="{$id_customer}">
          <input type="hidden" name="reset_token" id="reset_token" value="{$reset_token}">

          <div class="row form-group">
            <div class="offset-md-5">
              <button class="btn btn-primary" type="submit" name="submit">
                {l s='Change Password' d='Shop.Theme.Actions'}
              </button>
            </div>
          </div>
        </div>

      </section>
    </form>
{/block}

{block name='page_footer'}
  <ul>
    <li><a href="{$urls.pages.authentication}">{l s='Back to Login' d='Shop.Theme.Actions'}</a></li>
  </ul>
{/block}
