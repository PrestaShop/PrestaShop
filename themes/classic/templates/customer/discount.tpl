{extends file='customer/page.tpl'}

{block name='page_title'}
  {l s='My vouchers'}
{/block}

{block name='page_content_container'}
  <section id="content" class="page-content page-order">
  
    {if $cart_rules}
      <table class="table table-striped table-bordered">
        <thead class="thead-default">
          <tr>
            <th>{l s='Code'}</th>
            <th>{l s='Description'}</th>
            <th>{l s='Quantity'}</th>
            <th>{l s='Value'}</th>
            <th>{l s='Minimum'}</th>
            <th>{l s='Cumulative'}</th>
            <th>{l s='Expiration date'}</th>
          </tr>
        </thead>
        <tbody>
          {foreach from=$cart_rules item=cart_rule}
            <tr>
              <td>{$cart_rule.code}</td>
              <td>{$cart_rule.name}</td>
              <td>{$cart_rule.quantity_for_user}</td>
              <td>{$cart_rule.value}</td>
              <td>{$cart_rule.voucher_minimal}</td>
              <td>{$cart_rule.voucher_cumulable}</td>
              <td>{$cart_rule.voucher_date}</td>
            </tr>
          {/foreach}
        </tbody>
      </table>
    {/if}

  </section>
{/block}
