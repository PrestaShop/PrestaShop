{extends "checkout/checkout-step.tpl"}

{block "step_content"}
  {if $logged_in}
    <p>{l s='Connected as %1$s %2$s.' sprintf=[$customer.firstname, $customer.lastname]}</p>
    <p>{l s='Not you? [1]Log out[/1]' tags=["<a href='{$urls.actions.logout}'>"]}</p>
  {else if $show_login_form}
    <a href="{$urls.pages.order}">{l s='No account?'}</a>
    {$rendered_login_form nofilter}
  {else}
    <a href="?login">{l s='Already have an account?'}</a>
    {$rendered_register_form nofilter}
  {/if}
{/block}
