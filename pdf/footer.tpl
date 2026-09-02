{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
<table style="width: 100%;">
	<tr>
		<td style="text-align: center; font-size: 6pt; color: #444;  width:100%;">
			{if $available_in_your_account}
				{l s='An electronic version of this invoice is available in your account. To access it, log in to our website using your e-mail address and password (which you created when placing your first order).' d='Shop.Pdf' pdf='true'}
				<br />
			{/if}
			{$shop_address|escape:'html':'UTF-8'}<br />

			{if !empty($shop_phone) OR !empty($shop_fax)}
				{l s='For more assistance, contact Support:' d='Shop.Pdf' pdf='true'}<br />
        {if !empty($shop_phone)}
          {l s='Tel: %s' sprintf=[$shop_phone|escape:'html':'UTF-8'] d='Shop.Pdf' pdf='true'}
        {/if}

        {if !empty($shop_fax)}
          {l s='Fax: %s' sprintf=[$shop_fax|escape:'html':'UTF-8'] d='Shop.Pdf' pdf='true'}
        {/if}
				<br />
			{/if}

			{if isset($shop_details)}
				{$shop_details|escape:'html':'UTF-8'}<br />
			{/if}

			{if isset($free_text)}
				{$free_text|escape:'html':'UTF-8'}<br />
			{/if}
		</td>
	</tr>
</table>

