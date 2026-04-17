{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
<table class="product" width="100%" cellpadding="4" cellspacing="0">
	<tr>
		<th class="header small left" valign="middle">{l s='If the following conditions are not met, we reserve the right to refuse your package and/or refund:' d='Shop.Pdf' pdf='true'}</th>
	</tr>
	<tr>
		<td class="center small white">
			<ul class="left">
				<li>{l s='Please include this return reference on your return package:' d='Shop.Pdf' pdf='true'} {$order_return->id}</li>
				<li>{l s='All products must be returned in their original package and condition, unused and without damage.' d='Shop.Pdf' pdf='true'}</li>
				<li>{l s='Please print out this document and slip it into your package.' d='Shop.Pdf' pdf='true'}</li>
				<li>{l s='The package should be sent to the following address:' d='Shop.Pdf' pdf='true'}</li>
			</ul>
			<span style="margin-left: 20px;">{$shop_address}</span>
		</td>
	</tr>
</table>
<br/>
{l s='Upon receiving your package, we will notify you by e-mail. We will then begin processing the refund, if applicable. Let us know if you have any questions' d='Shop.Pdf' pdf='true'}
