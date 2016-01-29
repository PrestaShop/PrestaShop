{extends file='customer/page.tpl'}

{block name='page_title'}
  {l s='Return Merchandise Authorization (RMA)'}
{/block}

{block name='page_content'}
  <h6>{l s='Here is a list of pending merchandise returns'}</h6>

  {if isset($errorMsg) && $errorMsg}
    <form method="post">

      <section class="form-fields">
        <div class="form-group row"> 
          <label class="col-md-3 form-control-label">{l s='Please provide an explanation for your RMA:'}</label>
          <div class="col-md-9">
            <textarea cols="67" rows="3" name="returnText" class="form-control"></textarea>
          </div>
        </div>
      </section>

      <footer class="form-footer">
        {foreach $ids_order_detail as $id_order_detail}
          <input type="hidden" name="ids_order_detail[{$id_order_detail}]" value="{$id_order_detail}"/>
        {/foreach}

        {foreach $order_qte_input as $key => $value}
          <input type="hidden" name="order_qte_input[{$key}]" value="{$value}"/>
        {/foreach}
        <input type="hidden" name="id_order" value="{$id_order}"/>

        <button type="submit" class="btn btn-primary">{l s='Make an RMA slip'}</button>
      </footer>

    </form>
  {/if}

  {if $ordersReturn && count($ordersReturn)}
    <table class="table table-striped table-bordered">
      <thead class="thead-default">
        <tr>
          <th>{l s='Return'}</th>
          <th>{l s='Order'}</th>
          <th>{l s='Package status'}</th>
          <th>{l s='Date issued'}</th>
          <th>{l s='Return slip'}</th>
        </tr>
      </thead>
      <tbody>
        {foreach from=$ordersReturn item=return}
          <tr>
            <td><a href="{$return.return_url}">{$return.return_number}</a></td>
            <td><a href="{$return.details_url}">{$return.reference}</a></td>
            <td>{$return.state_name}</td>
            <td>{$return.return_date}</td>
            <td>
              {if $return.print_url}
                <a href="{$return.print_url}">{l s='Print out'}</a>
              {else}
                --
              {/if}
            </td>
          </tr>
        {/foreach}
      </tbody>
    </table>
  {/if}

{/block}
