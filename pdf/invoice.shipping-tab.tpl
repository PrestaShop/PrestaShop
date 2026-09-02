{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
{if !$order->isVirtual()}
<table id="shipping-tab" width="100%">
	<tr>
		<td class="shipping center small grey bold" width="44%">{l s='Carrier' d='Shop.Pdf' pdf='true'}</td>
		<td class="shipping center small white" width="56%">{$carrier->name}</td>
	</tr>
</table>
{/if}
