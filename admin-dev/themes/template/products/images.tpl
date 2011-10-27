<div class="tab-page" id="step2">
		<h4 class="tab" >2. {l s='Images'} (<span id="countImage">{$countImages}</span>)</h4>
		<table cellpadding="5">
		<tr>
			<td><b>{if isset($id_image)}{l s='Edit this product image'}{else}{l s='Add a new image to this product'}{/if}</b></td>
		</tr>
		</table>
		<hr style="width: 100%;" /><br />
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
				id_product : "{$id_product}",
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
			$("#listImage").append("<li id=\'img"+id+"\'><div class=\"float\" >" + filename + "</div></div><a style=\"margin-left:10px;\" href=\"javascript:delQueue(" + id +");\"><img src=\"../img/admin/disabled.gif\" alt=\"\" border=\"0\"></a><p class=\"errorImg\"></p></li>");
		},

		});
	});
</script>

{$content}
