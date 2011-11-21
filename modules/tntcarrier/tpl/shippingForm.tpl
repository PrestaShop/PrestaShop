<script type="text/javascript" src="../modules/{$varShipping.moduleName}/js/jquery-ui.js"></script>
<script type="text/javascript" src="../modules/{$varShipping.moduleName}/js/relaisColis.js"></script>
<script type="text/javascript" src="../modules/{$varShipping.moduleName}/js/shipping.js"></script>
<link type="text/css" href="../modules/{$varShipping.moduleName}/css/ui.tabs.css" rel="stylesheet">
<link type="text/css" href="../modules/{$varShipping.moduleName}/css/ui.dialog.css" rel="stylesheet">
<link type="text/css" href="../modules/{$varShipping.moduleName}/css/tntB2CRelaisColis.css" rel="stylesheet">
<fieldset style="border: 0px;">
	<div id="googleMapTnt" style="float:right;display:{if $varShipping.collect == '1'}none{/if}">
		<div id="tntB2CRelaisColis" class="exemplePresentation">
			<script type="text/javascript"> tntB2CRelaisColis();</script>
		</div>
		<div style="text-align: justify; font-family: arial,helvetica,sans-serif; font-size: 10pt;">
			<div style="height: 25px;">&nbsp;</div>
			<div id="exempleIntegration">
				<input style="float:right" type="button" value="{$lang.fillDataInTheForm}" onclick="callbackSelectionRelais();" />
			</div>
		</div>
	</div>
	<form action="index.php?tab={$glob.tab}&configure={$glob.configure}&token={$glob.token}&tab_module={$glob.tab_module}&module_name={$glob.module_name}&id_tab=2&section=shipping" method="post" class="form" id="configFormShipping">		
		<h4>{$lang.shipping} :</h4>
		<label>{$lang.collect} : </label>
		<div class="margin-form">
			<input type="radio" id="tnt_carrier_collect_no" name="tnt_carrier_shipping_collect" value="0" {if $varShipping.collect == '0'} checked="checked" {/if} /> : {$lang.noDeposit}<br/>
			<input type="radio" id="tnt_carrier_collect_yes" onclick="collectButtonClick()" name="tnt_carrier_shipping_collect" value="1" {if $varShipping.collect == '1'} checked="checked" {/if} /> : {$lang.yes}
		</div>
		<div id="divPex" style="display:{if $varShipping.collect == '1'}none{/if}">
			<a href="#" style="color:blue" onclick="depositButtonClick();return false;">{$lang.chooseYourDepositoryLocation}</a><br/><br/>
			<label>{$lang.pexCode} : </label>
			<div class="margin-form"><input type="text" size="20" id="tnt_carrier_shipping_pex" name="tnt_carrier_shipping_pex" value="{$varShipping.pex}" /></div>
		</div>
		<label>{$lang.companyName} : </label>
		<div class="margin-form"><input type="text" size="20" id="tnt_carrier_shipping_company" name="tnt_carrier_shipping_company" value="{$varShipping.company}" /></div>
		<label>{$lang.lastName} : </label>
		<div class="margin-form"><input type="text" size="20" id="tnt_carrier_shipping_last_name" name="tnt_carrier_shipping_last_name" value="{$varShipping.lastName}" /></div>
		<label>{$lang.firstName} : </label>
		<div class="margin-form"><input type="text" size="20" id="tnt_carrier_shipping_first_name" name="tnt_carrier_shipping_first_name" value="{$varShipping.firstName}" /></div>
		<label>{$lang.address1} : </label>
		<div class="margin-form"><input type="text" size="20" id="tnt_carrier_shipping_address1" name="tnt_carrier_shipping_address1" value="{$varShipping.address1}" /></div>
		<label>{$lang.address2} : </label>
		<div class="margin-form"><input type="text" size="20" id="tnt_carrier_shipping_address2" name="tnt_carrier_shipping_address2" value="{$varShipping.address2}" /></div>
		<label>{$lang.zip} : </label>
		<div class="margin-form"><input type="text" size="20" id="tnt_carrier_shipping_postal_code" name="tnt_carrier_shipping_postal_code" value="{$varShipping.zipCode}" /></div>
		<label>{$lang.city} : </label>
		<div class="margin-form"><input type="text" size="20" id="tnt_carrier_shipping_city" name="tnt_carrier_shipping_city" value="{$varShipping.city}" /></div><br/>
		<label>{$lang.email} : </label>
		<div class="margin-form"><input type="text" size="20" name="tnt_carrier_shipping_email" value="{$varShipping.email}" /></div>
		<label>{$lang.phone} : </label>
		<div class="margin-form"><input type="text" size="20" name="tnt_carrier_shipping_phone" value="{$varShipping.phone}" /></div>
		<div id="divClosing" style="display:{if $varShipping.collect == '0'}none{/if}">
			<label>{$lang.closingTime} : </label>
			<div class="margin-form"><input type="text" size="20" name="tnt_carrier_shipping_closing" value="{$varShipping.closing}" /> (HH:MM)</div>
			<br/>
		</div>
		<!--<label>{$lang.saturdayDelivery} : </label>
		<div class="margin-form">
			<input type="radio" id="tnt_carrier_delivery_yes" name="tnt_carrier_shipping_delivery" value="1" '.(Configuration::get('TNT_CARRIER_SHIPPING_DELIVERY') == 1 ? 'checked="checked"' : ''} /> : {$lang.yes}<br/>
			<input type="radio" id="tnt_carrier_delivery_no" name="tnt_carrier_shipping_delivery" value="0" '.(Configuration::get('TNT_CARRIER_SHIPPING_DELIVERY') == 0 ? 'checked="checked"' : ''} /> : {$lang.no}
		</div>-->
		<br/><br/>
		<label>{$lang.labelFormatPrinting} : </label><br/><br/>
		<select name="tnt_carrier_print_sticker" value="{$varShipping.sticker}" >
			<option value="STDA4">{$lang.a4printing}</option>
			<option value="THERMAL">THERMAL</option>
			<option value="THERMAL,NO_LOGO">THERMAL {$lang.withoutPrintingLogoTNT}</option>
			<option value="THERMAL,ROTATE_180">THERMAL {$lang.withReversePrint}</option>
			<option value="THERMAL,NO_LOGO,ROTATE_180">THERMAL {$lang.withoutPrintingLogoTNTWithReversePrint}</option>
		</select><br/><br/>
		<div class="margin-form"><input class="button" name="submitSave" type="submit"></div>
	</form>
</fieldset>