{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}

<!--  TAX DETAILS -->
{if $isTaxEnabled}
  {if $tax_exempt}

    {l s='Exempt of VAT according to section 259B of the General Tax Code.' d='Shop.Pdf' pdf='true'}

  {elseif (isset($tax_breakdowns) && $tax_breakdowns)}
    <table id="tax-tab" width="100%">
      <thead>
        <tr>
          <th class="header small">{l s='Tax Detail' d='Shop.Pdf' pdf='true'}</th>
          <th class="header small">{l s='Tax Rate' d='Shop.Pdf' pdf='true'}</th>
          {if $display_tax_bases_in_breakdowns}
            <th class="header small">{l s='Base price' d='Shop.Pdf' pdf='true'}</th>
          {/if}
          <th class="header-right small">{l s='Total Tax' d='Shop.Pdf' pdf='true'}</th>
        </tr>
      </thead>
      <tbody>
      {assign var=has_line value=false}

      {foreach $tax_breakdowns as $label => $bd}
        {assign var=label_printed value=false}

        {foreach $bd as $line}
          {* We force the display of the ecotax even if the ecotax rate is equals to 0 *}
          {if $line.rate == 0 and $label != 'ecotax_tax'}
            {continue}
          {/if}

          {assign var=has_line value=true}

          <tr>
            <td class="white">
              {if !$label_printed}
                {if $label == 'product_tax'}
                  {l s='Products' d='Shop.Pdf' pdf='true'}
                {elseif $label == 'shipping_tax'}
                  {l s='Shipping' d='Shop.Pdf' pdf='true'}
                {elseif $label == 'ecotax_tax'}
                  {l s='Ecotax' d='Shop.Pdf' pdf='true'}
                {elseif $label == 'wrapping_tax'}
                  {l s='Wrapping' d='Shop.Pdf' pdf='true'}
                {/if}
                {assign var=label_printed value=true}
              {/if}
            </td>

            <td class="center white">
              {$line.rate} %
            </td>

            {if $display_tax_bases_in_breakdowns}
              <td class="right white">
                {if isset($is_order_slip) && $is_order_slip}- {/if}
                {displayPrice currency=$order->id_currency price=$line.total_tax_excl}
              </td>
            {/if}

            <td class="right white">
              {if isset($is_order_slip) && $is_order_slip}- {/if}
              {displayPrice currency=$order->id_currency price=$line.total_amount}
            </td>
          </tr>
        {/foreach}
      {/foreach}

      {if !$has_line}
        <tr>
          <td class="white center" colspan="{if $display_tax_bases_in_breakdowns}4{else}3{/if}">
            {l s='No taxes' d='Shop.Pdf' pdf='true'}
          </td>
        </tr>
      {/if}

      </tbody>
    </table>
  {/if}
{/if}
<!--  / TAX DETAILS -->
