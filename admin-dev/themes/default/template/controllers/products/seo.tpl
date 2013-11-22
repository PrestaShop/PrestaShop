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
<div class="panel">
	<input type="hidden" name="submitted_tabs[]" value="Seo" />
	<h3>{l s='SEO'}</h3>

	{include file="controllers/products/multishop/check_fields.tpl" product_tab="Seo"}

	<div class="form-group">
		<label class="control-label col-lg-3" for="meta_title_{$id_lang}">
			{include file="controllers/products/multishop/checkbox.tpl" field="meta_title" type="default" multilang="true"}
			<span class="label-tooltip" data-toggle="tooltip"
				title="{l s='Product page title: Leave blank to use the product name'}">
				{l s='Meta title:'}
			</span>
		</label>
		<div class="col-lg-8">
			{include file="controllers/products/input_text_lang.tpl"
				languages=$languages
				input_name='meta_title'
				input_value=$product->meta_title
				maxchar=70
			}
		</div>
	</div>

	<div class="form-group">		
		<label class="control-label col-lg-3" for="meta_description_{$id_lang}">
			{include file="controllers/products/multishop/checkbox.tpl" field="meta_description" type="default" multilang="true"}
			<span class="label-tooltip" data-toggle="tooltip"
				title="{l s='A single sentence for the HTML header is needed. '}">
				{l s='Meta description:'}
			</span>
		</label>
		<div class="col-lg-8">
			{include file="controllers/products/input_text_lang.tpl"
				languages=$languages
				input_name='meta_description'
				input_value=$product->meta_description
				maxchar=160
			}
		</div>
	</div>

	<div class="form-group">
		<label class="control-label col-lg-3" for="meta_keywords_{$id_lang}">
			{include file="controllers/products/multishop/checkbox.tpl" field="meta_keywords" type="default" multilang="true"}
			<span class="label-tooltip" data-toggle="tooltip"
				title="{l s='Keywords for HTML header, separated by commas.'}">
				{l s='Meta keywords:'}
			</span>
		</label>
		<div class="col-lg-8">
			{include file="controllers/products/input_text_lang.tpl" languages=$languages
				input_value=$product->meta_keywords
				input_name='meta_keywords'}
		</div>
	</div>

	<div class="form-group">
		<label class="control-label col-lg-3" for="link_rewrite_{$id_lang}">
			{include file="controllers/products/multishop/checkbox.tpl" field="link_rewrite" type="seo_friendly_url" multilang="true"}
			<span class="label-tooltip" data-toggle="tooltip"
				title="{l s='friendly URL from the product name.'}">
				{l s='Friendly URL:'}
			</span>

		</label>
		<div class="col-lg-6">
				{include file="controllers/products/input_text_lang.tpl"
					languages=$languages
					input_value=$product->link_rewrite
					input_name='link_rewrite'}
		</div>
		<div class="col-lg-2">
			<button type="button" class="btn btn-default" id="generate-friendly-url" onmousedown="updateFriendlyURLByName();"><i class="icon-random"></i> {l s='Generate'}</button>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-9 col-lg-offset-3">
			{foreach from=$languages item=language}
			<div class="alert alert-warning translatable-field lang-{$language.id_lang}">
				<i class="icon-link"></i> {l s='The product link will look like this:'}<br/>
				<strong>{$curent_shop_url|escape:'html':'UTF-8'}lang/{if isset($product->id)}{$product->id}{else}id_product{/if}-<span id="friendly-url_{$language.id_lang}">{$product->link_rewrite[$default_language]}</span>.html</strong>
			</div>
			{/foreach}
		</div>
	</div>
</div>
<script type="text/javascript">
	hideOtherLanguage({$id_lang});
</script>