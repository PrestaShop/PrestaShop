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
