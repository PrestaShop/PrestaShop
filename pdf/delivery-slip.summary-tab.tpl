{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
<table id="summary-tab" width="100%">
	<tr>
		<th class="header small" valign="middle">{l s='Order Reference' d='Shop.Pdf' pdf='true'}</th>
		<th class="header small" valign="middle">{l s='Order date' d='Shop.Pdf' pdf='true'}</th>
		{if isset($carrier)}
			<th class="header small" valign="middle">{l s='Carrier' d='Shop.Pdf' pdf='true'}</th>
		{/if}
	</tr>
	<tr>
		<td class="center small white">{$order->getUniqReference()}</td>
		<td class="center small white">{dateFormat date=$order->date_add full=0}</td>
		{if isset($carrier)}
			<td class="center small white">{$carrier->name}</td>
		{/if}
	</tr>
</table>

