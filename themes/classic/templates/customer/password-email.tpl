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
