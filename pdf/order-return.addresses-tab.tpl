{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
<table id="addresses-tab" cellspacing="0" cellpadding="0">
	<tr>
		{if !empty($invoice_address)}
			<td width="50%">{if $delivery_address}<span class="bold">{l s='Delivery address' d='Shop.Pdf' pdf='true'}</span><br/><br/>
					{$delivery_address}
				{/if}
			</td>
			<td width="50%"><span class="bold">{l s='Billing address' d='Shop.Pdf' pdf='true'}</span><br/><br/>
				{$invoice_address}
			</td>
		{else}
			<td width="100%">{if $delivery_address}<span class="bold">{l s='Billing & Delivery Address' d='Shop.Pdf' pdf='true'}</span><br/><br/>
					{$delivery_address}
				{/if}
			</td>
		{/if}
	</tr>
</table>
