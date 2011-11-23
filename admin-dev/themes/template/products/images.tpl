<div class="tab-page" id="step2">
	<h4 class="tab" >2. {l s='Images'} (<span id="countImage">{$countImages}</span>)</h4>
	<table cellpadding="5">
		<tr>
			<td><b>{if isset($id_image)}{l s='Edit this product image'}{else}{l s='Add a new image to this product'}{/if}</b></td>
		</tr>
	</table>
	<div class="separation"></div><br />
		<table cellpadding="5" style="width:100%">
		<tr>
			<td class="col-left"><label>{l s='File:'}</label></td>
			<td style="padding-bottom:5px;">
				<div id="file-uploader">
					<noscript>
						<p>{l s='Please enable JavaScript to use file uploader:'}</p>
					</noscript>
				</div>
				<div id="progressBarImage" class="progressBarImage"></div>
				<div id="showCounter" style="display:none;"><span id="imageUpload">0</span><span id="imageTotal">0</span></div>
					<p class="float" style="clear: both;">
						{l s='Format:'} JPG, GIF, PNG. {l s='Filesize:'} {$max_image_size / 1000}{l s='Kb max.'}
					</p>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center;">
				<input type="hidden" name="resizer" value="auto" />
					{if Tools::getValue('id_image')}<input type="hidden" name="id_image" value="{Tools::getValue('id_image')}" />{/if}
			</td>
		</tr>
		<tr><td colspan="2" style="padding-bottom:10px;"><div class="separation"></div></td></tr>
		<tr>
			<td colspan="2">
				<table cellspacing="0" cellpadding="0" class="table tableDnD" id="imageTable">
						<thead>
						<tr class="nodrag nodrop"> 
							<th style="width: 100px;">{l s='Image'}</th>
							<th>{l s='Position'}</th>
							{if $shops}
							{foreach from=$shops item=shop}
								<th>{$shop.name}</th>
							{/foreach}
							{/if}
							<th>{l s='Cover'}</th>
							<th>{l s='Action'}</th>
						</tr>
						</thead>
						<tbody id="imageList">
						</tbody>
				</table>
			</td>
		</tr>
	</table>
</div>
<table id="lineType" style="display:none;">
	<tr id="image_id">
		<td style="padding: 4px;">
			<a href="image_path.jpg" target="_blank">
				<img src="{$smarty.const._THEME_PROD_DIR_}en-default-small.jpg" alt="image_id" title="image_id" />
			</a>
		</td>
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
			<img class="covered" src="../img/admin/blank.gif" alt="e" /></a>
		</td>
		<td class="center">
			<a href="#" class="delete_product_image" >
				<img src="../img/admin/delete.gif" alt="{l s='Delete this image'}" title="{l s='Delete this image'}" />
			</a>
		</td>
	</tr>
</table>
<script type="text/javascript" src="../js/jquery/plugins/jquery.tablednd.js"></script>
<script type="text/javascript">
	var upbutton = '{l s='Upload a file'}';
	var token = '{$token}';
	var come_from = '{$table}';
	var id_tmp = 0;
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
			}
			else
				assoc = false;
			imageLine({$image->id}, "{$image->getExistingImgPath()}", {$image->position}, "{if $image->cover}enabled{else}forbbiden{/if}", assoc);
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
				if ($(this).attr("id") == current)
				{	
					image_up += '"' + $(this).attr("id") + '" : ' + (i + 1) + ',';
					stop = true;
				}
				if (!stop || (i + 1) == 2)
					image_up += '"' + $(this).attr("id") + '" : ' + (i + 1) + ',';
			});
			image_up = image_up.slice(0, -1);
			image_up += "}";
			updateImagePositon(image_up);
			}
		});
		var filecheck = 1;
		var uploader = new qq.FileUploader(
		{
			element: document.getElementById("file-uploader"),
			action: "ajax-tab.php",
			debug: false,
			params: {
				id_product : {/literal}{$id_product}{literal},
				id_category : {/literal}{$id_category_default}{literal},
				token : "{/literal}{$token}{literal}",
				tab : "AdminProducts",
				action : 'addImage',
				ajax: 1,
			},
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
					cover = "forbbiden";
					if (responseJSON.cover == "1")
						cover = "enabled";
					imageLine(responseJSON.id, responseJSON.path, responseJSON.position, cover, false)
					$("#imageTable tr:last").after(responseJSON.html);
					$("#countImage").html(parseInt($("#countImage").html()) + 1);
					$("#img" + id).remove();
					$("#imageTable").tableDnDUpdate();
				}
				else
				{
					$("#img" + id).addClass("red");
					$("#img" + id + " .errorImg").html(responseJSON.error);
					$("#img" + id + " .errorImg").show();
				}
				filecheck++;
			},
			onSubmit: function(id, filename)
			{
				$("#imageTable").show();
				$("#listImage").append("<li id='img"+id+"'><div class=\"float\" >" + filename + "</div></div><a style=\"margin-left:10px\"href=\"javascript:delQueue(" + id +");\"><img src=\"../img/admin/disabled.gif\" alt=\"\" border=\"0\"></a><p class=\"errorImg\"></p></li>");
			}
		});
		
		$('.delete_product_image').die().live('click', function(e)
		{
			e.preventDefault();
			id = $(this).parent().parent().attr('id');
			if (confirm("{/literal}{l s='Are you sure?'}{literal}"))
			$.ajax(
			{
				url : "ajax-tab.php",
				data : {"action":"deleteProductImage",
					"id_image":id,
					"id_product" : {/literal}{$id_product}{literal},
					"id_category" : {/literal}{$id_category_default}{literal},
					"token" : "{/literal}{$token}{literal}",
					"tab" : "AdminProducts",
					"ajax" : 1 },
				success : function (data)
				{
					data = jQuery.parseJSON(data);
					if (data)
					{
						cover = 0;
						if(data.status == 'ok')
						{
							if ($("#" + id).find(".covered").attr("src") == "../img/admin/enabled.gif")
								cover = 1;
							$("#" + id).remove();
						}
						if (cover)
							$("#imageTable tr").eq(1).find(".covered").attr("src", "../img/admin/enabled.gif");
						$("#countImage").html(parseInt($("#countImage").html()) - 1);
						refreshImagePositions($("#imageTable"));
					}
				}
			});
		});
		
		$('.covered').die().live('click', function(e)
		{
			e.preventDefault();
			id = $(this).parent().parent().parent().attr('id');
			$("#imageList .cover img").each( function(i){
				$(this).attr("src", $(this).attr("src").replace("enabled", "forbbiden"));
			});
			$(this).attr("src", $(this).attr("src").replace("forbbiden", "enabled"));
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
				"id_shop": id_shop,
				"active":active,
				"token" : "{/literal}{$token}{literal}",
				"tab" : "AdminProducts",
				"ajax" : 1 
			});
		});
		
		//function	
		function updateImagePositon(json)
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
		
		function imageLine(id, path, position, cover, shops)
		{
			line = $("#lineType").html();
			line = line.replace(/image_id/g, id);
			line = line.replace(/en-default/g, path);
			line = line.replace(/image_position/g, position);
			line = line.replace(/blank/g, cover);
			line = line.replace("<tbody>", "");
			line = line.replace("</tbody>", "");
			if (shops != false)
			{
				tmp = jQuery.parseJSON(shops);
				$.each(tmp, function(key, value){
					if (value == 1)
						line = line.replace('id="' + key + '' + id + '"','id="' + key + '' + id + '" checked=checked');
				});
			}
			$("#imageList").append(line);
		}
	});
	{/literal}
</script>
