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

function showSaveButtons()
{
	$('#desc-product-save').show();
	$('#desc-product-cancel').show();
	$('#desc-product-duplicate').show();
	$('#desc-product-delete').show();
	$('#desc-product-save-and-stay').show();
}

function hideSaveButtons()
{
	$('#desc-product-save').hide();
	$('#desc-product-cancel').hide();
	$('#desc-product-duplicate').hide();
	$('#desc-product-delete').hide();
	$('#desc-product-save-and-stay').hide();
}

var toload = new Array();
var pos_select = {$pos_select};
var tabs_toolbar_save_buttons = [];
{foreach $tabs_toolbar_save_buttons key=key item=value}
{if $value == true}
	tabs_toolbar_save_buttons.push('{$key}');
{/if}
{/foreach}

$(document).ready(function(){
	$('#desc-product-newCombinaison').hide();
	{* submenu binding *}
	$(".tab-page").click(function(e){
		e.preventDefault();

		// currentId is the current producttab id
		currentId = $(".productTabs a.selected").attr('id').substr(5);
		// id is the wanted producttab id
		id = $(this).attr('id').substr(5);

		// Update submit button value
		var split_position = id.indexOf('-') + 1;
		var btn_name = id.substr(split_position);
		$("#product_form_submit_btn").attr('name', 'submit'+btn_name);

		// Show/hide save buttons
		if (jQuery.inArray(btn_name, tabs_toolbar_save_buttons) != -1)
			showSaveButtons();
		else
			hideSaveButtons();

		if ($(this).attr("id") != $(".productTabs a.selected").attr('id'))
		{
			$(".tab-page").removeClass('selected');
			$("#product-tab-content-"+currentId).hide();
		}
		else
		{
			if (confirm(' {l s='Do you really want to reload the current tab (all modifications will be lost)'}'))
				$("#product-tab-content-"+currentId).html();
			else
				return false;
		}

		$("#product-tab-content-wait").show();

		if ($("#product-tab-content-"+id).hasClass('not-loaded') || $(this).hasClass('selected'))
		{
			myurl = $(this).attr("href")+"&ajax=1";
			$.ajax({
				url : myurl,
				async : true,
				success :function(data)
				{
					$("#product-tab-content-"+id).html(data);
					$("#product-tab-content-"+id).removeClass('not-loaded');
					$("#product-tab-content-"+id).show();
					$("#link-"+id).addClass('selected');
				}
			});
		}
		else
		{
			$("#product-tab-content-"+id).show();
			$("#link-"+id).addClass('selected');
		}
		$("#product-tab-content-wait").hide();

		var languages = new Array();
		if (id == "4-Combinations")
		{
			$('#desc-product-new').hide();
			$('#desc-product-newCombinaison').show();
			populate_attrs();
		}
		if (id == '7-Attachments')
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
	<input type="hidden" name="tabs" id="tabs" value="0" />
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
	{if !$newproduct}
	{foreach $product_tabs key=numStep item=tab}
		<div id="product-tab-content-{$tab.id}" class="{if !$tab.selected}not-loaded{/if} product-tab-content" {if !$tab.selected}style="display:none"{/if}>
		{if $tab.selected}{$custom_form}{/if}
		</div>
	{/foreach}
	{else}
		<div id="product-tab-content-1" class="product-tab-content">{$custom_form}</div>
	{/if}
</div>
<input type="hidden" name="id_product_attribute" id="id_product_attribute" value="0" />
<input id="product_form_submit_btn"  type="submit" value="{l s='Save'}" name="submitAddproduct" class="button" />
</form>
</div>
<br/>
{/block}
{*a href="{$link->getAdminLink('AdminCatalog')}"><img src="../img/admin/arrow2.gif" />{l s='Back to list'}</a><br/>
*}

