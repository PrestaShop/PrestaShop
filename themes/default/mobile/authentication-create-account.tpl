<form action="{$link->getPageLink('authentication', true)}" method="post" id="account-creation_form" class="std">
	{$HOOK_CREATE_ACCOUNT_TOP}
	<fieldset class="account_creation">
		<h3>{l s='Your personal information'}</h3>
		<p class="radio required">
			<label>{l s='Title'}</label>
			{foreach from=$genders key=k item=gender}
				<input type="radio" name="id_gender" id="id_gender{$gender->id}" value="{$gender->id}" {if isset($smarty.post.id_gender) && $smarty.post.id_gender == $gender->id}checked="checked"{/if} />
				<label for="id_gender{$gender->id}" class="top">{$gender->name}</label>
			{/foreach}
		</p>
		<p class="required">
			<label for="customer_firstname">{l s='First name'} <sup>*</sup></label>
			<input onkeyup="$('#firstname').val(this.value);" type="text" class="text" id="customer_firstname" name="customer_firstname" value="{if isset($smarty.post.customer_firstname)}{$smarty.post.customer_firstname}{/if}" />
		</p>
		<p class="required">
			<label for="customer_lastname">{l s='Last name'} <sup>*</sup></label>
			<input onkeyup="$('#lastname').val(this.value);" type="text" class="text" id="customer_lastname" name="customer_lastname" value="{if isset($smarty.post.customer_lastname)}{$smarty.post.customer_lastname}{/if}" />
		</p>
		<p class="required">
			<label for="email">{l s='E-Mail:'} <sup>*</sup></label>
			<input type="text" class="text" id="email" name="email" value="{if isset($smarty.post.email)}{$smarty.post.email}{/if}" />
		</p>
		<p class="required">
			<label for="passwd">{l s='Password'} <sup>*</sup></label>
			<input type="password" class="text" name="passwd" id="passwd" />
			<span>{l s='(5 characters min.)'}</span>
		</p>
		<p class="select">
			<label>{l s='Date of Birth'}</label>
			<select id="days" name="days">
				<option value="">-</option>
				{foreach from=$days item=day}
					<option value="{$day|escape:'htmlall':'UTF-8'}" {if ($sl_day == $day)} selected="selected"{/if}>{$day|escape:'htmlall':'UTF-8'}&nbsp;&nbsp;</option>
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
					<option value="{$k|escape:'htmlall':'UTF-8'}" {if ($sl_month == $k)} selected="selected"{/if}>{l s=$month}&nbsp;</option>
				{/foreach}
			</select>
			<select id="years" name="years">
				<option value="">-</option>
				{foreach from=$years item=year}
					<option value="{$year|escape:'htmlall':'UTF-8'}" {if ($sl_year == $year)} selected="selected"{/if}>{$year|escape:'htmlall':'UTF-8'}&nbsp;&nbsp;</option>
				{/foreach}
			</select>
		</p>
		{if $newsletter}
		<p class="checkbox" >
			<input type="checkbox" name="newsletter" id="newsletter" value="1" {if isset($smarty.post.newsletter) AND $smarty.post.newsletter == 1} checked="checked"{/if} />
			<label for="newsletter">{l s='Sign up for our newsletter!'}</label>
		</p>
		<p class="checkbox" >
			<input type="checkbox"name="optin" id="optin" value="1" {if isset($smarty.post.optin) AND $smarty.post.optin == 1} checked="checked"{/if} />
			<label for="optin">{l s='Receive special offers from our partners!'}</label>
		</p>
		{/if}
	</fieldset>
	{if $b2b_enable}
	<fieldset class="account_creation">
		<h3>{l s='Your company information'}</h3>
		<p class="text">
			<label for="">{l s='Company'}</label>
			<input type="text" class="text" id="company" name="company" value="{if isset($smarty.post.company)}{$smarty.post.company}{/if}" />
		</p>
		<p class="text">
			<label for="siret">{l s='SIRET'}</label>
			<input type="text" class="text" id="siret" name="siret" value="{if isset($smarty.post.siret)}{$smarty.post.siret}{/if}" />
		</p>
		<p class="text">
			<label for="ape">{l s='APE'}</label>
			<input type="text" class="text" id="ape" name="ape" value="{if isset($smarty.post.ape)}{$smarty.post.ape}{/if}" />
		</p>
		<p class="text">
			<label for="website">{l s='Website'}</label>
			<input type="text" class="text" id="website" name="website" value="{if isset($smarty.post.website)}{$smarty.post.website}{/if}" />
		</p>
	</fieldset>
	{/if}
	{if isset($PS_REGISTRATION_PROCESS_TYPE) && $PS_REGISTRATION_PROCESS_TYPE}
	<fieldset class="account_creation">
		<h3>{l s='Your address'}</h3>
		{foreach from=$dlv_all_fields item=field_name}
			{if $field_name eq "company"}
				<p class="text">
					<label for="company">{l s='Company'}</label>
					<input type="text" class="text" id="company" name="company" value="{if isset($smarty.post.company)}{$smarty.post.company}{/if}" />
				</p>
			{elseif $field_name eq "vat_number"}
				<div id="vat_number" style="display:none;">
					<p class="text">
						<label for="vat_number">{l s='VAT number'}</label>
						<input type="text" class="text" name="vat_number" value="{if isset($smarty.post.vat_number)}{$smarty.post.vat_number}{/if}" />
					</p>
				</div>
			{elseif $field_name eq "firstname"}
				<p class="required text">
					<label for="firstname">{l s='First name'} <sup>*</sup></label>
					<input type="text" class="text" id="firstname" name="firstname" value="{if isset($smarty.post.firstname)}{$smarty.post.firstname}{/if}" />
				</p>
			{elseif $field_name eq "lastname"}
				<p class="required text">
					<label for="lastname">{l s='Last name'} <sup>*</sup></label>
					<input type="text" class="text" id="lastname" name="lastname" value="{if isset($smarty.post.lastname)}{$smarty.post.lastname}{/if}" />
				</p>
			{elseif $field_name eq "address1"}
				<p class="required text">
					<label for="address1">{l s='Address'} <sup>*</sup></label>
					<input type="text" class="text" name="address1" id="address1" value="{if isset($smarty.post.address1)}{$smarty.post.address1}{/if}" />
					<span class="inline-infos">{l s='Street address, P.O. Box, company name, etc.'}</span>
				</p>
			{elseif $field_name eq "address2"}
				<p class="text">
					<label for="address2">{l s='Address (Line 2)'}</label>
					<input type="text" class="text" name="address2" id="address2" value="{if isset($smarty.post.address2)}{$smarty.post.address2}{/if}" />
					<span class="inline-infos">{l s='Apartment, suite, unit, building, floor, etc...'}</span>
				</p>
			{elseif $field_name eq "postcode"}
				<p class="required postcode text">
					<label for="postcode">{l s='Zip / Postal Code'} <sup>*</sup></label>
					<input type="text" class="text" name="postcode" id="postcode" value="{if isset($smarty.post.postcode)}{$smarty.post.postcode}{/if}" onkeyup="$('#postcode').val($('#postcode').val().toUpperCase());" />
				</p>
			{elseif $field_name eq "city"}
				<p class="required text">
					<label for="city">{l s='City'} <sup>*</sup></label>
					<input type="text" class="text" name="city" id="city" value="{if isset($smarty.post.city)}{$smarty.post.city}{/if}" />
				</p>
				<!--
					if customer hasn't update his layout address, country has to be verified
					but it's deprecated
				-->
			{elseif $field_name eq "Country:name" || $field_name eq "country"}
				<p class="required select">
					<label for="id_country">{l s='Country'} <sup>*</sup></label>
					<select name="id_country" id="id_country">
						<option value="">-</option>
						{foreach from=$countries item=v}
						<option value="{$v.id_country}" {if ($sl_country == $v.id_country)} selected="selected"{/if}>{$v.name|escape:'htmlall':'UTF-8'}</option>
						{/foreach}
					</select>
				</p>
			{elseif $field_name eq "State:name" || $field_name eq 'state'}
				{assign var='stateExist' value=true}
				<p class="required id_state select">
					<label for="id_state">{l s='State'} <sup>*</sup></label>
					<select name="id_state" id="id_state">
						<option value="">-</option>
					</select>
				</p>
			{/if}
		{/foreach}
		{if $stateExist eq false}
			<p class="required id_state select">
				<label for="id_state">{l s='State'} <sup>*</sup></label>
				<select name="id_state" id="id_state">
					<option value="">-</option>
				</select>
			</p>
		{/if}
		<p class="textarea">
			<label for="other">{l s='Additional information'}</label>
			<textarea name="other" id="other" cols="26" rows="3">{if isset($smarty.post.other)}{$smarty.post.other}{/if}</textarea>
		</p>
		<p class="inline-infos">{l s='You must register at least one phone number'}</p>
		<p class="text">
			<label for="phone">{l s='Home phone'}</label>
			<input type="text" class="text" name="phone" id="phone" value="{if isset($smarty.post.phone)}{$smarty.post.phone}{/if}" />
		</p>
		<p class="text">
			<label for="phone_mobile">{l s='Mobile phone'} <sup>*</sup></label>
			<input type="text" class="text" name="phone_mobile" id="phone_mobile" value="{if isset($smarty.post.phone_mobile)}{$smarty.post.phone_mobile}{/if}" />
		</p>
		<p class="required text" id="address_alias">
			<label for="alias">{l s='Assign an address alias for future reference'} <sup>*</sup></label>
			<input type="text" class="text" name="alias" id="alias" value="{if isset($smarty.post.alias)}{$smarty.post.alias}{else}{l s='My address'}{/if}" />
		</p>
	</fieldset>
	<fieldset class="account_creation dni">
		<h3>{l s='Tax identification'}</h3>
		<p class="required text">
			<label for="dni">{l s='Identification number'}</label>
			<input type="text" class="text" name="dni" id="dni" value="{if isset($smarty.post.dni)}{$smarty.post.dni}{/if}" />
			<span class="form_info">{l s='DNI / NIF / NIE'}</span>
		</p>
	</fieldset>
	{/if}
	{$HOOK_CREATE_ACCOUNT_FORM}
	<p class="cart_navigation required submit">
		<input type="hidden" name="email_create" value="1" />
		<input type="hidden" name="is_new_customer" value="1" />
		{if isset($back)}<input type="hidden" class="hidden" name="back" value="{$back|escape:'htmlall':'UTF-8'}" />{/if}
		<input type="submit" name="submitAccount" id="submitAccount" value="{l s='Register'}" class="exclusive" />
		<span><sup>*</sup>{l s='Required field'}</span>
	</p>

</form>