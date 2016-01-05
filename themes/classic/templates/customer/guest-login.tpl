{extends "page.tpl"}

{block name="page_title"}
  {l s='Guest Tracking'}
{/block}

{block name="page_content"}
  {if isset($show_login_link) && $show_login_link}
    <p><a href="{$urls.pages.my_account}">{l s='Click here to log in to your customer account.'}</a></p>
  {/if}
  <form action="{$urls.pages.guest_tracking}" method="post">
    <header>
      <h1 class="h3">{l s='To track your order, please enter the following information:'}</h1>
    </header>

    <section class="form-fields">

      <label>
        <span>{l s='Order Reference:'}</span>
        <input type="text" name="order_reference" value="{if isset($smarty.request.id_order)}{$smarty.request.id_order}{/if}" size="8" />
        <i>{l s='For example: QIIXJXNUI or QIIXJXNUI#1'}</i>
      </label>

      <label>
        <span>{l s='Email:'}</span>
        <input type="email" name="email" value="{if isset($smarty.request.email)}{$smarty.request.email}{/if}" />
      </label>

    </section>

    <footer class="form-footer">
      <input type="hidden" name="submitGuestTracking" value="1">

      <button type="submit">{l s='Send'}</button>
    </footer>
  </form>
{/block}
