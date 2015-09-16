{extends "page.tpl"}

{block name="page_title"}
  {l s='Your personal information'}
{/block}

    {* StarterTheme: Add confirmation/error messages *}

{block name="page_content_container"}
  <section id="content" class="page-content page-identity">

    {block name="identity_form"}
    <section class="identity-form">
      <form action="{$urls.pages.identity}" method="post">

        {block name="identity_form_header"}
        <header>
          <p>{l s='Please be sure to update your personal information if it has changed.'}</p>
        </header>
        {/block}

        {block name="identity_form_fields"}
        <section id="identity-form-fields" class="form-fields">

          {block name="form_item_gender"}
            {include file="customer/_partials/form-item-gender.tpl" genders=$genders}
          {/block}

          <label class="required">
            <span>{l s='First name'}</span>
            <input type="text" id="firstname" name="firstname" value="{$smarty.post.firstname}" />
          </label>

          <label class="required">
            <span>{l s='Last name'}</span>
            <input type="text" name="lastname" id="lastname" value="{$smarty.post.lastname}" />
          </label>

          <label class="required">
            <span>{l s='E-mail address'}</span>
            <input type="email" name="email" id="email" value="{$smarty.post.email}" />
          </label>

          {block name="form_item_date"}
            {include file="customer/_partials/form-item-date.tpl" dates=$birthday_dates}
          {/block}

          <label class="required">
            <span>{l s='Current Password'}</span>
            <input type="password" name="old_passwd" id="old_passwd" />
          </label>

          <label for="passwd">
            <span>{l s='New Password'}</span>
            <input type="password" name="passwd" id="passwd" />
          </label>

          <label for="confirmation">
            <span>{l s='Confirmation'}</span>
            <input type="password" name="confirmation" id="confirmation" />
          </label>

          {if $feature_active.newsletter}
            <label class="{if array_key_exists('newsletter', $field_required)}required{/if}">
              <input type="checkbox" id="newsletter" name="newsletter" value="1" {if $smarty.post.newsletter == 1} checked="checked"{/if}/>
              <span>{l s='Sign up for our newsletter!'}</span>
            </label>
          {/if}

          {if $feature_active.optin}
            <label class="{if array_key_exists('optin', $field_required)}required{/if}">
              <input type="checkbox" name="optin" id="optin" value="1" {if $smarty.post.optin == 1} checked="checked"{/if}/>
              {l s='Receive special offers from our partners!'}
            </label>
          {/if}

          {if $feature_active.b2b}
            {block name="identity_form_b2b"}
            <label>
              <span>{l s='SIRET'}</span>
              <input type="text" id="siret" name="siret" value="{$smarty.post.siret}" />
            </label>

            <label>
              <span>{l s='APE'}</span>
              <input type="text" id="ape" name="ape" value="{$smarty.post.ape}" />
            </label>

            <label>
              <span>{l s='Website'}</span>
              <input type="text" id="website" name="website" value="{$smarty.post.website}" />
            </label>
            {/block}
          {/if}

          {block name="displayCustomerIdentityForm"}
            {hook h="displayCustomerIdentityForm"}
          {/block}

        </section>
        {/block}

        {block name="identity_form_footer"}
        <footer id="identity-form-footer"  class="form-footer">

          <button type="submit" name="submitIdentity"><span>{l s='Save'}</span></button>

        </footer>
        {/block}

      </form>
    </section>
    {/block}

    <footer class="form-footer">
      <a href="{$urls.pages.my_account}">{l s='Back to my account'}</a>
    </footer>

  </section>
{/block}
