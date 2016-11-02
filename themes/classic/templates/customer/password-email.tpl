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
  {l s='Forgot your password?' d='Shop.Theme.CustomerAccount'}
{/block}

{block name='page_content'}
  <form action="{$urls.pages.password}" method="post">

    <header>
      <p>{l s='Please enter the email address you used to register. You will receive a temporary link to reset your password.' d='Shop.Theme.CustomerAccount'}</p>
    </header>

    <section class="form-fields">
      <div class="form-group row">
        <label class="col-md-3 form-control-label required">{l s='Email address' d='Shop.Forms.Labels'}</label>
        <div class="col-md-5">
          <input type="email" name="email" id="email" value="{if isset($smarty.post.email)}{$smarty.post.email|stripslashes}{/if}" class="form-control" required>
        </div>
      </div>
    </section>

    <footer class="form-footer text-xs-center">
      <button class="form-control-submit btn btn-primary" name="submit" type="submit">
        {l s='Send reset link' d='Shop.Theme.Actions'}
      </button>
    </footer>

  </form>
{/block}

{block name='page_footer'}
  <a href="{$urls.pages.my_account}" class="account-link">
    <i class="material-icons">&#xE5CB;</i>
    <span>{l s='Back to login' d='Shop.Theme.Actions'}</span>
  </a>
{/block}
