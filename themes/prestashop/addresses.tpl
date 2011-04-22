{*
* 2007-2011 PrestaShop 
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<script type="text/javascript">
<!--
	var baseDir = '{$base_dir_ssl}';
-->
</script>

{capture name=path}<a href="{$link->getPageLink('my-account.php', true)}">{l s='My account'}</a><span class="navigation-pipe">{$navigationPipe}</span>{l s='My addresses'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

<h1>{l s='My addresses'}</h1>
<p>{l s='Please configure the desired billing and delivery addresses to be preselected when placing an order. You may also add additional addresses, useful for sending gifts or receiving your order at the office.'}</p>

{if $ordered_fields}
<div class="addresses">
	<h3>{l s='Your addresses are listed below.'}</h3>
	<p>{l s='Be sure to update them if they have changed.'}</p>
	{assign var="adrs_style" value=$addresses_style}
	{foreach from=$addresses item=address name=myLoop}
	<ul class="address {if $smarty.foreach.myLoop.last}last_item{elseif $smarty.foreach.myLoop.first}first_item{/if} {if $smarty.foreach.myLoop.index % 2}alternate_item{else}item{/if}">
		<li class="address_title">{$address.alias}</li>
		{foreach from=$ordered_fields name=adr_loop item=field_item}
		{if $field_item eq "company" && $address.company}<li class="address_company">{$address.company}</li>
		{elseif $field_item eq "vat_number" && $address.vat_number}<li class="address_company">{$address.vat_number}</li>
		{elseif $field_item eq "address2" && $address.address2}<li class="address_address2">{$address.address2}</li>
		{elseif $field_item eq "phone" && $address.phone}<li class="address_phone">{$address.phone}</li>
		{elseif $field_item eq "phone_mobile" && $address.phone_mobile}<li class="address_phone_mobile">{$address.phone_mobile}</li>
		{else}
			{assign var=address_words value=" "|explode:$field_item} 
			<li>{foreach from=$address_words item=word_item name="word_loop"}{if !$smarty.foreach.word_loop.first} {/if}<span class="address_{$word_item}">{$address.$word_item|escape:'htmlall':'UTF-8'}</span>{/foreach}</li>
		{/if}
		{/foreach}
		<li class="address_update"><a href="{$link->getPageLink('address.php', true)}?id_address={$address.id_address|intval}" title="{l s='Update'}">{l s='Update'}</a></li>
		<li class="address_delete"><a href="{$link->getPageLink('address.php', true)}?id_address={$address.id_address|intval}&amp;delete" onclick="return confirm('{l s='Are you sure?'}');" title="{l s='Delete'}">{l s='Delete'}</a></li>
	</ul>
	{/foreach}
	<p class="clear" />
</div>
{else}
	<p class="warning">{l s='No addresses available.'}&nbsp;<a href="{$link->getPageLink('address.php', true)}">{l s='Add new address'}</a></p>
{/if}

<div class="clear address_add"><a href="{$link->getPageLink('address.php', true)}" title="{l s='Add an address'}" class="button_large">{l s='Add an address'}</a></div>

<ul class="footer_links">
	<li><a href="{$link->getPageLink('my-account.php', true)}"><img src="{$img_dir}icon/my-account.gif" alt="" class="icon" /></a><a href="{$link->getPageLink('my-account.php', true)}">{l s='Back to Your Account'}</a></li>
	<li><a href="{$base_dir}"><img src="{$img_dir}icon/home.gif" alt="" class="icon" /></a><a href="{$base_dir}">{l s='Home'}</a></li>
</ul>
