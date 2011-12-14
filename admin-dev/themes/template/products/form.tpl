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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{extends file="helper/form/form.tpl"}

{block name="autoload_tinyMCE"}
	// change each by click to load only on click
	$(".autoload_rte").each(function(e){
		tinySetup({
			mode :"exact",
			editor_selector :"autoload_rte",
			elements : $(this).attr("id"),
			theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull|cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,undo,redo",
			theme_advanced_buttons2 : "link,unlink,anchor,image,cleanup,code,|,forecolor,backcolor,|,hr,removeformat,visualaid,|,charmap,media,|,ltr,rtl,|,fullscreen",
			theme_advanced_buttons3 : "",
			theme_advanced_buttons4 : "",
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
							textarea.parent('div').find('span.counter').html('<span style="color:red;">{l s='Maximum'} '+max+' {l s=' characters'} : '+rest+'</span>');
						else
							textarea.parent('div').find('span.counter').html(' ');
					}
				});

			}			
		});
	})
{/block}

{block name="defaultForm"}
	<div>
	 	<div class="productTabs">
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

		var toload = new Array();

		$(document).ready(function(){
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

				$("#product-tab-content-wait").show();

				if ($("#product-tab-content-"+id).hasClass('not-loaded'))
					displayTabProductById(this, id, true);
				else
				{
					$("#product-tab-content-"+id).show();
					$("#link-"+id).addClass('selected');
				}
				$("#product-tab-content-wait").hide();

				var languages = new Array();
				if (btn_name == "Combinations")
				{
					$('#desc-product-new').hide();
					$('#desc-product-newCombination').show();
					populate_attrs();
				}
				else if (btn_name == 'Attachments')
				{
					$("#addAttachment").live('click', function() {
						return !$("#selectAttachment2 option:selected").remove().appendTo("#selectAttachment1");
					});
					$("#removeAttachment").live('click', function() {
						return !$("#selectAttachment1 option:selected").remove().appendTo("#selectAttachment2");
					});
					$("#product").submit(function() {
						$("#selectAttachment1 option").each(function(i) {
							$(this).attr("selected", "selected");
						});
					});
				}
				else
					$('#desc-product-newCombination').hide();
			});

			{if $show_product_tab_content}
				$("div.productTabs a[id$=-{$show_product_tab_content}]").click();
			{/if}

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
	
	<script type="text/javascript">
		//<![CDATA[
		function toggleVirtualProduct(elt)
		{
			$("#is_virtual_file_product").hide();
			$("#virtual_good_attributes").hide();
	
			if (elt.checked)
			{
				$('#virtual_good').show('slow');
				$('#virtual_good_more').show('slow');
				getE('out_of_stock_1').checked = 'checked';
				getE('out_of_stock_2').disabled = 'disabled';
				getE('out_of_stock_3').disabled = 'disabled';
				getE('label_out_of_stock_2').setAttribute('for', '');
				getE('label_out_of_stock_3').setAttribute('for', '');
			}
			else
			{
				$('#virtual_good').hide('slow');
				$('#virtual_good_more').hide('slow');
				getE('out_of_stock_2').disabled = false;
				getE('out_of_stock_3').disabled = false;
				getE('label_out_of_stock_2').setAttribute('for', 'out_of_stock_2');
				getE('label_out_of_stock_3').setAttribute('for', 'out_of_stock_3');
			}
		}
	
		function uploadFile()
		{
			$.ajaxFileUpload (
				{
					url:'./uploadProductFile.php',
					secureuri:false,
					fileElementId:'virtual_product_file',
					dataType: 'xml',
					success: function (data, status)
					{
						data = data.getElementsByTagName('return')[0];
						var result = data.getAttribute("result");
						var msg = data.getAttribute("msg");
						var fileName = data.getAttribute("filename")
						if(result == "error")
							$("#upload-confirmation").html('<p>error: ' + msg + '</p>');
						else
						{
							$('#virtual_product_file').remove();
							$('#virtual_product_file_label').hide();
							$('#file_missing').hide();
							$('#delete_downloadable_product').show();
							$('#virtual_product_name').attr('value', fileName);
							$('#upload-confirmation').html(
								'<a class="link" href="get-file-admin.php?file='+msg+'&filename='+fileName+'">{l s='The file'}&nbsp;"' + fileName + '"&nbsp;{l s='has successfully been uploaded'}</a>' +
								'<input type="hidden" id="virtual_product_filename" name="virtual_product_filename" value="' + msg + '" />');
						}
					}
				}
			);
		}
	
		function uploadFile2()
		{
				var link = '';
				$.ajaxFileUpload (
				{
					url:'./uploadProductFileAttribute.php',
					secureuri:false,
					fileElementId:'virtual_product_file_attribute',
					dataType: 'xml',
					success: function (data, status)
					{
						data = data.getElementsByTagName('return')[0];
						var result = data.getAttribute("result");
						var msg = data.getAttribute("msg");
						var fileName = data.getAttribute("filename");
						if(result == "error")
							$("#upload-confirmation2").html('<p>error: ' + msg + '</p>');
						else
						{
							$('#virtual_product_file_attribute').remove();
							$('#virtual_product_file_label').hide();
							$('#file_missing').hide();
							$('#delete_downloadable_product_attribute').show();
							$('#upload-confirmation2').html(
								'<a class="link" href="get-file-admin.php?file='+msg+'&filename='+fileName+'">{l s='The file'}&nbsp;"' + fileName + '"&nbsp;{l s='has successfully been uploaded'}</a>' +
								'<input type="hidden" id="virtual_product_filename_attribute" name="virtual_product_filename_attribute" value="' + msg + '" />');
							$('#virtual_product_name_attribute').attr('value', fileName);
	
							link = $("#delete_downloadable_product_attribute").attr('href');
							$("#delete_downloadable_product_attribute").attr('href', link+"&file="+msg);
						}
					}
				}
			);
		}
		//]]>
	</script>
	
	<form id="product_form" action="{$form_action}" method="post" enctype="multipart/form-data" name="product">
		<input type="hidden" name="id_product" value="{$id_product}" />
		<div class="tab-pane" id="tabPane1">
		{if !$product->active}
			<div class="warn draft" >
				<p>
					<span style="float: left">
					{l s='Your product will be saved as draft'}</span>
					<span style="float:right"><a href="#" class="button" style="display: block" onclick="submitAddProductAndPreview()" >{l s='Save and preview'}</a></span>
					<input type="hidden" name="fakeSubmitAddProductAndPreview" id="fakeSubmitAddProductAndPreview" />
					<br />
				</p>
			 	</div>
		{/if}
		{* all input are here *}
			<div id="product-tab-content-wait" style="display:none" >{l s='loading ...'}</div>
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

