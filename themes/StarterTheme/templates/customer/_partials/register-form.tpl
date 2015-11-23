{extends "page.tpl"}

{block name="page_header_container"}{/block}

{block name="page_content_container"}
  <section id="content" class="page-content page-authentication">
    {block name="register_form_container"}
      <section class="register-form">
        {block name="register_form"}
          <form action="{$urls.pages.authentication}" method="post">
            <header>
              <h1 class="h3">{l s='Create an account'}</h1>
            </header>

            {$hook_create_account_top}

            <section class="form-fields">

              {include file="customer/_partials/form-item-gender.tpl" genders=$genders}

              <label class="required">
                <span>{l s='First name'}</span>
                <input type="text" id="firstname" name="firstname" value="{if isset($smarty.post.firstname)}{$smarty.post.firstname}{/if}" />
              </label>

              <label class="required">
                <span>{l s='Last name'}</span>
                <input type="text" name="lastname" id="lastname" value="{if isset($smarty.post.lastname)}{$smarty.post.lastname}{/if}" />
              </label>

              <label class="required">
                <span>{l s='E-mail address'}</span>
                <input type="email" name="email" id="email" value="{if isset($smarty.post.email)}{$smarty.post.email}{/if}" />
              </label>

              <label for="passwd">
                <span>{l s='Password'}</span>
                <input type="password" name="passwd" id="passwd" />
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

            </section>

            {$hook_create_account_form}

            <footer class="form-footer">
              <input type="hidden" name="create_account" value="1">
              <button type="submit" name="submitCreate">{l s='Register'}</button>
            </footer>

          </form>
        {/block}
      </section>
    {/block}
  </section>
{/block}
