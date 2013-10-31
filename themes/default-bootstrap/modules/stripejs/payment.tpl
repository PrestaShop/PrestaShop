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
*  @version  Release: $Revision: 6664 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<script type="text/javascript" src="{$module_dir}stripe-prestashop.js"></script>
<div class="payment_module box"{if $stripe_ps_version < '1.5'}style="border: 1px solid #595A5E; padding: 0.6em; margin-left: 0.7em;"{/if}>
	<h1 class="page-heading bottom-indent">{l s='Pay by credit card with our secured payment server' mod='stripejs'}</h1>
	{* This form will be displayed only if a previous credit card was saved *}
	{if isset($stripe_save_tokens_ask) && $stripe_save_tokens_ask && isset($stripe_credit_card)}
	<form action="{$module_dir}validation.php" method="POST" id="stripe-payment-form-cc">
		<p>{l s='Pay with my saved Credit card (ending in' mod='stripejs'} {$stripe_credit_card|escape:html:'UTF-8'}{l s=')' mod='stripejs'}
		<input type="hidden" name="stripe_save_token" value="1" />
		<input type="hidden" name="stripeToken" value="0" />
		<button type="submit" class="stripe-submit-button-cc btn btn-default button-small"><span>{l s='Submit Payment' mod='stripejs'}<i class="icon-chevron-right right"></i></span></button></p>
		<p><a id="stripe-replace-card">{l s='Replace this card with a new one' mod='stripejs'}</a> | <a id="stripe-delete-card" onclick="return confirm('{l s='Do you really want to delete this card?' mod='stripejs'}');">{l s='Delete this card' mod='stripejs'}</a></p>
	</form>
	{/if}
	{* Classic Credit card form *}
	<div id="stripe-ajax-loader"><img src="{$module_dir}img/ajax-loader.gif" alt="" /> {l s='Transaction in progress, please wait.' mod='stripejs'}</div>
	<form action="{$module_dir}validation.php" method="POST" id="stripe-payment-form"{if isset($stripe_save_tokens_ask) && $stripe_save_tokens_ask && isset($stripe_credit_card)} style="display: none;"{/if}>
		<div class="stripe-payment-errors">{if isset($smarty.get.stripe_error)}{$smarty.get.stripe_error|base64_decode|escape:html:'UTF-8'}{/if}</div><a name="stripe_error" style="display:none"></a>
		<div class="stripe-card-deleted"></div>
        <div class="form-group">
            <label>{l s='Card Number' mod='stripejs'}</label>
            <input type="text" size="20" autocomplete="off" class="stripe-card-number form-control" />
        </div>
		<div class="form-group clearfix">
        	<div class="clearfix">
				<label>{l s='Card Type' mod='stripejs'}</label>
            </div>
			<img class="cc-icon disable" rel="Visa" alt="" src="{$module_dir}img/cc-visa.png" />
			<img class="cc-icon disable" rel="MasterCard" alt="" src="{$module_dir}img/cc-mastercard.png" />
			<img class="cc-icon disable" rel="Discover" alt="" src="{$module_dir}img/cc-discover.png" />
			<img class="cc-icon disable" rel="American Express" alt="" src="{$module_dir}img/cc-amex.png" />
			<img class="cc-icon disable" rel="JCB" alt="" src="{$module_dir}img/cc-jcb.png" />
			<img class="cc-icon disable" rel="Diners Club" alt="" src="{$module_dir}img/cc-diners.png" />
		</div>
        <div class="clearfix stripe-line">
            <div class="block-left form-group">
                <label>{l s='CVC' mod='stripejs'}</label><br/>
                <input type="text" size="4" autocomplete="off" class="stripe-card-cvc form-control" />
                <a href="javascript:void(0)" class="stripe-card-cvc-info" style="border: none;">
                    {l s='What\'s this?' mod='stripejs'}
                    <div class="cvc-info">
                    {l s='The CVC (Card Validation Code) is a 3 or 4 digit code on the reverse side of Visa, MasterCard and Discover cards and on the front of American Express cards.' mod='stripejs'}
                    </div>
                </a>
            </div>
            <div class="form-group">
            <label>{l s='Expiration (MM/YYYY)' mod='stripejs'}</label><br />
                <select id="month" name="month" class="stripe-card-expiry-month form-control">
                    <option value="01">{l s='January' mod='stripejs'}</option>
                    <option value="02">{l s='February' mod='stripejs'}</option>
                    <option value="03">{l s='March' mod='stripejs'}</option>
                    <option value="04">{l s='April' mod='stripejs'}</option>
                    <option value="05">{l s='May' mod='stripejs'}</option>
                    <option value="06">{l s='June' mod='stripejs'}</option>
                    <option value="07">{l s='July' mod='stripejs'}</option>
                    <option value="08">{l s='August' mod='stripejs'}</option>
                    <option value="09">{l s='September' mod='stripejs'}</option>
                    <option value="10">{l s='October' mod='stripejs'}</option>
                    <option value="11">{l s='November' mod='stripejs'}</option>
                    <option value="12">{l s='December' mod='stripejs'}</option>
                </select>
                <select id="year" name="year" class="stripe-card-expiry-year form-control">
                    <option value="2013">2013</option>
                    <option value="2014">2014</option>
                    <option value="2015">2015</option>
                    <option value="2016">2016</option>
                    <option value="2017">2017</option>
                    <option value="2018">2018</option>
                    <option value="2019">2019</option>
                    <option value="2020">2020</option>
                </select>
            </div>
        </div>
		{if isset($stripe_save_tokens_ask)}
        <div class="ckeckbox">
			<input type="checkbox" name="stripe_save_token" id="stripe_save_token" value="1" />
			<label class="lowercase" for="stripe_save_token">{l s='Store this credit card info for later use' mod='stripejs'}</label>
        </div>
		{/if}
		<button type="submit" class="stripe-submit-button btn-default btn button button-small"><span>{l s='Submit Payment' mod='stripejs'}<i class="icon-chevron-right right"></i></span></button>
	</form>
	<div id="stripe-translations">
		<span id="stripe-wrong-cvc">{l s='Wrong CVC.' mod='stripejs'}</span>
		<span id="stripe-wrong-expiry">{l s='Wrong Credit Card Expiry date.' mod='stripejs'}</span>
		<span id="stripe-wrong-card">{l s='Wrong Credit Card number.' mod='stripejs'}</span>
		<span id="stripe-please-fix">{l s='Please fix it and submit your payment again.' mod='stripejs'}</span>
		<span id="stripe-card-del">{l s='Your Credit Card has been successfully deleted, please enter a new Credit Card:' mod='stripejs'}</span>
		<span id="stripe-card-del-error">{l s='An error occured while trying to delete this Credit card. Please contact us.' mod='stripejs'}</span>
	</div>
</div>
