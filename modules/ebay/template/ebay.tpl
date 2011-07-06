<link rel="stylesheet" type="text/css" href="{MODULE_URL}template/ebay.css" />
<table border="0" cellpadding="0" cellspacing="0" class="ProductPrestashop">
<tbody>
	<tr class="headerProductPrestashop">
		<td class="headerLeftProductPrestashop"><img src="{SHOP_URL}img/logo.jpg" alt="{SHOP_NAME}" /></td>
		<td class="headerCenterProductPrestashop">{SLOGAN}</td>
		<td class="headerRightProductPrestashop">
			<a href="http://feedback.ebay.fr/ws/eBayISAPI.dll?ViewFeedback2&userid={EBAY_IDENTIFIER}&sspagename=VIP:feedback&ftab=FeedbackAsSeller">Consulter nos évaluations <img src="{MODULE_URL}template/images/stats.png" alt="Consulter nos évaluations" border="0" /></a><br />
			<a href="http://my.ebay.fr/ws/eBayISAPI.dll?AcceptSavedSeller&sellerid={EBAY_IDENTIFIER}&ssPageName=STRK:MEFS:ADDSTR">Ajouter cette boutique à mes favoris <img src="{MODULE_URL}template/images/favorite.png" alt="Ajouter cette boutique à mes favoris" border="0" /></a><br /><br />
			<form action="http://stores.ebay.fr/{EBAY_SHOP}/_i.html" method="GET">
				<input type="text" name="_nkw" class="headerSearchProductPrestashop" value="" />
				<input type="hidden" name="_armrs" value="1" />
				<input type="hidden" name="_from" value="" />
				<input type="hidden" name="_ipg" value="" />
				<input type="hidden" name="_sasi" value="1" />
			</form>
		</td>
	</tr>
	<tr>
		<td>
			<br />{MAIN_IMAGE}<br />
			{MEDIUM_IMAGE_1} {MEDIUM_IMAGE_2} {MEDIUM_IMAGE_3}<br clear="left" /><br />
		</td>
		<td colspan="2" class="bodyProductPrestashop">
			<br /><br />			
			<span class="bodyNameProductPrestashop">{PRODUCT_NAME}</span><br /><br />
			<span class="bodyPriceProductPrestashop">{PRODUCT_PRICE} {PRODUCT_PRICE_DISCOUNT}</span><br /><br />
			Disponibilité : <b>En Stock</b><br /><br /><br />

			<span class="bodyDescriptionProductPrestashop">{DESCRIPTION}</span>
		</td>
	</tr>
	<tr class="footerProductPrestashop"><td colspan="3">&nbsp;</td></tr>
</tbody>
</table>
