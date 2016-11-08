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
{extends file='page.tpl'}

{block name='page_title'}
  {l s='Reset your password' d='Shop.Theme.CustomerAccount'}
{/block}

{block name='page_content'}
    <form action="{$urls.pages.password}" method="post">

      <section class="form-fields">

        <label>
          <span>{l s='Email address: %email%' d='Shop.Theme.CustomerAccount' sprintf=['%email%' => $customer_email|stripslashes]}</span>
        </label>

        <label>
          <span>{l s='New password' d='Shop.Forms.Labels'}</span>
          <input type="password" data-validate="isPasswd" name="passwd" value="">
        </label>

        <label>
          <span>{l s='Confirmation' d='Shop.Forms.Labels'}</span>
          <input type="password" data-validate="isPasswd" name="confirmation" value="">
        </label>

      </section>

      <footer class="form-footer">
        <input type="hidden" name="token" id="token" value="{$customer_token}">
        <input type="hidden" name="id_customer" id="id_customer" value="{$id_customer}">
        <input type="hidden" name="reset_token" id="reset_token" value="{$reset_token}">
        <button type="submit" name="submit">
          {l s='Change Password' d='Shop.Theme.Actions'}
        </button>
      </footer>

    </form>
{/block}

{block name='page_footer'}
  <ul>
    <li><a href="{$urls.pages.authentication}">{l s='Back to Login' d='Shop.Theme.Actions'}</a></li>
  </ul>
{/block}
