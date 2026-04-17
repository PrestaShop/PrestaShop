{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
{foreach $list as $product}
<tr>
	<td style="border:1px solid #D6D4D4;">
		<table class="table">
			<tr>
				<td width="5">&nbsp;</td>
				<td>
					<font size="2" face="Open-sans, sans-serif" color="#555454">
						{$product['reference']}
					</font>
				</td>
				<td width="5">&nbsp;</td>
			</tr>
		</table>
	</td>
	<td style="border:1px solid #D6D4D4;">
		<table class="table">
			<tr>
				<td width="5">&nbsp;</td>
				<td>
					<font size="2" face="Open-sans, sans-serif" color="#555454">
						<strong>{$product['name']}</strong>
						{if count($product['customization']) == 1}
							<br>
							{foreach $product['customization'] as $customization}
								{$customization['customization_text'] nofilter}
							{/foreach}
						{/if}

						{hook h='displayProductPriceBlock' product=$product type="unit_price"}
					</font>
				</td>
				<td width="5">&nbsp;</td>
			</tr>
		</table>
	</td>
	<td style="border:1px solid #D6D4D4;">
		<table class="table">
			<tr>
				<td width="5">&nbsp;</td>
				<td align="right">
					<font size="2" face="Open-sans, sans-serif" color="#555454">
						{$product['unit_price']}
					</font>
				</td>
				<td width="5">&nbsp;</td>
			</tr>
		</table>
	</td>
	<td style="border:1px solid #D6D4D4;">
		<table class="table">
			<tr>
				<td width="5">&nbsp;</td>
				<td align="right">
					<font size="2" face="Open-sans, sans-serif" color="#555454">
						{$product['quantity']}
					</font>
				</td>
				<td width="5">&nbsp;</td>
			</tr>
		</table>
	</td>
	<td style="border:1px solid #D6D4D4;">
		<table class="table">
			<tr>
				<td width="5">&nbsp;</td>
				<td align="right">
					<font size="2" face="Open-sans, sans-serif" color="#555454">
						{$product['price']}
					</font>
				</td>
				<td width="5">&nbsp;</td>
			</tr>
		</table>
	</td>
</tr>
  {if count($product['customization']) > 1}
  	{foreach $product['customization'] as $customization}
  		<tr>
  		<td colspan="3" style="border:1px solid #D6D4D4;">
  			<table class="table">
  				<tr>
  					<td width="5">&nbsp;</td>
  					<td>
  						<font size="2" face="Open-sans, sans-serif" color="#555454">
  							{$customization['customization_text'] nofilter}
  						</font>
  					</td>
  					<td width="5">&nbsp;</td>
  				</tr>
  			</table>
  		</td>
  		<td style="border:1px solid #D6D4D4;">
  			<table class="table">
  				<tr>
  					<td width="5">&nbsp;</td>
  					<td align="right">
  						<font size="2" face="Open-sans, sans-serif" color="#555454">
  							{if count($product['customization']) > 1}
  								{$customization['customization_quantity']}
  							{/if}
  						</font>
  					</td>
  					<td width="5">&nbsp;</td>
  				</tr>
  			</table>
  		</td>
  		<td style="border:1px solid #D6D4D4;"></td>
  	</tr>
  	{/foreach}
  {/if}
{/foreach}
