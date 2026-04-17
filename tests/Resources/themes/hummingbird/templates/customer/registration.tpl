{**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *}
{extends file='page.tpl'}

{block name='container_class'}container container--limited-md{/block}

{block name='page_title'}
  {l s='Create an account' d='Shop.Theme.Customeraccount'}
{/block}

{block name='page_content'}
  {block name='register_form_container'}
    {$hook_create_account_top nofilter}
    <section class="register-form">
      {render file='customer/_partials/customer-form.tpl' ui=$register_form mode='register'}
      <hr>
      <p class="register-form__login-prompt">{l s='Already have an account?' d='Shop.Theme.Customeraccount'} <a href="{$urls.pages.authentication}">{l s='Sign in' d='Shop.Theme.Customeraccount'}</a></p>
    </section>
  {/block}
{/block}
