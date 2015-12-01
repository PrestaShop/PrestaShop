{extends "page.tpl"}

{block name="page_title"}
  {l s='Add/edit your address'}
{/block}

{block name="page_content_container"}
  <section id="content" class="page-content page-address">
    <section class="address-form">
      <form action="{$urls.pages.address}" method="post">

        <section class="form-fields">

        {block name="address_form_container"}
          {include file="customer/_partials/address-form-fields.tpl"
          address_fields=$address_fields
          address=$address
          countries=$countries}
        {/block}

        </section>

      <footer class="form-footer">
        <input type="hidden" class="hidden" name="id_address" value="{$address.id}">
        <input type="hidden" class="hidden" name="back" value="{$back}">
        <input type="hidden" class="hidden" name="mod" value="{$mod}">
        <input type="hidden" name="token" value="{$token}">
        <input type="hidden" name="submitAddress" value="1">

        <button type="submit" id="submitAddress">
          {l s='Save'}
        </button>
      </footer>

      </form>
    </section>
  </section>
{/block}
