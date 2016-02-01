{if $order.messages}
  <h4>{l s='Messages'}</h4>
  <table class="table table-striped table-bordered">
    <thead class="thead-default">
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
      <h4>{l s='Add a message'}</h4>
      <p>{l s='If you would like to add a comment about your order, please write it in the field below.'}</p>
    </header>

    <section class="form-fields">

      <div class="form-group row"> 
        <label class="col-md-3 form-control-label">{l s='Product'}</label>
        <div class="col-md-9">
          <select name="id_product" class="form-control">
            <option value="0">{l s='-- Choose --'}</option>
            {foreach from=$order.products item=product}
              <option value="{$product.product_id}">{$product.product_name}</option>
            {/foreach}
          </select>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-md-3 form-control-label"></label>
        <div class="col-md-9">
          <textarea rows="3" name="msgText" class="form-control"></textarea>
        </div>
      </div>

    </section>

    <footer class="form-footer">
      <input type="hidden" name="id_order" value="{$order.data.id}" />
      <button type="submit" name="submitMessage" class="btn btn-primary">
        {l s='Send'}
      </button>
    </footer>

  </form>
</section>
