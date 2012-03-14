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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{extends file="helpers/form/form.tpl"}

{block name="autoload_tinyMCE"}
	// Execute when tab Informations has finished loading
	onTabLoad('Informations', function(){
		// change each by click to load only on click
		$(".autoload_rte").each(function(e){
			tinySetup({
				mode :"exact",
				editor_selector :"autoload_rte",
				elements : $(this).attr("id"),
				theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
				theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,,|,forecolor,backcolor",
				theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,media,|,ltr,rtl,|,fullscreen",
				theme_advanced_buttons4 : "styleprops,|,cite,abbr,acronym,del,ins,attribs,pagebreak",
				setup : function(ed) {

					{* Count the total number of the field *}

					ed.onKeyUp.add(function(ed, e) {
						tinyMCE.triggerSave();
						textarea = $('#'+ed.id);
						max = textarea.parent('div').find('span.counter').attr('max');
						if (max != 'none')
						{
							textarea_value = textarea.val();
							count = stripHTML(textarea_value).length;
							rest = max - count;
							if (rest < 0)
								textarea.parent('div').find('span.counter').html('<span style="color:red;">{l s='Maximum'} '+max+' {l s='characters'} : '+rest+'</span>');
							else
								textarea.parent('div').find('span.counter').html(' ');
						}
					});
				}
			});
		});
	});
{/block}

{block name="defaultForm"}
	<div>
		<div class="productTabs" style="display:none;">
			<ul class="tab">
			{foreach $product_tabs key=numStep item=tab}
				<li class="tab-row">
					<a class="tab-page {if $tab.selected}selected{/if}" id="link-{$tab.id}" href="{$tab.href}&amp;updateproduct">{$tab.name}</a>{*todo href when nojs*}
				</li>
			{/foreach}
			</ul>
		</div>
	</div>
	<script type="text/javascript">
		var token = '{$token}';
		var id_product = {if isset($product->id)}{$product->id}{else}0{/if};
		var defaultLanguage = {$defaultLanguage};

		var product_type_pack = {Product::PTYPE_PACK};
		var product_type_virtual = {Product::PTYPE_VIRTUAL};
		var product_type_simple = {Product::PTYPE_SIMPLE};

		var toload = new Array();
		var empty_pack_msg = '{l s='Pack is empty. You need to add at least one product to the pack before you can save the pack.' slashes=1}';
		var empty_name_msg = '{l s='Product name is empty. You need to enter a name at least for the default language before you can save the product.' slashes=1}';
		var empty_link_rewrite_msg = '{l s='Friendly URL is empty. You need to enter a friendly URL at least for the default language before you can save the product.' slashes=1}';
		$('#product-tab-content-wait').show();
		var post_data = {$post_data};

		var product_type;
		$(document).ready(function()
		{
			product_type = $("input[name=type_product]:checked").val();
			$('#product-tab-content-wait').show();
			{if $is_pack}
				$('#pack_product').attr('checked', 'checked');
				$('li.tab-row a[id*="VirtualProduct"]').hide();
			{elseif $product->is_virtual}
				$('#virtual_product').attr('checked', 'checked');
				$('li.tab-row a[id*="Pack"]').hide();
				$('li.tab-row a[id*="Shipping"]').hide();
				$('#condition').attr('disabled', 'disabled');
				$('#condition option[value=new]').attr('selected', 'selected');
			{else}
				$('#simple_product').attr('checked', 'checked');
				$('li.tab-row a[id*="Pack"]').hide();
				$('li.tab-row a[id*="VirtualProduct"]').hide();
			{/if}

			$('input[name="type_product"]').click(function()
			{
				// Reset settings
				$('li.tab-row a[id*="Pack"]').hide();
				$('li.tab-row a[id*="VirtualProduct"]').hide();
				$('div.ppack').hide();
				$('#is_virtual_good').removeAttr('checked');
				$('div.is_virtual_good').hide();
				$('#is_virtual').val(0);
				$("#virtual_good_attributes").hide();
				
				// product_type is now global
				product_type = $(this).val();

				// until a product is added in the pack
				// if product is PTYPE_PACK, save buttons will be disabled 
				if (product_type == {Product::PTYPE_PACK})
				{
					//when you change the type of the product, directly go to the pack tab
					$('li.tab-row a[id*="Pack"]').show().click();
					$('#ppack').val(1).attr('checked', true).attr('disabled', 'disabled');
					$('#ppackdiv').show();
					// If the pack tab has not finished loaded the changes will be made when the loading event is triggered
					$("#product-tab-content-Pack").bind('loaded', function(){
						$('#ppack').val(1).attr('checked', true).attr('disabled', 'disabled');
						$('#ppackdiv').show();
					});
					$("#product-tab-content-Quantities").bind('loaded', function(){
						$('.stockForVirtualProduct').show();
					});

					$('li.tab-row a[id*="Shipping"]').show();
					$('#condition').removeAttr('disabled');
					$('#condition option[value=new]').removeAttr('selected');
					$('.stockForVirtualProduct').show();
					// if pack is enabled, if you choose pack, automatically switch to pack page
				}
				else if (product_type == {Product::PTYPE_VIRTUAL})
				{
					$('li.tab-row a[id*="VirtualProduct"]').show();
					$('#is_virtual_good').attr('checked', true);
					$('#virtual_good').show();
					$('#is_virtual').val(1);
					$("#virtual_good_attributes").show();
					// If the virtual product tab has not finished loaded the changes will be made when the loading event is triggered
					$("#product-tab-content-VirtualProduct").bind('loaded', function(){
						$('#virtual_good').show();
						$('#is_virtual').val(1);
						$("#virtual_good_attributes").show();
					});
					$("#product-tab-content-Quantities").bind('loaded', function(){
						$('.stockForVirtualProduct').hide();
					});
					$('li.tab-row a[id*="Shipping"]').hide();
					$('#condition').attr('disabled', 'disabled');
					$('#condition option[value=refurbished]').removeAttr('selected');
					$('#condition option[value=used]').removeAttr('selected');
					$('.stockForVirtualProduct').hide();
				}
				else
				{
					// 3rd case : product_type is PTYPE_SIMPLE (0)
					$('li.tab-row a[id*="Shipping"]').show();
					$('#condition').removeAttr('disabled');
					$('#condition option[value=new]').removeAttr('selected');
					$('.stockForVirtualProduct').show();
				}
				// this handle the save button displays and warnings
				handleSaveButtons();

			});

			$('#desc-product-newCombination').hide();

			{* submenu binding *}
			$(".tab-page").click(function(e){
				e.preventDefault();

				// currentId is the current product tab id
				currentId = $(".productTabs a.selected").attr('id').substr(5);
				// id is the wanted producttab id
				id = $(this).attr('id').substr(5);

				// Update submit button value
				var split_position = id.indexOf('-') + 1;
				var btn_name = id.substr(split_position);

				$('#key_tab').val(btn_name);

				if ($(this).attr("id") != $(".productTabs a.selected").attr('id'))
				{
					$(".tab-page").removeClass('selected');
					$("#product-tab-content-"+currentId).hide();
				}

				if ($("#product-tab-content-"+id).hasClass('not-loaded') && !$("#product-tab-content-"+id).hasClass('loading'))
					displayTabProductById(id, true, 0, null);
				else
				{
					$("#product-tab-content-"+id).show(0, function(){
						$(this).trigger('displayed');
					});
					$("#link-"+id).addClass('selected');
				}

				var languages = new Array();
				if (btn_name == "Combinations")
				{
					$('#desc-product-new').hide();
					$('#desc-product-newCombination').show();
					populate_attrs();
				}
				else if (btn_name == "Attachments")
				{
					handleSaveButtons();
				}
				else
				{
					$('#desc-product-newCombination').hide();
					// if pack is enabled, save button are visible only if pack is valid
					handleSaveButtons();
				}
			});

			$(".productTabs a.selected").click();

			// disable save if Associations tab is not loaded
			if ($('#product-tab-content-Associations').hasClass('not-loaded'))
				disableSave();

			$('#product-tab-content-Associations').bind('loaded', function()
			{
				handleSaveButtons();
			});

			$('.confirm_leave').live('click', function(){
				// Double quotes are necessary when the translated string has single quotes
				return confirm("{l s='You will lose all unsaved modifications, do you want to proceed?' js=1}");
			});

		});

		var tabs_preloaded = new Array();

		// Listen to the load event that is fired each time an ajax call to load a tab has completed
		$(window).bind("load", function() {
			{* Fill an array with tabs that need to be preloaded *}
			var tabs_to_preload = new Array();
			{foreach $tabs_preloaded as $tab_name => $value}
				{* If the tab was not given a loading priority number it will not be preloaded *}
				{if (is_numeric($value))}
					if ($("#product-tab-content-"+'{$tab_name}').hasClass('not-loaded'))
						tabs_to_preload.push('{$tab_name}');
				{/if}
			{/foreach}

			// Recursively load tabs starting with the first element of stack
			displayTabProductById(tabs_to_preload[0], false, 0, tabs_to_preload);

			$('.productTabs').show();
			$('#product_form').show();
			$('#product-tab-content-wait').hide();
		});
	</script>
	
	{***********************************************}
	{********** TO CHECK !!!!!!!!!!!!!!! ***********}
	<script type="text/javascript">
		// <![CDATA[
			ThickboxI18nImage = "{l s='Image'}";
			ThickboxI18nOf = "{l s='of'}";
			ThickboxI18nClose = "{l s='Close'}";
			ThickboxI18nOrEscKey = "{l s='(or \\"Esc\\")'}";
			ThickboxI18nNext = "{l s='Next >'}";
			ThickboxI18nPrev = "{l s='< Previous'}";
			tb_pathToImage = "../img/loadingAnimation.gif";
		//]]>
	</script>

	<div id="product-tab-content-wait" style="display:none"><div id="loading">{l s='Loading...'}</div></div>

	<form id="product_form" action="{$form_action}" method="post" enctype="multipart/form-data" name="product" style="display:none;">
		<input type="hidden" name="id_product" value="{$id_product}" />
		<div class="tab-pane" id="tabPane1">
		{if !$product->active}
			<div class="warn draft" >
				<p>
					<span style="float: left">
					{l s='Your product will be saved as a draft'}</span>
					<span style="float:right"><a href="#" class="button" style="display: block" onclick="submitAddProductAndPreview()" >{l s='Save and preview'}</a></span>
					<input type="hidden" name="fakeSubmitAddProductAndPreview" id="fakeSubmitAddProductAndPreview" />
					<br />
				</p>
				</div>
		{/if}
		{* all input are here *}
			{foreach $product_tabs key=numStep item=tab}
				<div id="product-tab-content-{$tab.id}" class="{if !$tab.selected}not-loaded{/if} product-tab-content" {if !$tab.selected}style="display:none"{/if}>
					{if $tab.selected}
						{$custom_form}
					{/if}
				</div>
			{/foreach}
		</div>
		<input type="hidden" name="id_product_attribute" id="id_product_attribute" value="0" />
		<input type="hidden" name="key_tab" id="key_tab" value="Informations" />
		<input id="product_form_submit_btn"  type="submit" value="{l s='Save'}" name="submitAddproduct" class="button" />
	</form>
	
	<br/>
{/block}

