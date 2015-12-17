{foreach from=$errors[null] item=error}
  <p>{$error}</p>
{/foreach}

<form action="{$action}" method="post">

  <section class="form-fields">

    <label class="required">
      <span>{l s='First name'}</span>
      <input required type="text" name="firstname" value="{$firstname}" />
    </label>
    {include file="_partials/form-field-errors.tpl" errors=$errors.firstname}

    <label class="required">
      <span>{l s='Last name'}</span>
      <input required type="text" name="lastname" value="{$lastname}" />
    </label>
    {include file="_partials/form-field-errors.tpl" errors=$errors.lastname}

    <label>
      <span>{l s='Email address'}</span>
      <input required type="email" name="email" value="{$email}">
    </label>
    {include file="_partials/form-field-errors.tpl" errors=$errors.email}

    {if $guest_allowed}
      <p>{l s='Set a password to create an account and save time on the next order (optional)'}</p>
      <label>
        <span>{l s='Password'}</span>
        <input type="password" name="password" value="">
      </label>
      {include file="_partials/form-field-errors.tpl" errors=$errors.password}
    {else}
      <label>
        <span>{l s='Password'}</span>
        <input required type="password" name="password" value="">
      </label>
      {include file="_partials/form-field-errors.tpl" errors=$errors.password}
    {/if}



    {if $ask_for_birthdate}
      <label>
        <span>{l s='Birthdate'}</span>
        <input type="date" name="birthdate" value="{$birthdate}">
      </label>
      {include file="_partials/form-field-errors.tpl" errors=$errors.birthdate}
    {/if}

    {if $ask_for_newsletter}
      <label>
        <input type="checkbox" name="newsletter" value="1" {if $newsletter} checked {/if}>
        <span>{l s='Sign up for our newsletter!'}</span>
      </label>
    {/if}

    {if $ask_for_partner_optin}
      <label>
        <input type="checkbox" name="partner_optin" value="1" {if $partner_optin} checked {/if}>
        <span>{l s='Receive offers from our partners'}</span>
      </label>
    {/if}

  </section>

  <footer class="form-footer">
    {if $back}
      <input type="hidden" name="back" value="{$back}">
    {/if}
    <button type="submit" name="submitCreate" value="1">
      {l s='Save'}
    </button>
  </footer>

</form>
