{extends "page.tpl"}

{block name="page_title"}
  {l s='Order details'}
{/block}

{block name="page_content"}

  {block name="order_return_infos"}
    <div id="order-return-infos">
      <h1>{l s='RE#%s on %s' sprintf=[$orderRet.return_number, $orderRet.return_date]}</h2>
      <p>{l s='We have logged your return request.'}</p>
      <p>{l s='Your package must be returned to us within %s days of receiving your order.' sprintf=$nbdaysreturn}</p>
      <p>{l s='The current status of your merchandise return is: %s' sprintf=$state_name}</p>
      <p>{l s='List of items to be returned:'}</p>
    </div>
  {/block}

  <table>
    <thead>
      <tr>
        <th>{l s='Reference'}</th>
        <th>{l s='Product'}</th>
        <th>{l s='Quantity'}</th>
      </tr>
    </thead>
    <tbody>
    {foreach from=$products item=product}
      <tr>
        <td>{$product.product_reference}</td>
        <td>{$product.product_name}</td>
        <td>{$product.product_quantity}</td>
      </tr>
      {if $product.customizations}
        {foreach $product.customizations  as $customization}
          <tr>
            <td colspan="2">
              <ul>
                {foreach from=$customization.fields item=field}
                  {if $field.type == 'image'}
                    <li><img src="{$field.image.small.url}" alt="" /></li>
                  {elseif $field.type == 'text'}
                    <li>{$field.label} : {$field.text}</li>
                  {/if}
                {/foreach}
              </ul>
            </td>
            <td>{$customization.quantity}</td>
          </tr>
        {/foreach}
      {/if}
    {/foreach}
    </tbody>
  </table>

  {if $orderRet.state == 2}
    <section>
      <h3>{l s='Reminder'}</h3>
      <ul>
        <li>{l s='All merchandise must be returned in its original packaging and in its original state.'}</li>
        <li>{l s='Please print out the [1]PDF return slip[/1] and include it with your package.' tags=['<a href="'|cat:$orderRet.return_pdf_url|cat:'">']}</li>
        <li>{l s='Please see the PDF return slip ([1]for the correct address[/1]).' tags=['<a href="'|cat:$orderRet.return_pdf_url|cat:'">']}</li>
      </ul>
      {l s='When we receive your package, we will notify you by email. We will then begin processing order reimbursement.'}
      <br /><br /><a href="{$urls.pages.contact}">{l s='Please let us know if you have any questions.'}</a>
      <p>{l s='If the conditions of return listed above are not respected, we reserve the right to refuse your package and/or reimbursement.'}</p>
    </section>
  {/if}

  <ul>
    <li><a href="{$urls.pages.my_account}">{l s='Back to your account'}</a></li>
    <li><a href="{$urls.pages.index}">{l s='Home'}</a></li>
  </ul>
{/block}
