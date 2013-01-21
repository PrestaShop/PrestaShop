{*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 17056 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{capture assign='page_title'}{l s='Shipping'}{/capture}
{include file='./page-title.tpl'}

<div data-role="content">
	<h3 class="bg">{l s='Choose your delivery method'}</h3>
	<fieldset data-role="controlgroup">
	{if isset($delivery_option_list)}
		{foreach $delivery_option_list as $id_address => $option_list}
			{foreach $option_list as $key => $option}
				{foreach $option.carrier_list as $carrier}
					<input type="radio" name="delivery_option[{$id_address}]" class="delivery_option_radio" id="opc_carrier_{$carrier.instance->id}" value="{$carrier.instance->id}" {if isset($delivery_option[$id_address]) && $delivery_option[$id_address] == $key}checked="checked"{/if} />
					<label for="opc_carrier_{$carrier.instance->id}">{$carrier.instance->name}</label>
				{/foreach}
			{/foreach}
		{/foreach}
	{/if}
	</fieldset>
	<fieldset data-role="fieldcontain">
		<input type="checkbox" name="same" id="recyclable" value="1" class="delivery_option_radio" {if $recyclable == 1}checked="checked"{/if} />
		<label for="recyclable">{l s='I agree to receive my order in recycled packaging'}.</label>
	</fieldset>

	<h3 class="bg">{l s='Gift'}</h3>
	<fieldset data-role="fieldcontain">
		<input type="checkbox" id="gift" name="gift" value="1" class="delivery_option_radio" {if $cart->gift == 1}checked="checked"{/if} />
		<label for="gift">{l s='I would like my order to be gift-wrapped.'}</label>
	</fieldset>
	<p class="textarea" id="gift_div" style="display: none;">
		<label for="gift_message">{l s='If you wish, you can add a note to the gift:'}</label>
		<textarea name="gift_message" id="gift_message" cols="35" rows="5">{$cart->gift_message|escape:'htmlall':'UTF-8'}</textarea>
	</p>
	
	<h3 class="bg">{l s='Terms of service'}</h3>
	<fieldset data-role="fieldcontain">
		<input type="checkbox" value="1" id="cgv" name="cgv" {if $checkedTOS}checked="checked"{/if} />
		<label for="cgv">{l s='I agree to the Terms of Service and will adhere to them unconditionally.'}</label>
	</fieldset>
	<p class="lnk_CGV"><a href="{$link_conditions}" data-ajax="false">{l s='(Read Terms of Service)'}</a></p>
</div>
