{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
{l s='We have logged your return request.' d='Shop.Pdf' pdf='true'}<br />
{l s='Your package must be returned to us within' d='Shop.Pdf' pdf='true'} {$return_nb_days} {l s='days of receiving your order.' d='Shop.Pdf' pdf='true'}<br /><br />

<table id="summary-tab" width="100%">
	<tr>
		<th class="header small" valign="middle">{l s='Return Number' d='Shop.Pdf' pdf='true'}</th>
		<th class="header small" valign="middle">{l s='Date' d='Shop.Pdf' pdf='true'}</th>
	</tr>
	<tr>
		<td class="center small white">{sprintf('%06d', $order_return->id)}</td>
		<td class="center small white">{dateFormat date=$order_return->date_add full=0}</td>
	</tr>
</table>
