<section id="personal-information-section" data-checkout-step-status="{$status}">
  <header>
    <h1 class="h1">{l s='Personal Information'}</h1>
  </header>

  {if $show_login_form}
    {block name="checkout_login_form"}
      <section class="login-form">
        <header>
          <h1 class="h2">{l s='Log in to your account'}</h1>
          <span><a href="{$guest_or_register_url}" data-link-action="show-register-form">{l s='No account?'}</a></span>
        </header>
        {include file="customer/_partials/login-form.tpl" back=$urls.pages.order}
      </section>
    {/block}
  {else}
    {block name="checkout_basic_information"}
      {include file="checkout/_partials/personal-details.tpl" customer=$customer}
    {/block}
  {/if}
</section>
