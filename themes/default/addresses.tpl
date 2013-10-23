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

{*
** Retro compatibility for PrestaShop version < 1.4.2.5 with a recent theme
*}

{* Two variable are necessaries to display the address with the new layout system *}
{* Will be deleted for 1.5 version and more *}
{if !isset($multipleAddresses)}
	{$ignoreList.0 = "id_address"}
	{$ignoreList.1 = "id_country"}
	{$ignoreList.2 = "id_state"}
	{$ignoreList.3 = "id_customer"}
	{$ignoreList.4 = "id_manufacturer"}
	{$ignoreList.5 = "id_supplier"}
	{$ignoreList.6 = "date_add"}
	{$ignoreList.7 = "date_upd"}
	{$ignoreList.8 = "active"}
	{$ignoreList.9 = "deleted"}

	{* PrestaShop < 1.4.2 compatibility *}
	{if isset($addresses)}
		{$address_number = 0}
		{foreach from=$addresses key=k item=address}
			{counter start=0 skip=1 assign=address_key_number}
			{foreach from=$address key=address_key item=address_content}
				{if !in_array($address_key, $ignoreList)}
					{$multipleAddresses.$address_number.ordered.$address_key_number = $address_key}
					{$multipleAddresses.$address_number.formated.$address_key = $address_content}
					{counter}
				{/if}
			{/foreach}
		{$multipleAddresses.$address_number.object = $address}
		{$address_number = $address_number  + 1}
		{/foreach}
	{/if}
{/if}

{* Define the style if it doesn't exist in the PrestaShop version*}
{* Will be deleted for 1.5 version and more *}
{if !isset($addresses_style)}
	{$addresses_style.company = 'address_company'}
	{$addresses_style.vat_number = 'address_company'}
	{$addresses_style.firstname = 'address_name'}
	{$addresses_style.lastname = 'address_name'}
	{$addresses_style.address1 = 'address_address1'}
	{$addresses_style.address2 = 'address_address2'}
	{$addresses_style.city = 'address_city'}
	{$addresses_style.country = 'address_country'}
	{$addresses_style.phone = 'address_phone'}
	{$addresses_style.phone_mobile = 'address_phone_mobile'}
	{$addresses_style.alias = 'address_title'}
{/if}

<script type="text/javascript">
//<![CDATA[
	{literal}
	$(document).ready(function()
	{
			resizeAddressesBox();
	});
	{/literal}
//]]>
</script>

{capture name=path}<a href="{$link->getPageLink('my-account', true)|escape:'html'}">{l s='My account'}</a><span class="navigation-pipe">{$navigationPipe}</span><span class="navigation_page">{l s='My addresses'}</span>{/capture}

<h1 class="page-heading">{l s='My addresses'}</h1>
<p>{l s='Please configure your default billing and delivery addresses when placing an order. You may also add additional addresses, which can be useful for sending gifts or receiving an order at your office.'}</p>

{if isset($multipleAddresses) && $multipleAddresses}
<div class="addresses">
	<p><strong class="dark">{l s='Your addresses are listed below.'}</strong></p>
	<p class="p-indent">{l s='Be sure to update your personal information if it has changed.'}</p>
	{assign var="adrs_style" value=$addresses_style}
	<div class="bloc_adresses row">
	{foreach from=$multipleAddresses item=address name=myLoop}
    	<div class="col-xs-12 col-sm-6 address">
			<ul class="{if $smarty.foreach.myLoop.last}last_item{elseif $smarty.foreach.myLoop.first}first_item{/if} {if $smarty.foreach.myLoop.index % 2}alternate_item{else}item{/if} box">
                <li><h3 class="page-subheading">{$address.object.alias}</h3></li>
                {foreach from=$address.ordered name=adr_loop item=pattern}
                    {assign var=addressKey value=" "|explode:$pattern}
                    <li>
                    {foreach from=$addressKey item=key name="word_loop"}
                        <span {if isset($addresses_style[$key])} class="{$addresses_style[$key]}"{/if}>
                            {$address.formated[$key|replace:',':'']|escape:'htmlall':'UTF-8'}
                        </span>
                    {/foreach}
                    </li>
                {/foreach}
                <li class="address_update">
                <a class="btn btn-default button button-small" href="{$link->getPageLink('address', true, null, "id_address={$address.object.id|intval}")|escape:'html'}" title="{l s='Update'}"><span>{l s='Update'}<i class="icon-chevron-right right"></i></span></a>
                <a class="btn btn-default button button-small" href="{$link->getPageLink('address', true, null, "id_address={$address.object.id|intval}&delete")|escape:'html'}" onclick="return confirm('{l s='Are you sure?' js=1}');" title="{l s='Delete'}"><span>{l s='Delete'}<i class="icon-remove right"></i></span></a></li>
            </ul>
        </div>
	{/foreach}
	</div>
</div>
{else}
	<p class="alert alert-warning">{l s='No addresses are available.'}&nbsp;<a href="{$link->getPageLink('address', true)|escape:'html'}">{l s='Add a new address'}</a></p>
{/if}

<div class="clearfix main-page-indent">
	<a href="{$link->getPageLink('address', true)|escape:'html'}" title="{l s='Add an address'}" class="btn btn-default button button-medium"><span>{l s='Add an address'}<i class="icon-chevron-right right"></i></span></a>
</div>

<ul class="footer_links clearfix">
	<li><a class="btn btn-defaul button button-small" href="{$link->getPageLink('my-account', true)|escape:'html'}"><span><i class="icon-chevron-left"></i> {l s='Back to your account'}</span></a></li>
	<li><a class="btn btn-defaul button button-small" href="{$base_dir}"><span><i class="icon-chevron-left"></i> {l s='Home'}</span></a></li>
</ul>
