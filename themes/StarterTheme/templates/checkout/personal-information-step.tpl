{extends "checkout/checkout-step.tpl"}

{block "step_content"}
  {if $logged_in}
    <p>{l s='Connected as %1$s %2$s.' sprintf=[$customer.firstname, $customer.lastname]}</p>
    <p>{l s='Not you? [1]Log out[/1]' tags=["<a href='{$urls.actions.logout}'>"]}</p>
  {else if $show_login_form}
    <a href="{$urls.pages.order}">{l s='No account?'}</a>
    {form form=$login_form template='customer/_partials/login-form.tpl'}
  {else}
    <a href="?login">{l s='Already have an account?'}</a>
    {form form=$register_form template='customer/_partials/register-form.tpl'}
  {/if}

  <form method="POST">
    <button type="submit" class="continue" name="continue" value="1" {if !$step_is_complete}disabled{/if}>
      {l s='Continue'}
    </button>
  </form>
{/block}
