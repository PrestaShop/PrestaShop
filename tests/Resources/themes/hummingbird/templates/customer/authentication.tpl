{**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *}
{$componentName = 'login'}

{extends file='page.tpl'}

{block name='container_class'}container container--limited-sm{/block}

{block name='page_title'}
  {l s='Sign in' d='Shop.Theme.Customeraccount'}
{/block}

{block name='page_content'}
  {block name='login_form_container'}
    <div class="{$componentName}">
      <section class="{$componentName}__form-wrapper">
        {render file='customer/_partials/login-form.tpl' ui=$login_form}
      </section>
      
      <hr/>

      {block name='display_after_login_form'}
        {hook h='displayCustomerLoginFormAfter'}
      {/block}

      <div class="{$componentName}__register-prompt">
        <h2 class="h4 mb-3">{l s='No account?' d='Shop.Theme.Customeraccount'}</h2>

        <div class="d-grid">
          <a href="{$urls.pages.register}" class="btn btn-outline-primary" data-link-action="display-register-form">
            {l s='Create your account' d='Shop.Theme.Actions'}
          </a>
        </div>
      </div>
    </div>
  {/block}
{/block}
