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

{if isset($id_product) && isset($product)}
<div class="panel">
<input type="hidden" name="submitted_tabs[]" value="Images" />
	
<h3 class="tab" >
	{l s='Images'}
	<span class="badge" id="countImage">{$countImages}</span>
</h3>

<div class="row">
	<div class="form-group">
		<label class="control-label col-lg-3 file_upload_label">
			<span class="label-tooltip" data-toggle="tooltip"
				title="{l s='Format:'} JPG, GIF, PNG. {l s='Filesize:'} {$max_image_size|string_format:"%.2f"} {l s='MB max.'}">
				{if isset($id_image)}{l s='Edit this product image'}{else}{l s='Add a new image to this product'}{/if}
			</span>
		</label>
		<div class="col-lg-9">
			{l s='File:'}
			<div id="file-uploader">
				<noscript>
					<p>{l s='Please enable JavaScript to use file uploader:'}</p>
				</noscript>
			</div>
			<div id="progressBarImage" class="progressBarImage"></div>
			<div id="showCounter" style="display:none;">
				<span id="imageUpload">0</span><span id="imageTotal">0</span>
			</div>
			<input type="hidden" name="resizer" value="auto" />
		{if Tools::getValue('id_image')}
			<input type="hidden" name="id_image" value="{Tools::getValue('id_image')|intval}" />
		{/if}
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-lg-3">
			{l s='Legend:'}
		</label>
		<div class="col-lg-9 translatable">
			{foreach from=$languages item=language}
			<div class="lang_{$language.id_lang}" style="{if !$language.is_default}display: none;{/if} float: left;">
				<input class="updateCurrentText" type="text" {if !$product->id}disabled="disabled"{/if} id="legend_{$language.id_lang}" name="legend_{$language.id_lang}" value="{$product->name[$language.id_lang]|escape:'htmlall':'UTF-8'}"/>
				<div class="help-block">{l s='Invalid characters:'} <>;=#{}</div>
			</div>
			{/foreach}
		</div>
	</div>
</div>

<table class="table tableDnD" id="imageTable">
	<thead>
		<tr class="nodrag nodrop"> 
			<th class="fixed-width-sm"><span class="title_box">{l s='Image'}</span></th>
			<th class="fixed-width-lg"><span class="title_box">{l s='Legend'}</span></th>
			<th class="center fixed-width-xs"><span class="title_box">{l s='Position'}</span></th>
			{if $shops}
			{foreach from=$shops item=shop}
				<th class="center fixed-width-xs"><span class="title_box">{$shop.name}</span></th>
			{/foreach}
			{/if}
			<th class="center fixed-width-xs"><span class="title_box">{l s='Cover'}</span></th>
			<th></th>
		</tr>
	</thead>
	<tbody id="imageList">
	</tbody>
</table>

<table id="lineType" style="display:none;">
	<tr id="image_id">
		<td>
			<a href="{$smarty.const._THEME_PROD_DIR_}image_path.jpg" class="fancybox">
				<img src="{$smarty.const._THEME_PROD_DIR_}{$iso_lang}-default-{$imageType}.jpg" alt="image_id" title="image_id" />
			</a>
		</td>
		<td>legend</td>
		<td id="td_image_id" class="pointer dragHandle center positionImage">
			image_position
		</td>
		{if $shops}
			{foreach from=$shops item=shop}
			<td class="center">
				<input type="checkbox" class="image_shop" name="id_image" id="{$shop.id_shop}image_id" value="{$shop.id_shop}" />
			</td>
			{/foreach}
		{/if}
		<td class="center cover"><a href="#">
			<i class="covered icon-check-empty"></i>
		</td>
		<td class="center">
			<a href="#" class="btn btn-default delete_product_image pull-right" >
				<i class="icon-trash"></i> {l s='Delete this image'}
			</a>
		</td>
	</tr>
</table>

<script type="text/javascript">
	var upbutton = '{l s='Upload an image'}';
	var token = '{$token}';
	var come_from = '{$table}';
	var success_add =  '{l s='The image has been successfully added.'}';
	var id_tmp = 0;
	var current_shop_id = {$current_shop_id|intval};
	{literal}
	//Ready Function
	$(document).ready(function(){
		{/literal}
		{foreach from=$images item=image}
			assoc = {literal}"{"{/literal};
			{if $shops}
				{foreach from=$shops item=shop}
					assoc += '"{$shop.id_shop}" : {if $image->isAssociatedToShop($shop.id_shop)}1{else}0{/if},';
				{/foreach}
			{/if}
			if (assoc != {literal}"{"{/literal})
			{
				assoc = assoc.slice(0, -1);
				assoc += {literal}"}"{/literal};
				assoc = jQuery.parseJSON(assoc);
			}
			else
				assoc = false;
			imageLine({$image->id}, "{$image->getExistingImgPath()}", {$image->position}, "{if $image->cover}icon-check-sign{else}icon-check-empty{/if}", assoc, "{$image->legend[$default_language]|@addcslashes:'\"'}");
		{/foreach}
		{literal}
		$("#imageTable").tableDnD(
		{
			onDrop: function(table, row) {
			current = $(row).attr("id");
			stop = false;
			image_up = "{";
			$("#imageList").find("tr").each(function(i) {
				$("#td_" +  $(this).attr("id")).html(i + 1);
				if (!stop || (i + 1) == 2)
					image_up += '"' + $(this).attr("id") + '" : ' + (i + 1) + ',';
			});
			image_up = image_up.slice(0, -1);
			image_up += "}";
			updateImagePosition(image_up);
			}
		});
		var filecheck = 1;
		var params = new Array;
		params['id_product'] = {/literal}{$id_product|intval}{literal};
		params['id_category'] = {/literal}{$id_category_default|intval}{literal};
		params['token'] = "{/literal}{$token}{literal}";
		params['tab'] = "AdminProducts";
		params['action'] = "addImage";
		params['ajax'] = 1;
		uploader = new qq.FileUploader(
		{
			element: document.getElementById("file-uploader"),
			action: "ajax-tab.php",
			debug: false,
			onComplete: function(id, fileName, responseJSON)
			{
				var percent = ((filecheck * 100) / nbfile);
				$("#progressBarImage").progressbar({value: percent});
				if (percent != 100)
				{
					$("#imageUpload").html(parseInt(filecheck));
					$("#imageTotal").html(" / " + parseInt(nbfile) + " {/literal}{l s='Images'}{literal}");
					$("#progressBarImage").show();
					$("#showCounter").show();
				}
				else
				{
					$("#progressBarImage").progressbar({value: 0});
					$("#progressBarImage").hide();
					$("#showCounter").hide();
					nbfile = 0;
					filecheck = 0;
				}
				if (responseJSON.status == 'ok')
				{
					cover = "icon-check-empty";
					if (responseJSON.cover == "1")
						cover = "icon-check-sign";
					imageLine(responseJSON.id, responseJSON.path, responseJSON.position, cover, responseJSON.shops, responseJSON.legend[{/literal}{$default_language|intval}{literal}])
					$("#imageTable tr:last").after(responseJSON.html);
					$("#countImage").html(parseInt($("#countImage").html()) + 1);
					$("#img" + id).remove();
					$("#imageTable").tableDnDUpdate();
					showSuccessMessage(responseJSON.name + " " + success_add);
				}
				else
					showErrorMessage(responseJSON.error);
				filecheck++;
			},
			onSubmit: function(id, filename)
			{
				$('input[id^="legend_"]').each(function()
				{
					id = $(this).prop("id").replace("legend_", "legend[") + "]";
					params[id] = $(this).val();
				});
				uploader.setParams(params);
				$("#imageTable").show();
				$("#listImage").append("<li id='img"+id+"'><div>" + filename + "</div></div><a href=\"javascript:delQueue(" + id +");\"><img src=\"../img/admin/disabled.gif\" ></a><p class=\"errorImg\"></p></li>");
			}
		});

		/**
		 * on success function 
		 */
		function afterDeleteProductImage(data)
		{
			data = $.parseJSON(data);
			if (data)
			{
				cover = 0;
				id = data.content.id;
				if (data.status == 'ok')
				{
					if ($("#" + id + ' .covered').hasClass('icon-check-sign'))
						cover = 1;
					$("#" + id).remove();
				}
				if (cover)
					$("#imageTable tr").eq(1).find(".covered").addClass('icon-check-sign');
				$("#countImage").html(parseInt($("#countImage").html()) - 1);
				refreshImagePositions($("#imageTable"));
				showSuccessMessage(data.confirmations);
			}
		}

		$('.delete_product_image').die().live('click', function(e)
		{
			e.preventDefault();
			id = $(this).parent().parent().attr('id');
			if (confirm("{/literal}{l s='Are you sure?' js=1}{literal}"))
			doAdminAjax({
					"action":"deleteProductImage",
					"id_image":id,
					"id_product" : {/literal}{$id_product}{literal},
					"id_category" : {/literal}{$id_category_default}{literal},
					"token" : "{/literal}{$token}{literal}",
					"tab" : "AdminProducts",
					"ajax" : 1 }, afterDeleteProductImage
			);
		});
		
		$('.covered').die().live('click', function(e)
		{
			e.preventDefault();
			id = $(this).parent().parent().parent().attr('id');
			$("#imageList .cover i").each( function(i){
				$(this).removeClass('icon-check-sign').addClass('icon-check-empty');
			});
			$(this).removeClass('icon-check-empty').addClass('icon-check-sign');

			if (current_shop_id != 0)
				$('#' + current_shop_id + id).attr('check', true);
			else
				$(this).parent().parent().parent().children('td input').attr('check', true);
			doAdminAjax({
				"action":"UpdateCover",
				"id_image":id,
				"id_product" : {/literal}{$id_product}{literal},
				"token" : "{/literal}{$token}{literal}",
				"controller" : "AdminProducts",
				"ajax" : 1 }
			);
		});
		
		$('.image_shop').die().live('click', function()
		{
			active = false;
			if ($(this).attr("checked"))
				active = true;
			id = $(this).parent().parent().attr('id');
			id_shop = $(this).attr("id").replace(id, "");
			doAdminAjax(
			{
				"action":"UpdateProductImageShopAsso",
				"id_image":id,
				"id_product":id_product,
				"id_shop": id_shop,
				"active":active,
				"token" : "{/literal}{$token}{literal}",
				"tab" : "AdminProducts",
				"ajax" : 1 
			});
		});

		function updateImagePosition(json)
		{
			doAdminAjax(
			{
				"action":"updateImagePosition",
				"json":json,
				"token" : "{/literal}{$token}{literal}",
				"tab" : "AdminProducts",
				"ajax" : 1
			});
		}
		
		function delQueue(id)
		{
			$("#img" + id).fadeOut("slow");
			$("#img" + id).remove();
		}
		
		function imageLine(id, path, position, cover, shops, legend)
		{
			line = $("#lineType").html();
			line = line.replace(/image_id/g, legend);			
			line = line.replace(/[a-z]{0,2}-default-small_default/g, path+'-small_default');
			line = line.replace(/image_path/g, path);
			line = line.replace(/image_position/g, position);
			line = line.replace(/legend/g, legend);
			line = line.replace(/icon-check-empty/g, cover);
			line = line.replace(/<tbody>/gi, "");
			line = line.replace(/<\/tbody>/gi, "");
			if (shops != false)
			{
				$.each(shops, function(key, value){
					if (value == 1)
						line = line.replace('id="' + key + '' + id + '"','id="' + key + '' + id + '" checked=checked');
				});
			}
			$("#imageList").append(line);
		}
		$('.fancybox').fancybox();
	});
	{/literal}
</script>
</div>
{/if}
