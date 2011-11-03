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

<link href="../css/fileuploader.css" rel="stylesheet" type="text/css">
<script src="../js/fileuploader.js" type="text/javascript"></script>
<script src="../js/jquery/ui/jquery.ui.core.min.js" type="text/javascript"></script>
<script src="../js/jquery/ui/jquery.ui.widget.min.js" type="text/javascript"></script>
<script src="../js/jquery/ui/jquery.ui.progressbar.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../js/admin.js"></script>

<div id="productBox">
{if isset($product_tabs)}
<div class="toolbarBox">
	<ul class="cc_button">
		{foreach from=$toolbar_btn item=btn key=k}
			<li>
				<a class="toolbar_btn action-{$k}" href="{$btn.href}" title="{$btn.desc}">
					<span class="process-icon-{$k} {$btn.class|default:''}" ></span>{$btn.desc}
				</a>
			</li> 
			{/foreach}
		</ul>
		<script type="text/javascript">
			$(document).ready(function(){
				$("a.toolbar_btn").click(function(e){
					e.preventDefault();
				});
			});
		</script>
		{if isset($product)}
		<div class="pageTitle">
			<h3>{l s='Current product:'}<span id="current_product" style="font-weight: normal;">&nbsp;</span></h3>
		</div>
		{/if}
</div>
 	<div class="productTabs">
		<ul class="tab">
		{foreach $product_tabs key=numStep item=tab}
			<li class="tab-row">
				<a class="tab-page {if $tab.selected}selected{/if}" id="link-{$tab.id}" href="{$tab.href}">{$tab.name}</a>{*todo href when nojs*}
			</li>
		{/foreach}
		</ul>
	</div>
<script type="text/javascript">
var toload = new Array();
var pos_select = {$pos_select};
$(document).ready(function(){
	{* submenu binding *}
	$(".tab-page").click(function(e){
		e.preventDefault();
		// currentId is the current producttab id
		currentId = $(".productTabs a.selected").attr('id').substr(5);
		// id is the wanted producttab id
		id = $(this).attr('id').substr(5);
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
					$("#product-tab-content-wait").hide();
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
		
		var languages = new Array();
		if (id == 3)
			populate_attrs();
		if (id == 7)
		{
			$("#addAttachment").click(function() {
				return !$("#selectAttachment1 option:selected").remove().appendTo("#selectAttachment2");
			});
			$("#removeAttachment").click(function() {
				return !$("#selectAttachment2 option:selected").remove().appendTo("#selectAttachment1");
			});
			$("#product").submit(function() {
				$("#selectAttachment1 option").each(function(i) {
					$(this).attr("selected", "selected");
				});
			});
		}
	});
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
						$('#virtual_product_name_attribute').attr('value', fileName);
						$('#upload-confirmation2').html(
							'<a class="link" href="get-file-admin.php?file='+msg+'&filename='+fileName+'">{l s='The file'}&nbsp;"' + fileName + '"&nbsp;{l s='has successfully been uploaded'}</a>' +
							'<input type="hidden" id="virtual_product_filename_attribute" name="virtual_product_filename_attribute" value="' + msg + '" />');
						
						link = $("#delete_downloadable_product_attribute").attr('href');		
						$("#delete_downloadable_product_attribute").attr('href', link+"&file="+msg);
					}
				}
			}
		);
	}
	//]]>
</script>
<form action="{$form_action}" method="post" enctype="multipart/form-data" name="product" id="product">
{$draft_warning}
<input type="hidden" name="id_product" value="{$id_product}" />
<input type="hidden" name="tabs" id="tabs" value="0" />
<div class="tab-pane" id="tabPane1">
	<div id="product-tab-content-wait" style="display:none" >{l s='loading ...'}</div>
	{if !$newproduct}
	{foreach $product_tabs key=numStep item=tab}
		<div id="product-tab-content-{$tab.id}" class="{if !$tab.selected}not-loaded{/if} product-tab-content" {if !$tab.selected}style="display:none"{/if}>
{if $tab.selected}{$content}{/if}
		</div>
	{/foreach}
	{else}{* @todo : this is a temporary fix*}
		<div id="product-tab-content-1" class="product-tab-content">{$content}</div>
	{/if}
</div>
			<input type="hidden" name="id_product_attribute" id="id_product_attribute" value="0" />
</form>
</div>
<br/>
<a href="{$link->getAdminLink('AdminCatalog')}"><img src="../img/admin/arrow2.gif" />{l s='Back to list'}</a><br/>
{else}
{$content}
{/if}

