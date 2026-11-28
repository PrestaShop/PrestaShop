{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}

{$style_tab}


<table width="100%" id="body" border="0" cellpadding="0" cellspacing="0" style="margin:0;">
	<!-- Addresses -->
	<tr>
		<td colspan="12">

		{$addresses_tab}

		</td>
	</tr>

	<tr>
		<td colspan="12" height="30">&nbsp;</td>
	</tr>

	<tr>
		<td colspan="12">

		{$summary_tab}

		</td>
	</tr>

	<tr>
		<td colspan="12" height="20">&nbsp;</td>
	</tr>

</table>

{* The product table is rendered outside this layout table on purpose. TCPDF only repeats a
   <thead> across a page break for a table that is not nested, so a document longer than one
   page would lose its column headers. *}
{$product_tab}

<table width="100%" id="body-end" border="0" cellpadding="0" cellspacing="0" style="margin:0;">

	<tr>
		<td colspan="12" height="20">&nbsp;</td>
	</tr>

	{if isset($payment_tab)}
		<tr>
			<td colspan="7" class="left">

				{$payment_tab}

			</td>
			<td colspan="5">&nbsp;</td>
		</tr>
	{/if}

	<!-- Hook -->
	{if isset($HOOK_DISPLAY_PDF)}
	<tr>
		<td colspan="12" height="30">&nbsp;</td>
	</tr>

	<tr>
		<td colspan="12">
			{$HOOK_DISPLAY_PDF}
		</td>
	</tr>
	{/if}

</table>
