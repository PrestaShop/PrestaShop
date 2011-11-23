<script type="text/javascript" src="../modules/{$varShipping.moduleName}/js/jquery-ui.js"></script>
<script type="text/javascript" src="../modules/{$varShipping.moduleName}/js/relaisColis.js"></script>
<script type="text/javascript" src="../modules/{$varShipping.moduleName}/js/shipping.js"></script>
<link type="text/css" href="../modules/{$varShipping.moduleName}/css/ui.tabs.css" rel="stylesheet">
<link type="text/css" href="../modules/{$varShipping.moduleName}/css/ui.dialog.css" rel="stylesheet">
<link type="text/css" href="../modules/{$varShipping.moduleName}/css/tntB2CRelaisColis.css" rel="stylesheet">
<fieldset style="border: 0px;">
	<form action="index.php?tab={$glob.tab}&configure={$glob.configure}&token={$glob.token}&tab_module={$glob.tab_module}&module_name={$glob.module_name}&id_tab=2&section=shipping" method="post" class="form" id="configFormShipping">		
		<h4>{l s='Shipping' mod='tntcarrier'} :</h4>
		<!--<span style="font-weight:bold">{l s='Would you like TNT to pick up your package directly at your warehouse ?' mod='tntcarrier'}</span><br/><br/>
        <div style='color: #7F7F7F;font-size: 0.85em;padding: 0 0 1em 80px;'>
			<input type="radio" id="tnt_carrier_collect_no" name="tnt_carrier_shipping_collect" onclick="depositButtonClick()" value="0" {if $varShipping.collect == '0'} checked="checked" {/if} /> : {l s='No (then you will have to deposit your packages in a TNT depositary. Thank you to choose in the form the depositary agency you wish to)' mod='tntcarrier'}<br/>
			<input type="radio" id="tnt_carrier_collect_yes" onclick="collectButtonClick()" name="tnt_carrier_shipping_collect" value="1" {if $varShipping.collect == '1'} checked="checked" {/if} /> : {l s='Yes' mod='tntcarrier'}
		</div>-->
		<!--<div id="divPex" style="display:{if $varShipping.collect == '1'}none{/if}">
			<input type='button' class='button' onclick="depositButtonClick();return false;" value="{l s='Choose your depository location' mod='tntcarrier'}" /><br/><br/>
		</div>-->
        <!--<div id="googleMapTnt" style="float:right;display:{if $varShipping.collect == '1'}none{/if}">
            <div id="tntB2CRelaisColis" class="exemplePresentation">
                <script type="text/javascript"> tntB2CRelaisColis();</script>
            </div>
            <div style="text-align: justify; font-family: arial,helvetica,sans-serif; font-size: 10pt;">
                <div style="height: 25px;">&nbsp;</div>
                <div id="exempleIntegration">
                </div>
            </div>
        </div>-->
        <!--{if $varShipping.collect == '1'}
        <h4>{l s='Your informations' mod='tntcarrier'}</h4>
        {else}
        <h4>{l s='Depository agency information' mod='tntcarrier'}</h4>
        {/if}-->
        <!--<label>{l s='Pex code' mod='tntcarrier'} : </label>-->
        <div class="margin-form"><input type="hidden" size="20" id="tnt_carrier_shipping_pex" name="tnt_carrier_shipping_pex" value="{$varShipping.pex}" /></div>
        <label>{l s='Company Name' mod='tntcarrier'} : </label>
		<div class="margin-form"><input type="text" size="20" id="tnt_carrier_shipping_company" name="tnt_carrier_shipping_company" value="{$varShipping.company}" /> <span style="color:red">*</span></div>
		<!--<div id='tnt_exp_names' style="display:{if $varShipping.collect == '0'}none{/if}">
			<label>{l s='Last name' mod='tntcarrier'} : </label>
			<div class="margin-form"><input type="text" size="20" id="tnt_carrier_shipping_last_name" name="tnt_carrier_shipping_last_name" value="{$varShipping.lastName}" /> <span style="color:red">*</div>
			<label>{l s='First name' mod='tntcarrier'} : </label>
			<div class="margin-form"><input type="text" size="20" id="tnt_carrier_shipping_first_name" name="tnt_carrier_shipping_first_name" value="{$varShipping.firstName}" /> <span style="color:red">*</div>
		</div>-->
		<label>{l s='Address line 1' mod='tntcarrier'} : </label>
		<div class="margin-form"><input type="text" size="20" id="tnt_carrier_shipping_address1" name="tnt_carrier_shipping_address1" value="{$varShipping.address1}" /> <span style="color:red">*</div>
		<label>{l s='Address line 2' mod='tntcarrier'} : </label>
		<div class="margin-form"><input type="text" size="20" id="tnt_carrier_shipping_address2" name="tnt_carrier_shipping_address2" value="{$varShipping.address2}" /> <span style="color:red">*</div>
		<label>{l s='Postal Code' mod='tntcarrier'} : </label>
		<div class="margin-form"><input type="text" size="20" id="tnt_carrier_shipping_postal_code" name="tnt_carrier_shipping_postal_code" value="{$varShipping.zipCode}" /> <span style="color:red">*</div>
		<label>{l s='City' mod='tntcarrier'} : </label>
		<div class="margin-form"><input type="text" size="20" id="tnt_carrier_shipping_city" name="tnt_carrier_shipping_city" value="{$varShipping.city}" /> <span style="color:red">*</div>
		<label>{l s='Company Closing Time' mod='tntcarrier'} : </label>
		<div class="margin-form"><input type="text" size="20" name="tnt_carrier_shipping_closing" value="{$varShipping.closing}" /> (HH:MM) <span style="color:red">*</div>
		<br/>
		<label>{l s='Contact last name' mod='tntcarrier'} : </label>
		<div class="margin-form"><input type="text" size="20" id="tnt_carrier_shipping_last_name" name="tnt_carrier_shipping_last_name" value="{$varShipping.lastName}" /> <span style="color:red">*</div>
		<label>{l s='Contact first name' mod='tntcarrier'} : </label>
		<div class="margin-form"><input type="text" size="20" id="tnt_carrier_shipping_first_name" name="tnt_carrier_shipping_first_name" value="{$varShipping.firstName}" /> <span style="color:red">*</div>
		<label>{l s='Contact Email Address' mod='tntcarrier'} : </label>
		<div class="margin-form"><input type="text" size="20" name="tnt_carrier_shipping_email" value="{$varShipping.email}" /> <span style="color:red">*</div>
		<label>{l s='Contact Phone Number' mod='tntcarrier'} : </label>
		<div class="margin-form"><input type="text" size="20" name="tnt_carrier_shipping_phone" value="{$varShipping.phone}" /> <span style="color:red">*</div>
		<!--<div id="divClosing" style="display:{if $varShipping.collect == '0'}none{/if}">
			<label>{l s='Your Closing Time' mod='tntcarrier'} : </label>
			<div class="margin-form"><input type="text" size="20" name="tnt_carrier_shipping_closing" value="{$varShipping.closing}" /> (HH:MM) <span style="color:red">*</div>
			<br/>
		</div>-->
		<!--<label>{l s='Saturday Delivery' mod='tntcarrier'} : </label>
		<div class="margin-form">
			<input type="radio" id="tnt_carrier_delivery_yes" name="tnt_carrier_shipping_delivery" value="1" '.(Configuration::get('TNT_CARRIER_SHIPPING_DELIVERY') == 1 ? 'checked="checked"' : ''} /> : {l s='Yes' mod='tntcarrier'}<br/>
			<input type="radio" id="tnt_carrier_delivery_no" name="tnt_carrier_shipping_delivery" value="0" '.(Configuration::get('TNT_CARRIER_SHIPPING_DELIVERY') == 0 ? 'checked="checked"' : ''} /> : {l s='No' mod='tntcarrier'}
		</div>-->
		<br/><br/>
		<span style="font-weight:bold">{l s='Label Format for printing (This Label will have to be sticked on the package)' mod='tntcarrier'} : </span><br/><br/>
        <div style="padding-left:210px">
		<select name="tnt_carrier_print_sticker" value="{$varShipping.sticker}" >
			<option value="STDA4">{l s='A4 printing' mod='tntcarrier'}</option>
			<option value="THERMAL">THERMAL</option>
			<option value="THERMAL,NO_LOGO">THERMAL {l s='without printing the logo TNT' mod='tntcarrier'}</option>
			<option value="THERMAL,ROTATE_180">THERMAL {l s='with a reverse print' mod='tntcarrier'}</option>
			<option value="THERMAL,NO_LOGO,ROTATE_180">THERMAL {l s='without printing the logo TNT and with a reverse print' mod='tntcarrier'}</option>
		</select></div><br/><br/>
		<div class="margin-form"><input class="button" name="submitSave" type="submit" value="{l s='save' mod='tntcarrier'}"></div>
	</form>
<span style="color:red">* : {l s='Required fields' mod='tntcarrier'}</span>
</fieldset>