{*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<script type="text/javascript">
// <![CDATA[
var idSelectedCountry = {if isset($smarty.post.id_state)}{$smarty.post.id_state|intval}{else}{if isset($address->id_state)}{$address->id_state|intval}{else}false{/if}{/if};
var countries = new Array();
var countriesNeedIDNumber = new Array();
var countriesNeedZipCode = new Array();
{foreach from=$countries item='country'}
	{if isset($country.states) && $country.contains_states}
		countries[{$country.id_country|intval}] = new Array();
		{foreach from=$country.states item='state' name='states'}
			countries[{$country.id_country|intval}].push({ldelim}'id' : '{$state.id_state}', 'name' : '{$state.name|addslashes}'{rdelim});
		{/foreach}
	{/if}
	{if $country.need_identification_number}
		countriesNeedIDNumber.push({$country.id_country|intval});
	{/if}
	{if isset($country.need_zip_code)}
		countriesNeedZipCode[{$country.id_country|intval}] = {$country.need_zip_code};
	{/if}
{/foreach}
$(function(){ldelim}
	$('.id_state option[value={if isset($smarty.post.id_state)}{$smarty.post.id_state|intval}{else}{if isset($address->id_state)}{$address->id_state|intval}{/if}{/if}]').attr('selected', true);
{rdelim});
{literal}
	$(document).ready(function() {
		$('#company').on('input',function(){
			vat_number();
		});
		vat_number();
		function vat_number()
		{
			if ($('#company').val() != '')
				$('#vat_number').show();
			else
				$('#vat_number').hide();
		}
	});
{/literal}
//]]>
</script>

{capture name=path}{l s='Your addresses'}{/capture}
<div class="box">
    <h1 class="page-subheading">{l s='Your addresses'}</h1>
    
    <p class="info-title">
    {if isset($id_address) && (isset($smarty.post.alias) || isset($address->alias))}
        {l s='Modify address'} 
        {if isset($smarty.post.alias)}
            "{$smarty.post.alias}"
        {else}
            {if isset($address->alias)}"{$address->alias|escape:'html'}"{/if}
        {/if}
    {else}
        {l s='To add a new address, please fill out the form below.'}
    {/if}
    </p>
    
    {include file="$tpl_dir./errors.tpl"}
    
    <p class="required"><sup>*</sup>{l s='Required field'}</p>
    
    <form action="{$link->getPageLink('address', true)|escape:'html'}" method="post" class="std" id="add_address">
        <fieldset>
            <!--h3 class="page-subheading">{if isset($id_address)}{l s='Your address'}{else}{l s='New address'}{/if}</h3-->
            <div class="required form-group dni">
                <label for="dni">{l s='Identification number'} <sup>*</sup></label>
                <input type="text" class="form-control" name="dni" id="dni" value="{if isset($smarty.post.dni)}{$smarty.post.dni}{else}{if isset($address->dni)}{$address->dni|escape:'html'}{/if}{/if}" />
                <span class="form_info">{l s='DNI / NIF / NIE'}</span>
            </div>
        {assign var="stateExist" value="false"}
        {assign var="postCodeExist" value="false"}
        {foreach from=$ordered_adr_fields item=field_name}
            {if $field_name eq 'company'}
            <div class="form-group">
                <label for="company">{l s='Company'}</label>
                <input class="form-control" type="text" id="company" name="company" value="{if isset($smarty.post.company)}{$smarty.post.company}{else}{if isset($address->company)}{$address->company|escape:'html'}{/if}{/if}" onkeyup="if (validate_{$address_validation.$field_name.validate}($(this).val())) $(this).parent().removeClass('form-error').addClass('form-ok'); else $(this).parent().addClass('form-error').removeClass('form-ok');" />
            </div>
            {/if}
            {if $field_name eq 'vat_number'}
                <div id="vat_area">
                    <div id="vat_number">
                        <div class="form-group">
                            <label for="vat_number">{l s='VAT number'}</label>
                            <input type="text" class="form-control" name="vat_number" value="{if isset($smarty.post.vat_number)}{$smarty.post.vat_number}{else}{if isset($address->vat_number)}{$address->vat_number|escape:'html'}{/if}{/if}" onkeyup="if (validate_{$address_validation.$field_name.validate}($(this).val())) $(this).parent().removeClass('form-error').addClass('form-ok'); else $(this).parent().addClass('form-error').removeClass('form-ok');" />
                        </div>
                    </div>
                </div>
            {/if}
            {if $field_name eq 'firstname'}
            <div class="required form-group">
            
                <label for="firstname">{l s='First name'} <sup>*</sup></label>
                <input class="form-control" type="text" name="firstname" id="firstname" value="{if isset($smarty.post.firstname)}{$smarty.post.firstname}{else}{if isset($address->firstname)}{$address->firstname|escape:'html'}{/if}{/if}" onkeyup="if (validate_{$address_validation.$field_name.validate}($(this).val())) $(this).parent().removeClass('form-error').addClass('form-ok'); else $(this).parent().addClass('form-error').removeClass('form-ok');" />
            </div>
    
            {/if}
            {if $field_name eq 'lastname'}
            <div class="required form-group">
                <label for="lastname">{l s='Last name'} <sup>*</sup></label>
                <input class="form-control" type="text" id="lastname" name="lastname" value="{if isset($smarty.post.lastname)}{$smarty.post.lastname}{else}{if isset($address->lastname)}{$address->lastname|escape:'html'}{/if}{/if}" onkeyup="if (validate_{$address_validation.$field_name.validate}($(this).val())) $(this).parent().removeClass('form-error').addClass('form-ok'); else $(this).parent().addClass('form-error').removeClass('form-ok');" />
            </div>
            {/if}
            {if $field_name eq 'address1'}
            <div class="required form-group">
                <label for="address1">{l s='Address'} <sup>*</sup></label>
                <input class="form-control" type="text" id="address1" name="address1" value="{if isset($smarty.post.address1)}{$smarty.post.address1}{else}{if isset($address->address1)}{$address->address1|escape:'html'}{/if}{/if}"onkeyup="if (validate_{$address_validation.$field_name.validate}($(this).val())) $(this).parent().removeClass('form-error').addClass('form-ok'); else $(this).parent().addClass('form-error').removeClass('form-ok');" />
            </div>
            {/if}
            {if $field_name eq 'address2'}
            <div class="required form-group">
                <label for="address2">{l s='Address (Line 2)'}</label>
                <input class="form-control" type="text" id="address2" name="address2" value="{if isset($smarty.post.address2)}{$smarty.post.address2}{else}{if isset($address->address2)}{$address->address2|escape:'html'}{/if}{/if}" onkeyup="if (validate_{$address_validation.$field_name.validate}($(this).val())) $(this).parent().removeClass('form-error').addClass('form-ok'); else $(this).parent().addClass('form-error').removeClass('form-ok');" />
            </div>
            {/if}
            {if $field_name eq 'postcode'}
            {assign var="postCodeExist" value="true"}
            <div class="required postcode form-group">
                <label for="postcode">{l s='Zip / Postal Code'} <sup>*</sup></label>
                <input class="form-control" type="text" id="postcode" name="postcode" value="{if isset($smarty.post.postcode)}{$smarty.post.postcode}{else}{if isset($address->postcode)}{$address->postcode|escape:'html'}{/if}{/if}" onkeyup="if (validate_{$address_validation.$field_name.validate}($(this).val())) $(this).parent().removeClass('form-error').addClass('form-ok'); else $(this).parent().addClass('form-error').removeClass('form-ok');" />
            </div>
            {/if}
            {if $field_name eq 'city'}
            <div class="required form-group">
                <label for="city">{l s='City'} <sup>*</sup></label>
                <input class="form-control" type="text" name="city" id="city" value="{if isset($smarty.post.city)}{$smarty.post.city}{else}{if isset($address->city)}{$address->city|escape:'html'}{/if}{/if}" maxlength="64" onkeyup="if (validate_{$address_validation.$field_name.validate}($(this).val())) $(this).parent().removeClass('form-error').addClass('form-ok'); else $(this).parent().addClass('form-error').removeClass('form-ok');" />
            </div>
            {*
                if customer hasn't update his layout address, country has to be verified
                but it's deprecated
            *}
            {/if}
            {if $field_name eq 'Country:name' || $field_name eq 'country'}
            <div class="required form-group">
                <label for="id_country">{l s='Country'}<sup>*</sup></label>
                <select id="id_country" class="form-control" name="id_country">{$countries_list}</select>
            </div>
            {if $vatnumber_ajax_call}
            <script type="text/javascript">
            var ajaxurl = '{$ajaxurl}';
            {literal}
                    $(document).ready(function(){
                        $('#id_country').change(function() {
                            $.ajax({
                                type: "GET",
                                url: ajaxurl+"vatnumber/ajax.php?id_country="+$('#id_country').val(),
                                success: function(isApplicable){
                                    if(isApplicable == "1")
                                    {
                                        $('#vat_area').show();
                                        $('#vat_number').show();
                                    }
                                    else
                                    {
                                        $('#vat_area').hide();
                                    }
                                }
                            });
                        });
                    });
            {/literal}
            </script>
            {/if}
            {/if}
            {if $field_name eq 'State:name'}
            {assign var="stateExist" value="true"}
            <div class="required id_state form-group">
                <label for="id_state">{l s='State'} <sup>*</sup></label>
                <select name="id_state" id="id_state" class="form-control">
                    <option value="">-</option>
                </select>
            </div>
            {/if}
            {/foreach}
            {if $postCodeExist eq "false"}
            <div class="required postcode form-group unvisible">
                <label for="postcode">{l s='Zip / Postal Code'} <sup>*</sup></label>
                <input class="form-control" type="text" id="postcode" name="postcode" value="{if isset($smarty.post.postcode)}{$smarty.post.postcode}{else}{if isset($address->postcode)}{$address->postcode|escape:'html'}{/if}{/if}" onkeyup="$('#postcode').val($('#postcode').val().toUpperCase());"
                    onkeyup="if (validate_{$address_validation.$field_name.validate}($(this).val())) $(this).parent().removeClass('form-error').addClass('form-ok'); else $(this).parent().addClass('form-error').removeClass('form-ok');" />
            </div>
            {/if}		
            {if $stateExist eq "false"}
            <div class="required id_state form-group">
                <label for="id_state">{l s='State'} <sup>*</sup></label>
                <select name="id_state" id="id_state" class="form-control">
                    <option value="">-</option>
                </select>
            </div>
            {/if}
            <div class="form-group">
                <label for="other">{l s='Additional information'}</label>
                <textarea class="form-control" id="other" name="other" cols="26" rows="3" onkeyup="if (validate_{$address_validation.other.validate}($(this).val())) $(this).parent().removeClass('form-error').addClass('form-ok'); else $(this).parent().addClass('form-error').removeClass('form-ok');
              ">{if isset($smarty.post.other)}{$smarty.post.other}{else}{if isset($address->other)}{$address->other|escape:'html'}{/if}{/if}</textarea>
            </div>
            {if isset($one_phone_at_least) && $one_phone_at_least}
                <p class="inline-infos required">{l s='You must register at least one phone number.'}</p>
            {/if}
            <div class="form-group">
                <label for="phone">{l s='Home phone'}</label>
                <input type="tel" id="phone" class="form-control" name="phone" value="{if isset($smarty.post.phone)}{$smarty.post.phone}{else}{if isset($address->phone)}{$address->phone|escape:'html'}{/if}{/if}"  onkeyup="if (validate_{$address_validation.phone.validate}($(this).val())) $(this).parent().removeClass('form-error').addClass('form-ok'); else $(this).parent().addClass('form-error').removeClass('form-ok');" />
            </div>
            <div class="{if isset($one_phone_at_least) && $one_phone_at_least}required {/if}form-group">
                <label for="phone_mobile">{l s='Mobile phone'}{if isset($one_phone_at_least) && $one_phone_at_least} <sup>*</sup>{/if}</label>
                <input type="tel" id="phone_mobile" class="form-control" name="phone_mobile" value="{if isset($smarty.post.phone_mobile)}{$smarty.post.phone_mobile}{else}{if isset($address->phone_mobile)}{$address->phone_mobile|escape:'html'}{/if}{/if}"  onkeyup="if (validate_{$address_validation.phone_mobile.validate}($(this).val())) $(this).parent().removeClass('form-error').addClass('form-ok'); else $(this).parent().addClass('form-error').removeClass('form-ok');"  />
            </div>
            <div class="required form-group" id="adress_alias">
                <label for="alias">{l s='Please assign an address title for future reference.'} <sup>*</sup></label>
                <input type="text" id="alias" class="form-control" name="alias" value="{if isset($smarty.post.alias)}{$smarty.post.alias}{else if isset($address->alias)}{$address->alias|escape:'html'}{elseif !$select_address}{l s='My address'}{/if}"  onkeyup="if (validate_{$address_validation.alias.validate}($(this).val())) $(this).parent().removeClass('form-error').addClass('form-ok'); else $(this).parent().addClass('form-error').removeClass('form-ok');" />
            </div>
        </fieldset>
        <p class="submit2">
            {if isset($id_address)}<input type="hidden" name="id_address" value="{$id_address|intval}" />{/if}
            {if isset($back)}<input type="hidden" name="back" value="{$back}" />{/if}
            {if isset($mod)}<input type="hidden" name="mod" value="{$mod}" />{/if}
            {if isset($select_address)}<input type="hidden" name="select_address" value="{$select_address|intval}" />{/if}
            <input type="hidden" name="token" value="{$token}" />		
            <button type="submit" name="submitAddress" id="submitAddress" class="btn btn-default button button-medium"><span>{l s='Save'}<i class="icon-chevron-right right"></i></span></button>
        </p>
    </form>
</div>
