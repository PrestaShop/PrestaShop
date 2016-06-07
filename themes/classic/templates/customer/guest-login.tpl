{extends file='page.tpl'}

{block name='page_title'}
  {l s='Guest Tracking' d='Shop.Theme.CustomerAccount'}
{/block}

{block name='page_content'}
  {if isset($show_login_link) && $show_login_link}
    <p><a href="{$urls.pages.my_account}">{l s='Click here to log in to your customer account.' d='Shop.Theme.CustomerAccount'}</a></p>
  {/if}
  <form action="{$urls.pages.guest_tracking}" method="post">
    <header>
      <h1 class="h3">{l s='To track your order, please enter the following information:' d='Shop.Theme.CustomerAccount'}</h1>
    </header>

    <section class="form-fields">

      <label>
        <span>{l s='Order Reference:' d='Shop.Theme.CustomerAccount'}</span>
        <input type="text" name="order_reference" value="{if isset($smarty.request.id_order)}{$smarty.request.id_order}{/if}" size="8">
        <em>{l s='For example: QIIXJXNUI or QIIXJXNUI#1' d='Shop.Theme.CustomerAccount'}</em>
      </label>

      <label>
        <span>{l s='Email:' d='Shop.Forms.Labels'}</span>
        <input type="email" name="email" value="{if isset($smarty.request.email)}{$smarty.request.email}{/if}">
      </label>

    </section>

    <footer class="form-footer">
      <input type="hidden" name="submitGuestTracking" value="1">

      <button type="submit">{l s='Send' d='Shop.Theme.Actions'}</button>
    </footer>
  </form>
{/block}
