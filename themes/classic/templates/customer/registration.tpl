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
  {l s='Create an account' d='Shop.Theme.CustomerAccount'}
{/block}

{block name='page_content'}
    {block name='register_form_container'}
      {$hook_create_account_top nofilter}
      <section class="register-form">
        <p>{l s='Already have an account?' d='Shop.Theme.CustomerAccount'} <a href="{$urls.pages.authentication}">{l s='Log in instead!' d='Shop.Theme.CustomerAccount'}</a></p>
        {render file='customer/_partials/customer-form.tpl' ui=$register_form}
      </section>
    {/block}
{/block}
