{if $order.messages}
<div class="box">
  <h3>{l s='Messages'}</h3>
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
</div>
{/if}

<section class="order-message-form box">
  <form action="{$urls.pages.order_detail}" method="post">

    <header>
      <h3>{l s='Add a message'}</h3>
      <p>{l s='If you would like to add a comment about your order, please write it in the field below.'}</p>
    </header>

    <section class="form-fields">

      <div class="form-group row">
        <label class="col-md-3 form-control-label">{l s='Product'}</label>
        <div class="col-md-5">
          <select name="id_product" class="form-control form-control-select">
            <option value="0">{l s='-- Choose --'}</option>
            {foreach from=$order.products item=product}
              <option value="{$product.id_product}">{$product.name}</option>
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

    <footer class="form-footer text-xs-center">
      <input type="hidden" name="id_order" value="{$order.details.id}">
      <button type="submit" name="submitMessage" class="btn btn-primary form-control-submit">
        {l s='Send'}
      </button>
    </footer>

  </form>
</section>
