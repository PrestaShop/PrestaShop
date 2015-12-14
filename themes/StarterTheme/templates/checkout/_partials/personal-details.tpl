<section class="customer-info-form">
  <header>
    <h1 class="h2">{l s='Order as guest'}</h1>
    {if !$customer.is_logged}
      <p>{l s='Have an account?'} <a data-link-action="show-login-form" href="{$urls.pages.order_login}">{l s='Log in'}</a></p>
    {else}
      <p>{l s='Not you?'} <a data-link-action="logout" href="{$urls.actions.logout}">{l s='Log out'}</a></p>
    {/if}
  </header>
  <form action="{$urls.pages.authentication}" method="post">
    <section class="form-fields">

      {include file="customer/_partials/form-item-gender.tpl" genders=$genders}

      <label class="required">
        <span>{l s='First name'}</span>
        <input type="text" name="firstname" value="{$customer.firstname}" />
      </label>

      <label class="required">
        <span>{l s='Last name'}</span>
        <input type="text" name="lastname" value="{$customer.lastname}" />
      </label>

      {if !$customer.is_logged}
        <label class="required">
          <span>{l s='Email address'}</span>
          <input type="email" name="email" value="" />
        </label>

        {if $guest_allowed}
          <label>
            <span>{l s='Create an account and save time on the next order (facultative)'}</span>
          </label>
        {/if}

        <label {if !$guest_allowed}class="required"{/if}>
          <span>{l s='Password'}</span>
          <input type="password" name="passwd" value="" />
          <p>{l s='Five characters minimum'}</p>
        </label>

        <label for="birthdate">
          <span>{l s='Date of Birth'}</span>
          {block name="form_item_date"}
            {include file="customer/_partials/form-item-date.tpl" dates=$birthday_dates}
          {/block}
        </label>

        {if $feature_active.newsletter}
          <label class="{if array_key_exists('newsletter', $field_required)}required{/if}">
            <input type="checkbox" name="newsletter" id="newsletter" value="1" {if isset($smarty.post.newsletter) && $smarty.post.newsletter == 1} checked="checked"{/if}/>
            <span>{l s='Sign up for our newsletter!'}</span>
          </label>
        {/if}

        {if $feature_active.optin}
          <label class="{if array_key_exists('optin', $field_required)}required{/if}">
            <input type="checkbox" name="optin" id="optin" value="1" {if isset($smarty.post.optin) && $smarty.post.optin == 1} checked="checked"{/if}/>
            <span>{l s='Receive special offers from our partners!'}</span>
          </label>
        {/if}
      {/if}

    </section>

    <footer class="form-footer">
      <input type="hidden" name="create_account" value="1">
      <input type="hidden" name="create_from" value="order">
      <input type="hidden" name="back" value="{$urls.pages.order}">
      <button type="submit" name="submitCreate">{l s='Continue'}</button>
    </footer>

  </form>
</section>
