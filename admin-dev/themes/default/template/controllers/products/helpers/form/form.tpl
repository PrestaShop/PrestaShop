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

{extends file="helpers/form/form.tpl"}

{block name="autoload_tinyMCE"}
	// Execute when tab Informations has finished loading
	tabs_manager.onLoad('Informations', function(){
		tinySetup({
			editor_selector :"autoload_rte",
			setup : function(ed) {
				ed.onInit.add(function(ed)
				{
					if (typeof ProductMultishop.load_tinymce[ed.id] != 'undefined')
					{
						if (typeof ProductMultishop.load_tinymce[ed.id])
							ed.hide();
						else
							ed.show();
					}
				});

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
{/block}

{block name="defaultForm"}
	<div>
		<div class="productTabs" style="display:none;">
			<ul class="tab">
			{*todo href when nojs*}
			{foreach $product_tabs key=numStep item=tab}
				<li class="tab-row">
					<a class="tab-page {if $tab.selected}selected{/if}" id="link-{$tab.id}" href="{$tab.href}&amp;updateproduct">{$tab.name}</a>
				</li>
			{/foreach}
			</ul>
		</div>
	</div>
	<script type="text/javascript">
		var token = '{$token}';
		var id_product = {if isset($product->id)}{$product->id}{else}0{/if};
		var id_lang_default = {$id_lang_default};
		var product_type_pack = {Product::PTYPE_PACK};
		var product_type_virtual = {Product::PTYPE_VIRTUAL};
		var product_type_simple = {Product::PTYPE_SIMPLE};

		var has_combinations = {$has_combinations};

		var toload = new Array();
		var empty_pack_msg = '{l s='This pack is empty. You will need to add at least one product to the pack before you can save.' slashes=1}';
		var empty_name_msg = '{l s='The product name is empty. You will at least need to enter a name for the default language before you can save the product.' slashes=1}';
		var empty_link_rewrite_msg = '{l s='The friendly URL is empty. You will at least need to enter a friendly URL for the default language before you can save the product.' slashes=1}';
		var reload_tab_title = '{l s='Confirmation' slashes=1}';
		var reload_tab_description = '{l s='Some tabs was not loaded correctly. Would you like to reload them?' slashes=1}';

		$('#product-tab-content-wait').show();
		var post_data = {$post_data};
		var save_error = {if $save_error}true{else}false{/if};

		var product_type = {$product_type};
		{*var mce_maximum = '{l s='Maximum'}';*}
		{if isset($display_multishop_checkboxes) && $display_multishop_checkboxes}
			var display_multishop_checkboxes = true;
		{else}
			var display_multishop_checkboxes = false;
		{/if}

		$(document).ready(function()
		{
			$('#product-tab-content-wait').show();
			//product_type = $("input[name=type_product]:checked").val();
			if (product_type == product_type_pack)
			{
				$('li.tab-row a[id*="VirtualProduct"]').hide();
			}
			else if (product_type == product_type_virtual)
			{
				$('li.tab-row a[id*="Pack"]').hide();
				$('li.tab-row a[id*="Shipping"]').hide();
			}
			else
			{
				$('li.tab-row a[id*="Pack"]').hide();
				$('li.tab-row a[id*="VirtualProduct"]').hide();
			}

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

				// if the tab has not already been loaded, load it now
				tabs_manager.display(id, true);

				tabs_manager.onLoad(id, function(){
					$("#product-tab-content-"+id).show(0, function(){
						$(this).trigger('displayed');
					});
					$("#link-"+id).addClass('selected');
				});

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

			tabs_manager.onLoad('Associations', function(){
				handleSaveButtons();
			});

			$('.confirm_leave').live('click', function(){
				// Double quotes are necessary when the translated string has single quotes
				return confirm("{l s='You will lose all unsaved modifications. Are you sure that you\'d like to proceed?' js=1}");
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
			tabs_manager.displayBulk(tabs_to_preload);
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
			ThickboxI18nOrEscKey = "{l s='(or "Esc")'}";
			ThickboxI18nNext = "{l s='Next >'}";
			ThickboxI18nPrev = "{l s='< Previous'}";
			tb_pathToImage = "../img/loadingAnimation.gif";
		//]]>
	</script>

	<div id="product-tab-content-wait" style="display:none"><div id="loading">{l s='Loading...'}</div></div>

	<form id="product_form" action="{$form_action}" method="post" enctype="multipart/form-data" name="product" style="display:none;">
		<input type="hidden" name="id_product" value="{$id_product}" />
		<input type="hidden" id="is_virtual" name="is_virtual" value="{$product->is_virtual|escape:html:'UTF-8'}" />
		<div class="tab-pane" id="tabPane1">
		{if !$product->active && $product->isAssociatedToShop()}
			<div class="warn draft" >
				<p>
					<span style="float: left">
					{l s='Your product will be saved as a draft.'}</span>
					<span style="float:right"><a href="#" class="button" style="display: block" onclick="submitAddProductAndPreview()" >{l s='Save and preview.'}</a></span>
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

