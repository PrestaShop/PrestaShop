{extends file='customer/page.tpl'}

{block name='page_title'}
  {l s='Credit slips'}
{/block}

{block name='page_content_container'}
  <section id="content" class="page-content page-order">

    <h6>{l s='Credit slips you have received after canceled orders'}.</h6>
    {if $credit_slips}
      <table class="table table-striped table-bordered">
        <thead class="thead-default">
          <tr>
            <th>{l s='Credit slip'}</th>
            <th>{l s='Order'}</th>
            <th>{l s='Date issued'}</th>
            <th>{l s='View credit slip'}</th>
          </tr>
        </thead>
        <tbody>
          {foreach from=$credit_slips item=slip}
            <tr>
              <td>{$slip.credit_slip_number}</td>
              <td><a href="#">{$slip.order_number}</a></td>
              <td>{$slip.credit_slip_date}</td>
              <td class="text-xs-center">
                <a href="{$slip.url}"><i class="material-icons">&#xE415;</i></a>
              </td>
            </tr>
          {/foreach}
        </tbody>
      </table>
    {/if}

  </section>
{/block}
