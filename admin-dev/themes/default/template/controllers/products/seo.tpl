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
*  @version  Release: $Revision: 11204 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<input type="hidden" name="submitted_tabs[]" value="Seo" />
<h4>{l s='SEO'}</h4>

{include file="controllers/products/multishop/check_fields.tpl" product_tab="Seo"}

<div class="separation"></div>

<table>
	<tr>
		<td class="col-left">
			{include file="controllers/products/multishop/checkbox.tpl" field="meta_title" type="default" multilang="true"}
			<label>{l s='Meta title:'}</label>
		</td>
		<td>
			{include file="controllers/products/input_text_lang.tpl"
				languages=$languages
				input_name='meta_title'
				input_value=$product->meta_title}
			<p class="preference_description">{l s='Product page title; leave blank to use product name'}</p>
		</td>
	</tr>
	<tr>
		<td class="col-left">
			{include file="controllers/products/multishop/checkbox.tpl" field="meta_description" type="default" multilang="true"}
			<label>{l s='Meta description:'}</label>
		</td>
		<td>
			{include file="controllers/products/input_text_lang.tpl"
				languages=$languages
				input_name='meta_description'
				input_value=$product->meta_description
				input_hint='{l s=\'Forbidden characters:\'\} <>;=#{\}'}
			<p class="preference_description">{l s='A single sentence for HTML header'}</p>
		</td>
	</tr>
	<tr>
		<td class="col-left">
			{include file="controllers/products/multishop/checkbox.tpl" field="meta_keywords" type="default" multilang="true"}
			<label>{l s='Meta keywords:'}</label>
		</td>
		<td>
			{include file="controllers/products/input_text_lang.tpl" languages=$languages
				input_value=$product->meta_keywords
				input_name='meta_keywords'}
			<p class="preference_description">{l s='Keywords for HTML header, separated by commas'}</p>
		</td>
	</tr>
	<tr>
		<td class="col-left">
			{include file="controllers/products/multishop/checkbox.tpl" field="link_rewrite" type="default" multilang="true"}
			<label>{l s='Friendly URL:'}</label>
		</td>
		<td>
			{include file="controllers/products/input_text_lang.tpl"
				languages=$languages
				input_value=$product->link_rewrite
				input_name='link_rewrite'}
			
			<p class="clear" style="padding:10px 0 0 0">
			<a style="cursor:pointer" class="button"
			onmousedown="updateFriendlyURLByName();">{l s='Generate'}</a>&nbsp;
			{l s='Friendly URL from product name.'}<br /><br />
			{l s='Product link will look like this:'}
			{$curent_shop_url|escape:'htmlall':'UTF-8'}lang/{if isset($product->id)}{$product->id}{else}<b>id_product</b>{/if}-<span id="friendly-url">{$product->link_rewrite[$default_language]}</span>.html</p>
		</td>
	</tr>
</table>
