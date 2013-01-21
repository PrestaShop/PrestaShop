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
*  @version  Release: $Revision: 6594 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{capture assign='page_title'}{l s='Contact'}{/capture}
{include file='./page-title.tpl'}

	<div data-role="content" id="content">
		<p class="bold">{l s='For questions about an order or for more information about our products'}.</p>
		{include file="./errors.tpl"}
		<form action="{$request_uri|escape:'htmlall':'UTF-8'}" method="post" class="std" enctype="multipart/form-data">
			{if isset($customerThread.id_contact)}
				{foreach from=$contacts item=contact}
					{if $contact.id_contact == $customerThread.id_contact}
						<input type="text" id="contact_name" name="contact_name" value="{$contact.name|escape:'htmlall':'UTF-8'}" readonly="readonly" />
						<input type="hidden" name="id_contact" value="{$contact.id_contact}" />
					{/if}
				{/foreach}
			{else}
				<select id="id_contact" name="id_contact" onchange="showElemFromSelect('id_contact', 'desc_contact')">
					<option value="0">-- {l s='Subject Heading'} --</option>
				{foreach from=$contacts item=contact}
					<option value="{$contact.id_contact|intval}" {if isset($smarty.post.id_contact) && $smarty.post.id_contact == $contact.id_contact}selected="selected"{/if}>{$contact.name|escape:'htmlall':'UTF-8'}</option>
				{/foreach}
				</select>
			</p>
			<p id="desc_contact0" class="desc_contact">&nbsp;</p>
				{foreach from=$contacts item=contact}
					<p id="desc_contact{$contact.id_contact|intval}" class="desc_contact" style="display:none;">
						<label>&nbsp;</label>{$contact.description|escape:'htmlall':'UTF-8'}
					</p>
				{/foreach}
			{/if}
			
			<fieldset>
				{if isset($customerThread.email)}
					<input class="ui-input-text ui-body-c ui-corner-all ui-shadow-inset" type="email" id="email" name="from" value="{$customerThread.email}" placeholder="{l s='E-mail address'}" readonly="readonly" />
				{else}
					<input class="ui-input-text ui-body-c ui-corner-all ui-shadow-inset" type="email" id="email" name="from" value="{$email}" placeholder="{l s='E-mail address'}"/>
				{/if}
			</fieldset>
			
			{if !$PS_CATALOG_MODE}
				{if (!isset($customerThread.id_order) || $customerThread.id_order > 0)}
				<fieldset>
					{if !isset($customerThread.id_order) && isset($isLogged) && $isLogged == 1}
						<select name="id_order" ><option value="0">-- {l s='Order ID'} --</option>{$orderList}</select>
					{elseif !isset($customerThread.id_order) && !isset($isLogged)}
						<input type="text" placeholder="{l s='Order ID'}" name="id_order" id="id_order" value="{if isset($customerThread.id_order) && $customerThread.id_order > 0}{$customerThread.id_order|intval}{else}{if isset($smarty.post.id_order)}{$smarty.post.id_order|intval}{/if}{/if}" />
					{elseif $customerThread.id_order > 0}
						<input type="text" placeholder="{l s='Order ID'}" name="id_order" id="id_order" value="{$customerThread.id_order|intval}" readonly="readonly" />
					{/if}
				</fieldset>
				{/if}
				{if isset($isLogged) && $isLogged}
				<fieldset>
					{if !isset($customerThread.id_product)}
						<select name="id_product" style="width:300px;"><option value="0">-- {l s='Product'} --</option>{$orderedProductList}</select>
					{elseif $customerThread.id_product > 0}
						<input type="text" name="id_product" id="id_product" value="{$customerThread.id_product|intval}" readonly="readonly" />
					{/if}
				</fieldset>
				{/if}
			{/if}
			
			<fieldset>
				<textarea id="message" name="message" placeholder="{l s='Your message'}" rows="15" cols="10">{if isset($message) && $message != ''}{$message|escape:'htmlall':'UTF-8'|stripslashes}{/if}</textarea>
			</fieldset>
			
			<fieldset>
				<button class="ui-btn-hidden" type="submit" aria-disabled="false" data-theme="a" name="submitMessage" id="submitMessage">Envoyer</button>
			</fieldset>
		</form> 
		
		{include file='./sitemap.tpl'}
	</div><!-- /content -->