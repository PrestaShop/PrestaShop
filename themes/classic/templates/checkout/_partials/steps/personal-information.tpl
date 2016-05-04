{extends file='checkout/_partials/steps/checkout-step.tpl'}

{block name='step_content'}
  {if $customer.is_logged}
    <p class="identity">{l s='Connected as %1$s %2$s.' sprintf=[$customer.firstname, $customer.lastname]}</p>
    <p>{l s='Not you? [1]Log out[/1]' tags=["<a href='{$urls.actions.logout}'>"]}</p>
  {elseif $show_login_form}
    <a href="{$urls.pages.order}">{l s='Don\'t have an account?'}</a>
    {render file='checkout/_partials/login-form.tpl' ui=$login_form}
  {else}
    <p class="sign-in">{l s='Have an account?'}<a data-link-action="show-login-form" href="{$urls.pages.order_login}"> {l s='Sign in'}</a></p>
    {render file='checkout/_partials/customer-form.tpl' ui=$register_form guest_allowed=$guest_allowed}
  {/if}
{/block}
