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
	<ul id="listImage"></ul>
	<script type="text/javascript">var upbutton = "{l s='Upload a file'}"; </script>
	{firebug value=$product}
	<script type="text/javascript">
	function deleteImg(id)
	{
		var conf = confirm("{l s='Are you sure?'}");
		if (conf)
			$.post(
				"ajax-tab.php",
			{
				action: "deleteImage",
				id_image:id,
				id_product : "{$product->id}",
				id_category : "{$id_category_default}",
				token : "{$token}",
				tab : "AdminProducts",
				ajax : 1,
				updateproduct : 1},
				function (data) {
					if (data)
					{
						cover = 0;
						if(data.imageDeleted)
						{
							if ($("#tr_" + id).find(".covered").attr("src") == "../img/admin/enabled.gif")
								cover = 1;
							$("#tr_" + id).remove();
						}
						if (cover)
							$("#imageTable tr").eq(1).find(".covered").attr("src", "../img/admin/enabled.gif");

						$("#countImage").html(parseInt($("#countImage").html()) - 1);

						// refreshImagePositions($("#imageTable"));
					}
			});
			return false;
	}

	function delQueue(id)
	{
		$("#img" + id).fadeOut("slow");
		$("#img" + id).remove();
	}
	$(document).ready(function () {
		var filecheck = 1;
		var uploader = new qq.FileUploader({
			element: document.getElementById("file-uploader"),
			action: "ajax-tab.php",
			debug: false,
		params: {
			id_product : "{$id_product}",
			id_category : "{$id_category_default}",
			token : "{$token}",
			tab : "AdminProducts",
			updateproduct : 1,
			addImage : 1,
			ajaxMode : 1,
			ajax: 1,
			},
			onComplete: function(id, fileName, responseJSON){
				var percent = ((filecheck * 100) / nbfile);
				$("#progressBarImage").progressbar({
					value: percent
				});
				if (percent != 100)
				{
					$("#imageUpload").html(parseInt(filecheck));
					$("#imageTotal").html(" / " + parseInt(nbfile) + " {l s='Images'}");
					$("#progressBarImage").show();
					$("#showCounter").show();
				}
				else
				{
					$("#progressBarImage").progressbar({
						value: 0
					});
					$("#progressBarImage").hide();
					$("#showCounter").hide();
					nbfile = 0;
					filecheck = 0;
				}
				if (responseJSON.success)
				{
					$("#imageTable tr:last").after(responseJSON.success);
					$("#countImage").html(parseInt($("#countImage").html()) + 1);
					$("#img" + id).remove();
				}
				else
				{
					$("#img" + id).addClass("red");
					$("#img" + id + " .errorImg").html(responseJSON.error);
					$("#img" + id + " .errorImg").show();

			}
			if (percent >= 100)
			{
				refreshImagePositions($("#imageTable"));
			}
			filecheck++;
		},
		onSubmit: function(id, filename){
			$("#imageTable").show();
			$("#listImage").append("<li id='img"+id+"'><div class=\"float\" >" + filename + "</div></div><a style=\"margin-left:10px;\" href=\"javascript:delQueue(" + id +");\"><img src=\"../img/admin/disabled.gif\" alt=\"\" border=\"0\"></a><p class=\"errorImg\"></p></li>");
		},

		});
	});
</script>

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
							<script type="text/javascript">
								var token = '{$token}';
								var come_from = '{$table}';
								var alternate = '{if $_orderWay == 'DESC'}1{else}0{/if};
							</script>
							<table cellspacing="0" cellpadding="0" class="table tableDnD" id="imageTable">
								<thead>
								<tr>
									<th style="width: 100px;">{l s='Image'}</th>
									<th>&nbsp;</th>
									<th>{l s='Position'}</th>
						{if $shops}
							<script type="text/javascript">
											$(document).ready(function() {
												$('.image_shop').change(function() {
													$.post("ajax-tab.php",
														{
															updateProductImageShopAsso: 1,
															id_image:$(this).attr("name"),
															id_shop: $(this).val(),
															active:$(this).attr("checked"),
															id_product : "{$product->id}",
															id_category : "{$product->id_category_default}",
															token : "{$token}",
															tab : "AdminProducts",
															updateproduct : 1,
														});
												});
											});
										</script>
							{foreach from=$shops item=shop}
								<th>{$shop.name}</th>
							{/foreach}
						{/if}
						
									<th>{l s='Cover'}</th>
									<th>{l s='Action'}</th>
								</tr></thead>
			{foreach from=$images item=image}
			<tr id="tr_{$image->id}">
				<td style="padding: 4px;">
					<a href="{$smarty.const._THEME_PROD_DIR_}{$image->getExistingImgPath()}.jpg" target="_blank">
						<img src="{$smarty.const._THEME_PROD_DIR_}{$image->getExistingImgPath()}-small.jpg{if isset($smarty.post.image_updated) && $smarty.post.image_updated == $image->id}?date={time()}{/if}"
					alt="{$image->id} - {$image->legend|htmlentitiesUTF8}" title="{$image->id} - {$image->legend|htmlentitiesUTF8}" />
					</a>
				</td>
				<td class="center positionImage">{$image->position}</td>
					<td id="td_{$image->id}" class="pointer dragHandle center">
					<a {if $image->position}style="display: none;"{/if} href="{$currentIndex}&amp;id_image={$image->id_image}&amp;imgPosition={$image->position -1}&amp;imgDirection=0&amp;token={$token}">
						<img src="../img/admin/up.gif" alt="" border="0">
					</a>
					<a {if $image->position} == $imagesTotal} style="display: none;"{/if} href="{$currentIndex}&amp;id_image={$image->id_image}&amp;imgPosition={$image->position+1}&amp;imgDirection=1&amp;token={$token}">
						<img src="../img/admin/down.gif" alt="" border="0">
					</a>
				</td>
				{foreach from=$shops item=shop}
				<td class="center">
				<input type="checkbox" class="image_shop" name="{$image->id_image}" value="{$shop.id_shop}" {if $image->isAssociatedToShop($shop.id_shop)}checked="checked"{/if} />
				</td>
				{/foreach}
				<td class="center"><a href="{$currentIndex}&amp;id_image={$image->id_image}&amp;coverImage&amp;token={$token}">
					<img class="covered" src="../img/admin/{if $image->cover}enabled.gif{else}forbbiden.gif{/if}" alt="e" /></a>
				</td>
				<td class="center">
					<a href="#" onclick="deleteImg('{$image->id_image}');">
						<img src="../img/admin/delete.gif" alt="{l s='Delete this image'}" title="{l s='Delete this image'}" />
					</a>
				</td>
			</tr>

{/foreach}
							</table>
						</td>
					</tr>
				</table>
			</div>

