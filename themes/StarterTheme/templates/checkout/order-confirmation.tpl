{extends file='page.tpl'}

{block name='page_title'}
  {l s='Order confirmation'}
{/block}

{block name='page_content_container' prepend}
  <section id="content-hook_payment_return">
    {$HOOK_PAYMENT_RETURN}
  </section>

  <section id="content-hook_order_confirmation">
    {$HOOK_ORDER_CONFIRMATION}
  </section>
{/block}

{block name='page_content_container'}
  <section id="content" class="page-content page-order-confirmation">
    {* StarterTheme: Order confirmation content *}
  </section>
{/block}

{block name='page_content_container' append}
  <section id="content-hook-order-confirmation-footer">
    {$HOOK_ORDER_CONFIRMATION_FOOTER} {* StarterTheme: Create hook *}
  </section>
{/block}


{* StarterTheme: OrderConfirmationController.php: remove duplicate code, and if possible eliminate multiple execution of it too *}
