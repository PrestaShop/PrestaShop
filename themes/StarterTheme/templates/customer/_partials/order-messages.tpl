{if $order.messages}
  <h3>{l s='Messages'}</h3>
  <table>
    <thead>
      <tr>
        <th>{l s='From'}</th>
        <th>{l s='Message'}</th>
      </tr>
    </thead>
    <tbody>
    {foreach from=$order.messages item=message}
      <tr>
        <td>
          {$message.name}<br>
          {$message.message_date}
        </td>
        <td>{$message.message nofilter}</td>
      </tr>
    {/foreach}
    </tbody>
  </table>
{/if}

<section class="order-message-form">
  <form action="{$urls.pages.order_detail}" method="post">

    <header>
      <h1 class="h3">{l s='Add a message'}</h1>
      <p>{l s='If you would like to add a comment about your order, please write it in the field below.'}</p>
    </header>

    <section class="form-fields">

      <label>
        <span>{l s='Product'}</span>
        <select name="id_product">
          <option value="0">{l s='-- Choose --'}</option>
          {foreach from=$order.products item=product}
            <option value="{$product.product_id}">{$product.product_name}</option>
          {/foreach}
        </select>
      </label>

      <label>
        <textarea cols="67" rows="3" name="msgText"></textarea>
      </label>

    </section>

    <footer class="form-footer">
      <input type="hidden" name="id_order" value="{$order.data.id}" />
      <button type="submit" name="submitMessage">
        {l s='Send'}
      </button>
    </footer>

  </form>
</section>
