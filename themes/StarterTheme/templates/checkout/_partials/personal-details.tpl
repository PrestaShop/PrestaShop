<section class="customer-info-form">
  <form action="{$urls.pages.order}" method="post">
    <header>
      <h1 class="h3">{l s='Your personal details'}</h1>
    </header>

    {if !$customer.is_logged}
      <p><a data-link-action="show">{l s='Not you? Logout'}</a></p>
    {/if}

    <section class="form-fields">

      {* StarterTheme : Add Social titles with form-item-gender.tpl *}

      <label>
        <span>{l s='First name'}</span>
        <input type="text" name="firstname" value="{$customer.firstname}" />
      </label>

      <label>
        <span>{l s='Last name'}</span>
        <input type="text" name="lastname" value="{$customer.lastname}" />
      </label>

      {if !$customer.is_logged}
        <label>
          <span>{l s='Email address'}</span>
          <input type="email" name="email" value="" />
        </label>

        <label>
          <span>{l s='Password'}</span>
          <input type="email" name="email" value="" />
          <p>{l s='Optional. Create an account at the same time.'}</p>
        </label>
      {/if}

    </section>

    <footer class="form-footer">
      <input type="hidden" name="submitPersonalDetails" value="1">

      {if $customer.is_logged}
        <p><a href="{$urls.actions.logout}">{l s='Not you? Logout'}</a></p>
      {/if}

      <button type="submit">{l s='Continue'}</button>
    </footer>

  </form>
</section>
