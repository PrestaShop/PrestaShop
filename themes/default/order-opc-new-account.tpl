<div id="opc_new_account" class="opc-main-block">
	<div id="opc_new_account-overlay" class="opc-overlay" style="display: none;"></div>
	<h2><span>1</span> {l s='Account'}</h2>
	<form action="{$link->getPageLink('authentication', true, NULL, "back=order-opc")|escape:'html'}" method="post" id="login_form" class="std">
		<fieldset>
			<h3>{l s='Already registered?'}</h3>
			<p><a href="#" id="openLoginFormBlock">&raquo; {l s='Click here'}</a></p>
			<div id="login_form_content" style="display:none;">
				<!-- Error return block -->
				<div id="opc_login_errors" class="error" style="display:none;"></div>
				<!-- END Error return block -->
				<div style="margin-left:40px;margin-bottom:5px;float:left;width:40%;">
					<label for="login_email">{l s='Email address'}</label>
					<span><input type="text" id="login_email" name="email" /></span>
				</div>
				<div style="margin-left:40px;margin-bottom:5px;float:left;width:40%;">
					<label for="login_passwd">{l s='Password'}</label>
					<span><input type="password" id="login_passwd" name="login_passwd" /></span>
					<a href="{$link->getPageLink('password', true)|escape:'html'}" class="lost_password">{l s='Forgot your password?'}</a>
				</div>
				<p class="submit">
					{if isset($back)}<input type="hidden" class="hidden" name="back" value="{$back|escape:'html':'UTF-8'}" />{/if}
					<input type="submit" id="SubmitLogin" name="SubmitLogin" class="button" value="{l s='Login'}" />
				</p>
			</div>
		</fieldset>
	</form>
	<form action="javascript:;" method="post" id="new_account_form" class="std" autocomplete="on" autofill="on">
		<fieldset>
			<h3 id="new_account_title">{l s='New Customer'}</h3>
			<div id="opc_account_choice">
				<div class="opc_float">
					<p class="title_block">{l s='Instant Checkout'}</p>
					<p>
						<input type="button" class="exclusive_large" id="opc_guestCheckout" value="{l s='Guest checkout'}" />
					</p>
				</div>

				<div class="opc_float">
					<p class="title_block">{l s='Create your account today and enjoy:'}</p>
					<ul class="bullet">
						<li>{l s='Personalized and secure access'}</li>
						<li>{l s='A fast and easy check out process'}</li>
						<li>{l s='Separate billing and shipping addresses'}</li>
					</ul>
					<p>
						<input type="button" class="button_large" id="opc_createAccount" value="{l s='Create an account'}" />
					</p>
				</div>
				<div class="clear"></div>
			</div>
			<div id="opc_account_form">
				{$HOOK_CREATE_ACCOUNT_TOP}
				<script type="text/javascript">
				// <![CDATA[
				idSelectedCountry = {if isset($guestInformations) && $guestInformations.id_state}{$guestInformations.id_state|intval}{else}false{/if};
				{if isset($countries)}
					{foreach from=$countries item='country'}
						{if isset($country.states) && $country.contains_states}
							countries[{$country.id_country|intval}] = new Array();
							{foreach from=$country.states item='state' name='states'}
								countries[{$country.id_country|intval}].push({ldelim}'id' : '{$state.id_state}', 'name' : '{$state.name|escape:'html':'UTF-8'}'{rdelim});
							{/foreach}
						{/if}
						{if $country.need_identification_number}
							countriesNeedIDNumber.push({$country.id_country|intval});
						{/if}	
						{if isset($country.need_zip_code)}
							countriesNeedZipCode[{$country.id_country|intval}] = {$country.need_zip_code};
						{/if}
					{/foreach}
				{/if}
				//]]>
				{literal}
				function vat_number()
				{
					if (($('#company').length) && ($('#company').val() != ''))
						$('#vat_number_block').show();
					else
						$('#vat_number_block').hide();
				}
				function vat_number_invoice()
				{
					if (($('#company_invoice').length) && ($('#company_invoice').val() != ''))
						$('#vat_number_block_invoice').show();
					else
						$('#vat_number_block_invoice').hide();
				}
				$(document).ready(function() {
					$('#company').on('input',function(){
						vat_number();
					});
					$('#company_invoice').on('input',function(){
						vat_number_invoice();
					});
					vat_number();
					vat_number_invoice();
					{/literal}
					$('.id_state option[value={if isset($guestInformations.id_state)}{$guestInformations.id_state|intval}{/if}]').prop('selected', true);
					$('.id_state_invoice option[value={if isset($guestInformations.id_state_invoice)}{$guestInformations.id_state_invoice|intval}{/if}]').prop('selected', true);
					{literal}
				});
				{/literal}
				</script>
				<!-- Error return block -->
				<div id="opc_account_errors" class="error" style="display:none;"></div>
				<!-- END Error return block -->
				<!-- Account -->
				<input type="hidden" id="is_new_customer" name="is_new_customer" value="0" />
				<input type="hidden" id="opc_id_customer" name="opc_id_customer" value="{if isset($guestInformations) && $guestInformations.id_customer}{$guestInformations.id_customer}{else}0{/if}" />
				<input type="hidden" id="opc_id_address_delivery" name="opc_id_address_delivery" value="{if isset($guestInformations) && $guestInformations.id_address_delivery}{$guestInformations.id_address_delivery}{else}0{/if}" />
				<input type="hidden" id="opc_id_address_invoice" name="opc_id_address_invoice" value="{if isset($guestInformations) && $guestInformations.id_address_delivery}{$guestInformations.id_address_delivery}{else}0{/if}" />
				<p class="required text">
					<label for="email">{l s='Email'} <sup>*</sup></label>
					<input type="text" class="text" id="email" name="email" value="{if isset($guestInformations) && $guestInformations.email}{$guestInformations.email}{/if}" />
				</p>
				<p class="required password is_customer_param">
					<label for="passwd">{l s='Password'} <sup>*</sup></label>
					<input type="password" class="text" name="passwd" id="passwd" />
					<span class="form_info">{l s='(five characters min.)'}</span>
				</p>
				<p class="radio required">
					<span>{l s='Title'}</span>
					{foreach from=$genders key=k item=gender}
						<input type="radio" name="id_gender" id="id_gender{$gender->id_gender}" value="{$gender->id_gender}" {if isset($smarty.post.id_gender) && $smarty.post.id_gender == $gender->id_gender}checked="checked"{/if} />
						<label for="id_gender{$gender->id_gender}" class="top">{$gender->name}</label>
					{/foreach}
				</p>
				<p class="required text">
					<label for="firstname">{l s='First name'} <sup>*</sup></label>
					<input type="text" class="text" id="customer_firstname" name="customer_firstname" onblur="$('#firstname').val($(this).val());" value="{if isset($guestInformations) && $guestInformations.customer_firstname}{$guestInformations.customer_firstname}{/if}" />
				</p>
				<p class="required text">
					<label for="lastname">{l s='Last name'} <sup>*</sup></label>
					<input type="text" class="text" id="customer_lastname" name="customer_lastname" onblur="$('#lastname').val($(this).val());" value="{if isset($guestInformations) && $guestInformations.customer_lastname}{$guestInformations.customer_lastname}{/if}" />
				</p>
				<p class="select">
					<span>{l s='Date of Birth'}</span>
					<select id="days" name="days">
						<option value="">-</option>
						{foreach from=$days item=day}
							<option value="{$day|escape:'html':'UTF-8'}" {if isset($guestInformations) && ($guestInformations.sl_day == $day)} selected="selected"{/if}>{$day|escape:'html':'UTF-8'}&nbsp;&nbsp;</option>
						{/foreach}
					</select>
					{*
						{l s='January'}
						{l s='February'}
						{l s='March'}
						{l s='April'}
						{l s='May'}
						{l s='June'}
						{l s='July'}
						{l s='August'}
						{l s='September'}
						{l s='October'}
						{l s='November'}
						{l s='December'}
					*}
					<select id="months" name="months">
						<option value="">-</option>
						{foreach from=$months key=k item=month}
							<option value="{$k|escape:'html':'UTF-8'}" {if isset($guestInformations) && ($guestInformations.sl_month == $k)} selected="selected"{/if}>{l s=$month}&nbsp;</option>
						{/foreach}
					</select>
					<select id="years" name="years">
						<option value="">-</option>
						{foreach from=$years item=year}
							<option value="{$year|escape:'html':'UTF-8'}" {if isset($guestInformations) && ($guestInformations.sl_year == $year)} selected="selected"{/if}>{$year|escape:'html':'UTF-8'}&nbsp;&nbsp;</option>
						{/foreach}
					</select>
				</p>
				{if isset($newsletter) && $newsletter}
				<p class="checkbox">
					<input type="checkbox" name="newsletter" id="newsletter" value="1" {if isset($guestInformations) && $guestInformations.newsletter}checked="checked"{/if} autocomplete="off"/>
					<label for="newsletter">{l s='Sign up for our newsletter!'}</label>
				</p>
				<p class="checkbox" >
					<input type="checkbox"name="optin" id="optin" value="1" {if isset($guestInformations) && $guestInformations.optin}checked="checked"{/if} autocomplete="off"/>
					<label for="optin">{l s='Receive special offers from our partners!'}</label>
				</p>
				{/if}
				<h3>{l s='Delivery address'}</h3>
				{$stateExist = false}
				{$postCodeExist = false}
				{$dniExist = false}
				{foreach from=$dlv_all_fields item=field_name}
				{if $field_name eq "company" && $b2b_enable}
				<p class="text">
					<label for="company">{l s='Company'}</label>
					<input type="text" class="text" id="company" name="company" value="{if isset($guestInformations) && $guestInformations.company}{$guestInformations.company}{/if}" />
				</p>
				{elseif $field_name eq "vat_number"}	
				<div id="vat_number_block" style="display:none;">
					<p class="text">
						<label for="vat_number">{l s='VAT number'}</label>
						<input type="text" class="text" name="vat_number" id="vat_number" value="{if isset($guestInformations) && $guestInformations.vat_number}{$guestInformations.vat_number}{/if}" />
					</p>
				</div>
				{elseif $field_name eq "dni"}
				{assign var='dniExist' value=true}
				<p class="text">
					<label for="dni">{l s='Identification number'}</label>
					<input type="text" class="text" name="dni" id="dni" value="{if isset($guestInformations) && $guestInformations.dni}{$guestInformations.dni}{/if}" />
					<span class="form_info">{l s='DNI / NIF / NIE'}</span>
				</p>
				{elseif $field_name eq "firstname"}
				<p class="required text">
					<label for="firstname">{l s='First name'} <sup>*</sup></label>
					<input type="text" class="text" id="firstname" name="firstname" value="{if isset($guestInformations) && $guestInformations.firstname}{$guestInformations.firstname}{/if}" />
				</p>
				{elseif $field_name eq "lastname"}
				<p class="required text">
					<label for="lastname">{l s='Last name'} <sup>*</sup></label>
					<input type="text" class="text" id="lastname" name="lastname" value="{if isset($guestInformations) && $guestInformations.lastname}{$guestInformations.lastname}{/if}" />
				</p>
				{elseif $field_name eq "address1"}
				<p class="required text">
					<label for="address1">{l s='Address'} <sup>*</sup></label>
					<input type="text" class="text" name="address1" id="address1" value="{if isset($guestInformations) && $guestInformations.address1}{$guestInformations.address1}{/if}" />
				</p>
				{elseif $field_name eq "address2"}
				<p class="text is_customer_param">
					<label for="address2">{l s='Address (Line 2)'}</label>
					<input type="text" class="text" name="address2" id="address2" value="{if isset($guestInformations) && $guestInformations.address2}{$guestInformations.address2}{/if}" />
				</p>
				{elseif $field_name eq "postcode"}
				{$postCodeExist = true}
				<p class="required postcode text">
					<label for="postcode">{l s='Zip / Postal code'} <sup>*</sup></label>
					<input type="text" class="text" name="postcode" id="postcode" value="{if isset($guestInformations) && $guestInformations.postcode}{$guestInformations.postcode}{/if}" onkeyup="$('#postcode').val($('#postcode').val().toUpperCase());" />
				</p>
				{elseif $field_name eq "city"}
				<p class="required text">
					<label for="city">{l s='City'} <sup>*</sup></label>
					<input type="text" class="text" name="city" id="city" value="{if isset($guestInformations) && $guestInformations.city}{$guestInformations.city}{/if}" />
				</p>
				{elseif $field_name eq "country" || $field_name eq "Country:name"}
				<p class="required select">
					<label for="id_country">{l s='Country'} <sup>*</sup></label>
					<select name="id_country" id="id_country">
						{foreach from=$countries item=v}
						<option value="{$v.id_country}"{if (isset($guestInformations) AND $guestInformations.id_country == $v.id_country) OR (!isset($guestInformations) && $sl_country == $v.id_country)} selected="selected"{/if}>{$v.name|escape:'html':'UTF-8'}</option>
						{/foreach}
					</select>
				</p>
				{elseif $field_name eq "state" || $field_name eq 'State:name'}
				{$stateExist = true}
				<p class="required id_state select" style="display:none;">
					<label for="id_state">{l s='State'} <sup>*</sup></label>
					<select name="id_state" id="id_state">
						<option value="">-</option>
					</select>
				</p>
				{/if}
				{/foreach}
				{if !$postCodeExist}
				<p class="required postcode text hidden">
					<label for="postcode">{l s='Zip / Postal code'} <sup>*</sup></label>
					<input type="text" class="text" name="postcode" id="postcode" value="{if isset($guestInformations) && $guestInformations.postcode}{$guestInformations.postcode}{/if}" onkeyup="$('#postcode').val($('#postcode').val().toUpperCase());" />
				</p>
				{/if}				
				{if !$stateExist}
				<p class="required id_state select hidden">
					<label for="id_state">{l s='State'} <sup>*</sup></label>
					<select name="id_state" id="id_state">
						<option value="">-</option>
					</select>
				</p>
				{/if}
				{if !$dniExist}
				<p class="required text dni">
					<label for="dni">{l s='Identification number'} <sup>*</sup></label>
					<input type="text" class="text" name="dni" id="dni" value="{if isset($guestInformations) && $guestInformations.dni}{$guestInformations.dni}{/if}" />
					<span class="form_info">{l s='DNI / NIF / NIE'}</span>
				</p>
				{/if}
				<p class="textarea is_customer_param">
					<label for="other">{l s='Additional information'}</label>
					<textarea name="other" id="other" cols="26" rows="3"></textarea>
				</p>
				{if isset($one_phone_at_least) && $one_phone_at_least}
					<p class="inline-infos required is_customer_param">{l s='You must register at least one phone number.'}</p>
				{/if}								
				<p class="text is_customer_param">
					<label for="phone">{l s='Home phone'}</label>
					<input type="text" class="text" name="phone" id="phone" value="{if isset($guestInformations) && $guestInformations.phone}{$guestInformations.phone}{/if}" />
				</p>
				<p class="{if isset($one_phone_at_least) && $one_phone_at_least}required {/if}text">
					<label for="phone_mobile">{l s='Mobile phone'}{if isset($one_phone_at_least) && $one_phone_at_least} <sup>*</sup>{/if}</label>
					<input type="text" class="text" name="phone_mobile" id="phone_mobile" value="{if isset($guestInformations) && $guestInformations.phone_mobile}{$guestInformations.phone_mobile}{/if}" />
				</p>
				<input type="hidden" name="alias" id="alias" value="{l s='My address'}"/>

				<p class="checkbox">
					<input type="checkbox" name="invoice_address" id="invoice_address"{if (isset($smarty.post.invoice_address) && $smarty.post.invoice_address) || (isset($guestInformations) && $guestInformations.invoice_address)} checked="checked"{/if} autocomplete="off"/>
					<label for="invoice_address"><b>{l s='Please use another address for invoice'}</b></label>
				</p>

				<div id="opc_invoice_address" class="is_customer_param">
					{assign var=stateExist value=false}
					{assign var=postCodeExist value=false}
					{assign var=dniExist value=false}
					<h3>{l s='Invoice address'}</h3>
					{foreach from=$inv_all_fields item=field_name}
					{if $field_name eq "company" &&  $b2b_enable}
					<p class="text">
						<label for="company_invoice">{l s='Company'}</label>
						<input type="text" class="text" id="company_invoice" name="company_invoice" value="" />
					</p>
					{elseif $field_name eq "vat_number"}
					<div id="vat_number_block_invoice" class="is_customer_param" style="display:none;">
						<p class="text">
							<label for="vat_number_invoice">{l s='VAT number'}</label>
							<input type="text" class="text" id="vat_number_invoice" name="vat_number_invoice" value="" />
						</p>
					</div>
					{elseif $field_name eq "dni"}
					{assign var='dniExist' value=true}
					<p class="text">
						<label for="dni_invoice">{l s='Identification number'}</label>
						<input type="text" class="text" name="dni_invoice" id="dni_invoice" value="{if isset($guestInformations) && $guestInformations.dni_invoice}{$guestInformations.dni_invoice}{/if}" />
						<span class="form_info">{l s='DNI / NIF / NIE'}</span>
					</p>
					{elseif $field_name eq "firstname"}
					<p class="required text">
						<label for="firstname_invoice">{l s='First name'} <sup>*</sup></label>
						<input type="text" class="text" id="firstname_invoice" name="firstname_invoice" value="{if isset($guestInformations) && $guestInformations.firstname_invoice}{$guestInformations.firstname_invoice}{/if}" />
					</p>
					{elseif $field_name eq "lastname"}
					<p class="required text">
						<label for="lastname_invoice">{l s='Last name'} <sup>*</sup></label>
						<input type="text" class="text" id="lastname_invoice" name="lastname_invoice" value="{if isset($guestInformations) && $guestInformations.lastname_invoice}{$guestInformations.lastname_invoice}{/if}" />
					</p>
					{elseif $field_name eq "address1"}
					<p class="required text">
						<label for="address1_invoice">{l s='Address'} <sup>*</sup></label>
						<input type="text" class="text" name="address1_invoice" id="address1_invoice" value="{if isset($guestInformations) && $guestInformations.address1_invoice}{$guestInformations.address1_invoice}{/if}" />
					</p>
					{elseif $field_name eq "address2"}
					<p class="text is_customer_param">
						<label for="address2_invoice">{l s='Address (Line 2)'}</label>
						<input type="text" class="text" name="address2_invoice" id="address2_invoice" value="{if isset($guestInformations) && $guestInformations.address2_invoice}{$guestInformations.address2_invoice}{/if}" />
					</p>
					{elseif $field_name eq "postcode"}
					{$postCodeExist = true}
					<p class="required postcode_invoice text">
						<label for="postcode_invoice">{l s='Zip / Postal Code'} <sup>*</sup></label>
						<input type="text" class="text" name="postcode_invoice" id="postcode_invoice" value="{if isset($guestInformations) && $guestInformations.postcode_invoice}{$guestInformations.postcode_invoice}{/if}" onkeyup="$('#postcode_invoice').val($('#postcode_invoice').val().toUpperCase());" />
					</p>
					{elseif $field_name eq "city"}
					<p class="required text">
						<label for="city_invoice">{l s='City'} <sup>*</sup></label>
						<input type="text" class="text" name="city_invoice" id="city_invoice" value="{if isset($guestInformations) && $guestInformations.city_invoice}{$guestInformations.city_invoice}{/if}" />
					</p>
					{elseif $field_name eq "country" || $field_name eq "Country:name"}
					<p class="required select">
						<label for="id_country_invoice">{l s='Country'} <sup>*</sup></label>
						<select name="id_country_invoice" id="id_country_invoice">
							<option value="">-</option>
							{foreach from=$countries item=v}
							<option value="{$v.id_country}"{if (isset($guestInformations) AND $guestInformations.id_country_invoice == $v.id_country) OR (!isset($guestInformations) && $sl_country == $v.id_country)} selected="selected"{/if}>{$v.name|escape:'html':'UTF-8'}</option>
							{/foreach}
						</select>
					</p>
					{elseif $field_name eq "state" || $field_name eq 'State:name'}
					{$stateExist = true}
					<p class="required id_state_invoice select" style="display:none;">
						<label for="id_state_invoice">{l s='State'} <sup>*</sup></label>
						<select name="id_state_invoice" id="id_state_invoice">
							<option value="">-</option>
						</select>
					</p>
					{/if}
					{/foreach}
					{if !$postCodeExist}
					<p class="required postcode_invoice text hidden">
						<label for="postcode_invoice">{l s='Zip / Postal Code'} <sup>*</sup></label>
						<input type="text" class="text" name="postcode_invoice" id="postcode_invoice" value="" onkeyup="$('#postcode').val($('#postcode').val().toUpperCase());" />
					</p>
					{/if}					
					{if !$stateExist}
					<p class="required id_state_invoice select hidden">
						<label for="id_state_invoice">{l s='State'} <sup>*</sup></label>
						<select name="id_state_invoice" id="id_state_invoice">
							<option value="">-</option>
						</select>
					</p>
					{/if}
					{if !$dniExist}
					<p class="required text dni_invoice">
						<label for="dni_invoice">{l s='Identification number'} <sup>*</sup></label>
						<input type="text" class="text" name="dni_invoice" id="dni_invoice" value="{if isset($guestInformations) && $guestInformations.dni_invoice}{$guestInformations.dni_invoice}{/if}" />
						<span class="form_info">{l s='DNI / NIF / NIE'}</span>
					</p>
					{/if}
					<p class="textarea is_customer_param">
						<label for="other_invoice">{l s='Additional information'}</label>
						<textarea name="other_invoice" id="other_invoice" cols="26" rows="3"></textarea>
					</p>
					{if isset($one_phone_at_least) && $one_phone_at_least}
						<p class="inline-infos required is_customer_param">{l s='You must register at least one phone number.'}</p>
					{/if}					
					<p class="text is_customer_param">
						<label for="phone_invoice">{l s='Home phone'}</label>
						<input type="text" class="text" name="phone_invoice" id="phone_invoice" value="{if isset($guestInformations) && $guestInformations.phone_invoice}{$guestInformations.phone_invoice}{/if}" />
					</p>
					<p class="{if isset($one_phone_at_least) && $one_phone_at_least}required {/if}text">
						<label for="phone_mobile_invoice">{l s='Mobile phone'}{if isset($one_phone_at_least) && $one_phone_at_least} <sup>*</sup>{/if}</label>
						<input type="text" class="text" name="phone_mobile_invoice" id="phone_mobile_invoice" value="{if isset($guestInformations) && $guestInformations.phone_mobile_invoice}{$guestInformations.phone_mobile_invoice}{/if}" />
					</p>
					<input type="hidden" name="alias_invoice" id="alias_invoice" value="{l s='My Invoice address'}" />
				</div>
				{$HOOK_CREATE_ACCOUNT_FORM}
				<p class="submit">
					<input type="submit" class="exclusive button" name="submitAccount" id="submitAccount" value="{l s='Save'}" />
				</p>
				<p style="display: none;" id="opc_account_saved">
					{l s='Account information saved successfully'}
				</p>
				<p class="required opc-required" style="clear: both;">
					<sup>*</sup>{l s='Required field'}
				</p>
				<!-- END Account -->
			</div>
		</fieldset>
	</form>
	<div class="clear"></div>
</div>
