{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
<table id="total-tab" width="100%">

  <tr>
    <td class="grey" width="50%">
      {l s='Total Products' d='Shop.Pdf' pdf='true'}
    </td>
    <td class="white" width="50%">
      {displayPrice currency=$order->id_currency price=$footer.products_before_discounts_tax_excl}
    </td>
  </tr>

  {if $footer.product_discounts_tax_excl > 0}

    <tr>
      <td class="grey" width="50%">
        {l s='Total Discounts' d='Shop.Pdf' pdf='true'}
      </td>
      <td class="white" width="50%">
        - {displayPrice currency=$order->id_currency price=$footer.product_discounts_tax_excl}
      </td>
    </tr>

  {/if}
  {if !$order->isVirtual()}
  <tr>
    <td class="grey" width="50%">
      {l s='Shipping Costs' d='Shop.Pdf' pdf='true'}
    </td>
    <td class="white" width="50%">
      {if $footer.shipping_tax_excl > 0}
        {displayPrice currency=$order->id_currency price=$footer.shipping_tax_excl}
      {else}
        {l s='Free Shipping' d='Shop.Pdf' pdf='true'}
      {/if}
    </td>
  </tr>
  {/if}

  {if $footer.wrapping_tax_excl > 0}
    <tr>
      <td class="grey">
        {l s='Wrapping Costs' d='Shop.Pdf' pdf='true'}
      </td>
      <td class="white">{displayPrice currency=$order->id_currency price=$footer.wrapping_tax_excl}</td>
    </tr>
  {/if}

  <tr class="bold">
    <td class="grey">
      {if $isTaxEnabled}
        {l s='Total (Tax excl.)' d='Shop.Pdf' pdf='true'}
      {else}
        {l s='Total' d='Shop.Pdf' pdf='true'}
      {/if}
    </td>
    <td class="white">
      {displayPrice currency=$order->id_currency price=$footer.total_paid_tax_excl}
    </td>
  </tr>
  {if $isTaxEnabled}
    {if $footer.total_taxes > 0}
      <tr class="bold">
        <td class="grey">
          {l s='Total Tax' d='Shop.Pdf' pdf='true'}
        </td>
        <td class="white">
          {displayPrice currency=$order->id_currency price=$footer.total_taxes}
        </td>
      </tr>
    {/if}
    <tr class="bold big">
      <td class="grey">
        {l s='Total' d='Shop.Pdf' pdf='true'}
      </td>
      <td class="white">
        {displayPrice currency=$order->id_currency price=$footer.total_paid_tax_incl}
      </td>
    </tr>
  {/if}
</table>
