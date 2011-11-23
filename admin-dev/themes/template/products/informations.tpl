<div class="" id="step1">
			<h4 class="tab">1. {l s='Info.'}</h4>
			<script type="text/javascript">
				$(document).ready(function() {
					updateCurrentText();
					updateFriendlyURL();
					$.ajax({
						url: "ajax-tab.php",
						cache: false,
						dataType: "json",
						data: {
							ajaxProductManufacturers:"1",
							ajax : '1',
							token : "{$token}",
							controller : "AdminProducts",
							action : "productManufacturers",
						},
						success: function(j) {
							var options = $("select#id_manufacturer").html();
							if (j)
							for (var i = 0; i < j.length; i++)
								options += '<option value="' + j[i].optionValue + '">' + j[i].optionDisplay + '</option>';
							$("select#id_manufacturer").replaceWith("<select id=\"id_manufacturer\">"+options+"</select>");
						},
						error: function(XMLHttpRequest, textStatus, errorThrown)
						{
							$("select#id_manufacturer").replaceWith("<p id=\"id_manufacturer\">[TECHNICAL ERROR] ajaxProductManufacturers: "+textStatus+"</p>");
						}

					});
					/*$.ajax({
						url: "ajax-tab.php",
						cache: false,
						dataType: "json",
						data: {
							ajaxProductSuppliers:"1",
							ajax : '1',
							token : "{$token}",
							controller : "AdminProducts",
							action : "productSuppliers",
						},
						success: function(j) {
							var options = $("select#id_supplier").html();
							if (j)
							for (var i = 0; i < j.length; i++)
								options += '<option value="' + j[i].optionValue + '">' + j[i].optionDisplay + '</option>';
							$("select#id_supplier").replaceWith("<select id=\"id_supplier\">"+options+"</select>");
						},
						error: function(XMLHttpRequest, textStatus, errorThrown)
						{
							$("select#id_supplier").replaceWith("<p id=\"id_supplier\">[TECHNICAL ERROR] ajaxProductSuppliers : "+textStatus+"</p>");
						}

					});*/
					if ($('#available_for_order').is(':checked')){
						$('#show_price').attr('checked', 'checked');
						$('#show_price').attr('disabled', 'disabled');
					}
					else {
						$('#show_price').attr('disabled', '');
					}
				});
			</script>
			<h4>{l s='Product global information'}</h4>
		<script type="text/javascript">
			{$combinationImagesJs}
			$(document).ready(function(){
				$('#id_mvt_reason').change(function(){
					updateMvtStatus($(this).val());
				});
				updateMvtStatus($(this).val());
			});
			function updateMvtStatus(id_mvt_reason)
			{
				if (id_mvt_reason == -1)
					return $('#mvt_sign').hide();
				if ($('#id_mvt_reason option:selected').attr('rel') == -1)
					$('#mvt_sign').html('<img src="../img/admin/arrow_down.png" /> {l s='Decrease your stock'}');
				else
					$('#mvt_sign').html('<img src="../img/admin/arrow_up.png" /> {l s='Increase your stock'}');
				$('#mvt_sign').show();
			}
		</script>
			<div class="separation"></div>
			<br />
<table cellpadding="5" style="width: 50%; float: left; margin-right: 20px; border-right: 1px solid #E0D0B1;">
{* global information *}
	<tr>
		<td class="col-left"><label>{l s='Name:' }</label></td>
		<td style="padding-bottom:5px;" class="translatable">
		{foreach from=$languages item=language}
			<div class="lang_{$language.id_lang}" style="{if !$language.is_default}display: none;{/if} float: left;">
				<input class="{if !$product->id}copy2friendlyUrl{/if} updateCurrentText" size="43" type="text"
					id="name_{$language.id_lang}" name="name_{$language.id_lang}"
					value="{$product->name[$language.id_lang]|htmlentitiesUTF8|default:''}"/><sup> *</sup>
				<span class="hint" name="help_box">{l s='Invalid characters:'} <>;=#{}<span class="hint-pointer">&nbsp;</span>
				</span>
			</div>
	{/foreach}

			</td>
					</tr>
					<tr>
						<td class="col-left"><label>{l s='Reference:' }</label></td>
						<td style="padding-bottom:5px;">
							<input size="55" type="text" name="reference" value="{$product->reference|htmlentitiesUTF8}" style="width: 130px; margin-right: 44px;" />
							<span class="hint" name="help_box">{l s='Special characters allowed:' }.-_#\<span class="hint-pointer">&nbsp;</span></span>
						</td>
					</tr>
					<!--tr>
						<td class="col-left"><label>{l s='Supplier Reference:' }</label></td>
						<td style="padding-bottom:5px;">
							<input size="55" type="text" name="supplier_reference" value="{$product->supplier_reference|htmlentitiesUTF8}" style="width: 130px; margin-right: 44px;" />
							<span class="hint" name="help_box">{l s='Special characters allowed:' } .-_#\<span class="hint-pointer">&nbsp;</span></span>
						</td>
					</tr-->
					<tr>
						<td class="col-left"><label>{l s='EAN13 or JAN:' }</label></td>
						<td style="padding-bottom:5px;">
							<input size="55" maxlength="13" type="text" name="ean13" value="{$product->ean13|htmlentitiesUTF8}" style="width: 130px; margin-right: 5px;" /> <span class="small">{l s='(Europe, Japan)'}</span>
						</td>
					</tr>
					<tr>
						<td class="col-left"><label>{l s='UPC:' }</label></td>
						<td style="padding-bottom:5px;">
							<input size="55" maxlength="12" type="text" name="upc" value="{$product->upc}" style="width: 130px; margin-right: 5px;" /> <span class="small">{l s='(US, Canada)'}</span>
						</td>
					</tr>
</table>
{* status informations *}
<table cellpadding="5" style="width: 40%; float: left; margin-left: 10px;">
<tr>
	<td class="col-left"><label>{l s='Status:' }</label></td>
	<td style="padding-bottom:5px;">
		<input style="float:left;" onclick="toggleDraftWarning(false);showOptions(true);" type="radio" name="active" id="active_on" value="1" {if $product->active}checked="checked" {/if} />
		<label for="active_on" class="t"><img src="../img/admin/enabled.gif" alt="{l s='Enabled'}"
			title="{l s='Enabled'}" style="float:left; padding:0px 5px 0px 5px;" />
		{l s='Enabled'}</label>
		<br class="clear" />
		<input style="float:left;" onclick="toggleDraftWarning(true);showOptions(false);"  type="radio" name="active" id="active_off" value="0" {if !$product->active}checked="checked"{/if} />
		<label for="active_off" class="t"><img src="../img/admin/disabled.gif" alt="{l s='Disabled'}" title="{l s='Disabled'}" style="float:left; padding:0px 5px 0px 5px" />{l s='Disabled'} </label>
	</td>
</tr>
	{if $feature_shop_active}
	{* @todo use asso_shop from Helper *}
	<tr id="shop_association">
		<td class="col-left"><label>{l s='Shop association:' }</label></td>
		<td style="padding-bottom:5px;">{$displayAssoShop}</td>
	</tr>
	{/if}
	<tr id="product_options" {if !$product->active}style="display:none"{/if} >
		<td class="col-left"><label>{l s='Options:' }</label></td>
		<td style="padding-bottom:5px;">
			<input style="float: left;" type="checkbox" name="available_for_order" id="available_for_order" value="1" {if $product->available_for_order}checked="checked"{/if}  />
			<script type="text/javascript">
			$(document).ready(function()
			{
				$("#available_for_order").click(function(){
					if ($(this).is(':checked'))
					{
						$('#show_price').attr('checked', 'checked');
						$('#show_price').attr('disabled', 'disabled');
					}
					else
					{
						$('#show_price').attr('disabled', '');
					}
				});
			});
			</script>
		<label for="available_for_order" class="t"><img src="../img/admin/products.gif" alt="{l s='available for order'}" title="{l s='available for order'}" style="float:left; padding:0px 5px 0px 5px" />{l s='available for order'}</label>
		<br class="clear" />
		<input style="float: left;" type="checkbox" name="show_price" id="show_price" value="1" {if $product->show_price}checked="checked"{/if} />
		<label for="show_price" class="t"><img src="../img/admin/gold.gif" alt="{l s='display price'}" title="{l s='show price'}" style="float:left; padding:0px 5px 0px 5px" />{l s='show price'}</label>
		<br class="clear" />
		<input style="float: left;" type="checkbox" name="online_only" id="online_only" value="1" {if $product->online_only}checked="checked"{/if} />
		<label for="online_only" class="t"><img src="../img/admin/basket_error.png" alt="{l s='online only'}" title="{l s='online only'}" style="float:left; padding:0px 5px 0px 5px" />{l s='online only (not sold in store)'}</label>
		</td>
	</tr>
	<tr>
	<td class="col-left"><label>{l s='Condition:' }</label></td>
	<td style="padding-bottom:5px;">
	<select name="condition" id="condition">
	<option value="new" {if $product->condition == 'new'}selected="selected"{/if} >{l s='New'}</option>
	<option value="used" {if $product->condition == 'used'}selected="selected"{/if} >{l s='Used'}</option>
	<option value="refurbished" {if $product->condition == 'refurbished'}selected="selected"{/if}>{l s='Refurbished'}</option>
	</select>
	</td>
	</tr>
	<tr>
	<td class="col-left"><label>{l s='Manufacturer:' }</label></td>
	<td style="padding-bottom:5px;">
	<select name="id_manufacturer" id="id_manufacturer">
	<option value="0">-- {l s='Choose (optional)'} --</option>
	{if $product->id_manufacturer}
	<option value="{$product->id_manufacturer}" selected="selected">{$product->manufacturer_name}</option>
	{/if}
	<option disabled="disabled">----------</option>
	</select>&nbsp;&nbsp;&nbsp;
	<a href="{$link->getAdminLink('AdminManufacturer')}&addmanufacturer" onclick="return confirm('{l s='Are you sure you want to delete product information entered?' js=1} ')">
	<img src="../img/admin/add.gif" alt="{l s='Create'}" title="{l s='Create'}" /> <b>{l s='Create'}</b>
	</a>
	</td>
	</tr>
	<!--tr>
	<td class="col-left"><label>{l s='Supplier:' }</label></td>
	<td style="padding-bottom:5px;">
	<select name="id_supplier" id="id_supplier">
	<option value="0">-- {l s='Choose (optional)'} --</option>
	{if $product->id_supplier}
	<option value="{$product->id_supplier}" selected="selected">{$product->supplier_name}</option>
	{/if}
	<option disabled="disabled">----------</option>
	</select>&nbsp;&nbsp;&nbsp;
	<a href="{$link->getAdminLink('AdminSuppliers')}&addsupplier" onclick="return confirm('{l s='Are you sure you want to delete entered product information?' js=1}">
	<img src="../img/admin/add.gif" alt="{l s='Create'}" title="{l s='Create'}" /> <b>{l s='Create'}</b>
	</a>
	</td>
	</tr-->
</table>
<div class="clear"></div>
<table cellpadding="5" cellspacing="0" border="0" style="width: 100%;"><tr><td><div class="separation"></div></td></tr></table>
{* [begin] pack product *}
<table>
	<tr>
		<td>
			<input type="checkbox" name="ppack" id="ppack" value="1" {if $is_pack}checked="checked"{/if} onclick="$('#ppackdiv').slideToggle();" />
			<label class="t" for="ppack">{l s='Pack'}</label>
		</td>
		<td>
			<div id="ppackdiv" {if !$is_pack}style="display: none;"{/if}>
				<div id="divPackItems">
				{foreach from=$product->packItems item=packItem}
					{$packItem->pack_quantity} x {$packItem->name}<span onclick="delPackItem({$packItem->id});" style="cursor: pointer;"><img src="../img/admin/delete.gif" /></span><br />
				{/foreach}
				</div>
				<input type="hidden" name="inputPackItems" id="inputPackItems" value="{$input_pack_items}" />

				<input type="hidden" name="namePackItems" id="namePackItems" value="{$input_namepack_items}" />

				<input type="hidden" size="2" id="curPackItemId" />

				<p class="clear">{l s='Begin typing the first letters of the product name, then select the product from the drop-down list:'}
				<br />{l s='You cannot add downloadable products to a pack.'}</p>
				<input type="text" size="25" id="curPackItemName" />
				<input type="text" name="curPackItemQty" id="curPackItemQty" value="1" size="1" />
				<span onclick="addPackItem();" style="cursor: pointer;"><img src="../img/admin/add.gif" alt="{l s='Add an item to the pack'}" title="{l s='Add an item to the pack'}" /></span>
			</td>
		</div>
	</tr>
</table>
<script language="javascript">
function addPackItem()
{
	if ($('#curPackItemId').val() == '' || $('#curPackItemName').val() == '')
	{
		alert('{l s='Thanks to select at least one product.'}');
		return false;
	}
	else if ($('#curPackItemId').val() == '' || $('#curPackItemQty').val() == '')
	{
		alert('{l s='Thanks to set a quantity to add a product.'}');
		return false;
	}

	var lineDisplay = $('#curPackItemQty').val()+ 'x ' +$('#curPackItemName').val();

	var divContent = $('#divPackItems').html();
	divContent += lineDisplay;
	divContent += '<span onclick="delPackItem(' + $('#curPackItemId').val() + ');" style="cursor: pointer;"><img src="../img/admin/delete.gif" /></span><br />';

	// QTYxID-QTYxID
	var line = $('#curPackItemQty').val()+ 'x' +$('#curPackItemId').val();


	$('#inputPackItems').val($('#inputPackItems').val() + line  + '-');
	$('#divPackItems').html(divContent);
		$('#namePackItems').val($('#namePackItems').val() + lineDisplay + 'Â¤');

	$('#curPackItemId').val('');
	$('#curPackItemName').val('');

	$('#curPackItemName').setOptions({
		extraParams: {
			excludeIds :  getSelectedIds()
		}
	});
}
function delPackItem(id)
{
	var reg = new RegExp('-', 'g');
	var regx = new RegExp('x', 'g');

	var div = getE('divPackItems');
	var input = getE('inputPackItems');
	var name = getE('namePackItems');
	var select = getE('curPackItemId');
	var select_quantity = getE('curPackItemQty');

	var inputCut = input.value.split(reg);
	var nameCut = name.value.split(new RegExp('¤', 'g'));

	input.value = '';
	name.value = '';
	div.innerHTML = '';

	for (var i = 0; i < inputCut.length; ++i)
		if (inputCut[i])
		{
			var inputQty = inputCut[i].split(regx);
			if (inputQty[1] != id)
			{
				input.value += inputCut[i] + '-';
				name.value += nameCut[i] + '¤';
				div.innerHTML += nameCut[i] + ' <span onclick="delPackItem(' + inputQty[1] + ');" style="cursor: pointer;"><img src="../img/admin/delete.gif" /></span><br />';
			}
		}

	$('#curPackItemName').setOptions({
		extraParams: {
			excludeIds :  getSelectedIds()
		}
	});
}

	/* function autocomplete */
	urlToCall = null;
	function getSelectedIds()
	{
		// input lines QTY x ID-
		var ids = {$product->id}+',';
		ids += $('#inputPackItems').val().replace(/\\d+x/g, '').replace(/\-/g,',');
		ids = ids.replace(/\,$/,'');
		return ids;
	}

	$(function() {
		$('#curPackItemName')
			.autocomplete('ajax_products_list.php', {
				delay: 100,
				minChars: 1,
				autoFill: true,
				max:20,
				matchContains: true,
				mustMatch:true,
				scroll:false,
				cacheLength:0,
				{* param multipleSeparator:'||' ajouté à cause de bug dans lib autocomplete *}
				multipleSeparator:'||',
				formatItem: function(item) {
					return item[1]+' - '+item[0];
				}
			}).result(function(event, item){
				$('#curPackItemId').val(item[1]);
			});
			$('#curPackItemName').setOptions({
				extraParams: {
					excludeIds : getSelectedIds(), excludeVirtuals : 1
				}
			});

	});
</script>
{* [end] pack product *}

{* [begin] specific / detailled information *}

<div class="clear"></div>
<script type="text/javascript">
var newLabel = '{l s='New label'}';
var choose_language = '{l s='Choose language:'}';
var required = '{l s='required'}';
var customizationUploadableFileNumber = '{$product->uploadable_files}';
var customizationTextFieldNumber = '{$product->text_fields}';
var uploadableFileLabel = 0;
var textFieldLabel = 0;
$(document).ready(function(){
	$("#is_virtual_good").change(function(e)
	{
		$(".toggleVirtualPhysicalProduct").toggle();
	});
});
</script>
<table cellpadding="5" cellspacing="0" border="0" style="width: 100%;"><tr><td><div class="separation"></div></td></tr></table>
<table cellpadding="5" cellspacing="0" border="0" style="width: 100%;">
<tr>
	<td colspan="2">
		<p><input type="checkbox" id="is_virtual_good" name="is_virtual_good" value="true" {*onclick="toggleVirtualProduct(this);"*} {if $product->is_virtual && $product->productDownload->active}checked="checked"{/if} />
			<label for="is_virtual_good" class="t bold" style="color: black;">{l s='Is this a virtual product?'}</label>
		</p>
		{* [begin] physical product infos *}
		<div id="physical_good" class="toggleVirtualPhysicalProduct" {if $product->productDownload->id && $product->productDownload->active}style="display:none"{/if} >
		<table cellpadding="5" cellspacing="0" border="0" style="width: 100%;">
		<tr><td><div class="separation"></div></td></tr>
		</table>
		<table cellpadding="5" style="width: 50%; float: left; margin-right: 20px; border-right: 1px solid #E0D0B1;">
			<tr>
				<td class="col-left"><label>{l s='Width ( package ) :' }</label></td>
				<td style="padding-bottom:5px;">
					<input size="6" maxlength="6" name="width" type="text" value="{$product->width}" onKeyUp="if (isArrowKey(event)) return ;this.value = this.value.replace(/,/g, '.');" /> {$ps_dimension_unit}
				</td>
			</tr>
			<tr>
				<td class="col-left"><label>{l s='Height ( package ) :' }</label></td>
				<td style="padding-bottom:5px;">
					<input size="6" maxlength="6" name="height" type="text" value="{$product->height}" onKeyUp="if (isArrowKey(event)) return ;this.value = this.value.replace(/,/g, '.');" /> {$ps_dimension_unit}
				</td>
			</tr>
			<tr>
				<td class="col-left">
					<label>Carriers:</label>
				</td>
				<td class="padding-bottom:5px;">
					<select name="carriers[]" multiple="multiple" size="4">
						{foreach $carrier_list as $carrier}
							<option value="{$carrier.id_reference}" {if isset($carrier.selected) && $carrier.selected}selected="selected"{/if}>{$carrier.name}</option>
						{/foreach}
					</select>
				</td>
			</tr>
		</table>
		<table cellpadding="5" style="width: 40%; float: left; margin-left: 10px;">
			<tr>
				<td class="col-left"><label>{l s='Deep ( package ) :' }</label></td>
				<td style="padding-bottom:5px;">
					<input size="6" maxlength="6" name="depth" type="text" value="{$product->depth}" onKeyUp="if (isArrowKey(event)) return ;this.value = this.value.replace(/,/g, '.');" /> {$ps_dimension_unit}
				</td>
			</tr>
			<tr>
				<td class="col-left"><label>{l s='Weight ( package ) :' }</label></td>
				<td style="padding-bottom:5px;">
					<input size="6" maxlength="6" name="weight" type="text" value="{$product->weight}" onKeyUp="if (isArrowKey(event)) return ;this.value = this.value.replace(/,/g, '.');" /> {$ps_weight_unit}
				</td>
			</tr>
		</table>
		</div>
		{* [end] of physical product *}
		{* [begin] virtual product *}
		<div id="virtual_good" class="toggleVirtualPhysicalProduct" {if !$product->productDownload->id || $product->productDownload->active}style="display:none"{/if} >
			<input type="hidden" id="is_virtual" name="is_virtual" value="{$product->is_virtual}" />
			<table cellpadding="5" style="width: 50%; float: left; margin-right: 20px; border-right: 1px solid #E0D0B1;">
				<tr><td>
					<br/>{l s='Does this product has an associated file ?'}<br />
					<input type="radio" value="1" id="virtual_good_file_1" name="is_virtual_file" {if $product_downloaded}checked="checked"{/if} />{l s='Yes'}
					<input type="radio" value="0" id="virtual_good_file_2" name="is_virtual_file" {if !$product_downloaded}checked="checked"{/if} />{l s='No'}<br /><br />
					{if $download_product_file_missing}
						<p class="alert" id="file_missing">
							<b>{$download_product_file_missing} :<br/>
							{$smarty.const._PS_DOWNLOAD_DIR_}/{$product->productDownload->filename}</b>
						</p>
					{/if}
				</td></tr>
			<tr>
				<td>
					<div id="is_virtual_file_product" style="display:none;">
					{if !$download_dir_writable}
						<p class="alert">
							{l s='Your download repository is not writable.'}<br/>
							{$smarty.const._PS_DOWNLOAD_DIR_}
						</p>
					{/if}
					{if empty($product->cache_default_attribute)}
						{if $product->productDownload->id}
							<input type="hidden" id="virtual_product_id" name="virtual_product_id" value="{$product->productDownload->id}" />
						{/if}
						<p class="block">
						{if !$product->productDownload->checkFile()}
							<div style="padding:5px;width:50%;float:left;margin-right:20px;border-right:1px solid #E0D0B1">
							<p>{l s='Your server\'s maximum upload file size is'}:&nbsp;{$upload_max_filesize}</p>
							{if $show_file_input}
								<label id="virtual_product_file_label" for="virtual_product_file" class="t">{l s='Upload a file'}</label>
								<p><input type="file" id="virtual_product_file" name="virtual_product_file" onchange="uploadFile();" maxlength="{$upload_max_filesize}" /></p>
							{/if}
							<div id="upload-confirmation">
								{if $up_filename}
									<input type="hidden" id="virtual_product_filename" name="virtual_product_filename" value="{$up_filename}" />
								{/if}
							</div>
							<a id="delete_downloadable_product" style="display:none;" onclick="return confirm('{l s='Delete this file' slashes=1 js=1}')" href="{$currentIndex}&deleteVirtualProduct=true&token={$token}&id_product={$product->id}" class="red">
								{l s='Delete this file'}
							</a>
							</div>
						{else}
							<input type="hidden" id="virtual_product_filename" name="virtual_product_filename" value="{$product->productDownload->filename}" />
							{l s='This is the link'}:&nbsp;{$product->productDownload->getHtmlLink(false, true)}
							<a onclick="return confirm('{l s='Delete this file' slashes=1 js=1})')" href="{$currentIndex}&deleteVirtualProduct=true&token={$token}&id_product={$product->id}" class="red">{l s='Delete this file'}</a>
						{/if}
						</p>

						<p class="block">
							<label for="virtual_product_name" class="t">{l s='Filename'}</label>
							<input type="text" id="virtual_product_name" name="virtual_product_name" style="width:200px" value="{$product->productDownload->display_filename|htmlentitiesUTF8}" />
							<span class="hint" name="help_box" style="display:none;">{l s='The full filename with its extension (e.g., Book.pdf)'}</span>
						</p>
					</div>
				</td></tr>
			</table>
			<table cellpadding="5" style="width: 40%; float: left; margin-left: 10px;">
				<tr><td>
					<div id="virtual_good_more" style="'.$hidden.'padding:5px;width:40%;float:left;margin-left:10px">
						<p class="block">
							<label for="virtual_product_nb_downloable" class="t">{l s='Number of downloads'}</label>
							<input type="text" id="virtual_product_nb_downloable" name="virtual_product_nb_downloable" value="{$product->productDownload->nb_downloadable}" class="" size="6" />
							<span class="hint" name="help_box" style="display:none">{l s='Number of authorized downloads per customer'}</span>
						</p>
						<p class="block">
							<label for="virtual_product_expiration_date" class="t">{l s='Expiration date'}</label>
							<input class="datepicker" type="text" id="virtual_product_expiration_date" name="virtual_product_expiration_date" value="{$product->productDownload->date_expiration}" size="11" maxlength="10" autocomplete="off" /> {l s='Format: YYYY-MM-DD'}
							<span class="hint" name="help_box" style="display:none">{l s='No expiration date if you leave this blank'}</span>
						</p>
						<p class="block">
							<label for="virtual_product_nb_days" class="t">{l s='Number of days'}</label>
							<input type="text" id="virtual_product_nb_days" name="virtual_product_nb_days" value="{$product->productDownload->nb_days_accessible}" class="" size="4" /><sup> *</sup>
							<span class="hint" name="help_box" style="display:none">{l s='How many days this file can be accessed by customers'} - <em>({l s='set to zero for unlimited access'})</em></span>
						</p>
						<p class="block">
							<label for="virtual_product_is_shareable" class="t">{l s='is shareable'}</label>
							<input type="checkbox" id="virtual_product_is_shareable" name="virtual_product_is_shareable" value="1" {if $product->productDownload->is_shareable}checked="checked"{/if} />
							<span class="hint" name="help_box" style="display:none">{l s='Specify if the file can be shared'}</span>
						</p>
					</div>
					{else}
					<div class="hint clear" style="display: block;width: 70%;">{l s='You used combinations, for this reason you can\'t edit your file here, but in the Combinations tab'}</div>
					<br />
						{$error_product_download}
					{/if}
				</td></tr>
			</table>
		</div>
		<script type="text/javascript">
			$(document).ready(function(){
				if ($("#is_virtual_good").attr("checked"))
				{
					$("#virtual_good").show();
					$("#virtual_good_more").show();
				}

				if ( $("input[name=is_virtual_file]:checked").val() == 1)
				{
					$("#virtual_good_more").show();
					$("#virtual_good_attributes").show();
					$("#is_virtual_file_product").show();
				}
				else
				{
					$("#virtual_good_more").hide();
					$("#virtual_good_attributes").hide();
					$("#is_virtual_file_product").hide();
				}

				$("input[name=is_virtual_file]").live("change", function() {
					if($(this).val() == "1")
					{
						$("#virtual_good_more").show();
						$("#virtual_good_attributes").show();
						$("#is_virtual_file_product").show();
					}
					else
					{
						$("#virtual_good_more").hide();
						$("#virtual_good_attributes").hide();
						$("#is_virtual_file_product").hide();
					}
				});

				$("input[name=is_virtual_good]").live("change", function() {
					if($(this).attr("checked"))
					{
						$("#is_virtual").val(1);
					}
					else
					{
						$("#is_virtual").val(0);
					}
				});
			});
		</script>
	</td>
</tr>
<tr>
	<td colspan="2" style="padding-bottom:5px;"><div class="separation"></div></td>
 </tr>

	{* @todo : prices related has to be moved in price subtab *}
	<tr>
		<td class="col-left"><label>{l s='Pre-tax wholesale price:'}</label></td>
		<td style="padding-bottom:5px;">
			{$currency->prefix}<input size="11" maxlength="14" name="wholesale_price" type="text" value="{$product->wholesale_price}" onchange="this.value = this.value.replace(/,/g, '.');" />{$currency->suffix}
			<span style="margin-left:10px">{l s='The wholesale price at which you bought this product'}</span>
		</td>
	</tr>

	<tr>
		<td class="col-left"><label>{l s='Pre-tax retail price:'}</label></td>
		<td style="padding-bottom:5px;">
			{$currency->prefix}<input size="11" maxlength="14" id="priceTE" name="price" type="text" value="{$product->price}" onchange="this.value = this.value.replace(/,/g, '.');" onkeyup="if (isArrowKey(event)) return; calcPriceTI();" />{$currency->suffix}<sup> *</sup>
			<span style="margin-left:2px">{l s='The pre-tax retail price to sell this product'}</span>
		</td>
	</tr>
	<tr>
		<td class="col-left"><label>{l s='Tax rule:' }</label></td>
		<td style="padding-bottom:5px;">
			<script type="text/javascript">
			noTax = {if $tax_exclude_taxe_option}true{else}false{/if};
			taxesArray = new Array ();
			taxesArray[0] = 0;
			{foreach from=$tax_rules_groups item=tax_rules_group}
				{if isset($tax_rules_group['id_tax_rules_group'][$taxesRatesByGroup])}
					taxesArray[{$tax_rules_group.id_tax_rules_group}] = {$tax_rules_group.id_tax_rules_group[$taxesRatesByGroup]};
				{else}
					taxesArray[{$tax_rules_group.id_tax_rules_group}] = 0;
				{/if}
			{/foreach}
			ecotaxTaxRate = {$ecotaxTaxRate / 100};
			</script>

					<span {if $tax_exclude_taxe_option}style="display:none;"{/if} >
					 <select onChange="javascript:calcPriceTI(); unitPriceWithTax('unit');" name="id_tax_rules_group" id="id_tax_rules_group" {if $tax_exclude_taxe_option}disabled="disabled"{/if} >
					     <option value="0">{l s='No Tax'}</option>
						{foreach from=$tax_rules_groups item=tax_rules_group}
							<option value="{$tax_rules_group.id_tax_rules_group}" {if $product->id_tax_rules_group == $tax_rules_group.id_tax_rules_group}selected="selected"{/if} >
								{$tax_rules_group['name']|htmlentitiesUTF8}
							</option>
						{/foreach}
						</select>
						<a href="{$link->getAdminLink('AdminTaxRulesGroup')}&addtax_rules_group&id_product={$product->id}" onclick="return confirm('{l s='Are you sure you want to delete entered product information?'}'" >
						<img src="../img/admin/add.gif" alt="{l s='Create'}" title="{l s='Create'}" /> <b>{l s='Create'}</b>
						</a>
					</span>
					{if $tax_exclude_taxe_option}
						<span style="margin-left:10px; color:red;">{l s='Taxes are currently disabled'}</span> (<b><a href="{$link->getAdminLink('AdminTaxes')}">{l s='Tax options'}</a></b>)
						<input type="hidden" value="{$product->id_tax_rules_group}" name="id_tax_rules_group" />
					{/if}
				</td>
			</tr>
			{if $ps_use_ecotax}
			<tr>
				<td class="col-left"><label>{l s='Eco-tax (tax incl.):' }</label></td>
				<td style="padding-bottom:5px;">
					{$currency->prefix}<input size="11" maxlength="14" id="ecotax" name="ecotax" type="text" value="{$product->ecotax}" onkeyup="if (isArrowKey(event))return; calcPriceTE(); this.value = this.value.replace(/,/g, '.'); if (parseInt(this.value) > getE('priceTE').value) this.value = getE('priceTE').value; if (isNaN(this.value)) this.value = 0;" />{$currency->suffix}
					<span style="margin-left:10px">({l s='already included in price'})</span>
				</td>
			</tr>
			{/if}
			<tr {if !$country_display_tax_label || $tax_exclude_taxe_option}style="display:none"{/if} >
				<td class="col-left"><label>{l s='Retail price with tax:' }</label></td>
				<td style="padding-bottom:5px;">
					{$currency->prefix}<input size="11" maxlength="14" id="priceTI" type="text" value="" onchange="noComma('priceTI');" onkeyup="if (isArrowKey(event)) return;  calcPriceTE();" />{$currency->suffix}
				</td>
			</tr>
			<tr id="tr_unit_price">
				<td class="col-left"><label>{l s='Unit price without tax:' }</label></td>
				<td style="padding-bottom:5px;">
					{$currency->prefix} <input size="11" maxlength="14" id="unit_price" name="unit_price" type="text" value="{$product->unit_price}"
						onkeyup="if (isArrowKey(event)) return ;this.value = this.value.replace(/,/g, '.'); unitPriceWithTax('unit');"/>{$currency->suffix}
						{l s='per'} <input size="6" maxlength="10" id="unity" name="unity" type="text" value="{$product->unity|htmlentitiesUTF8}" onkeyup="if (isArrowKey(event)) return ;unitySecond();" onchange="unitySecond();"/>
							{if $ps_tax && $country_display_tax_label}
								<span style="margin-left:15px">{l s='or'}
									{$currency->prefix}<span id="unit_price_with_tax">0.00</span>{$currency->suffix}
									{l s='per'} <span id="unity_second">{$product->unity}</span> {l s='with tax'}
								</span>
							{/if}
							<p>{l s='Eg. $15 per Lb'}</p>
						</td>
					</tr>
					<tr>
						<td class="col-left"><label>&nbsp;</label></td>
						<td style="padding-bottom:5px;">
							<input type="checkbox" name="on_sale" id="on_sale" style="padding-top: 5px;" {if $product->on_sale}checked="checked"{/if} value="1" />&nbsp;<label for="on_sale" class="t">{l s='Display "on sale" icon on product page and text on product listing'}</label>
						</td>
					</tr>
					<tr>
						<td class="col-left"><label><b>{l s='Final retail price:'}</b></label></td>
						<td style="padding-bottom:5px;">
							<span {if !$country_display_tax_label}style="display:none"{/if} >
							{$currency->prefix}<span id="finalPrice" style="font-weight: bold;"></span>{$currency->suffix}<span {if $ps_tax}style="display:none;"{/if}> ({l s='tax incl.'})</span>
							</span>
							<span {if $ps_tax}style="display:none;"{/if} >

							{if $country_display_tax_label}
								 /
							{/if}
							{$currency->prefix}<span id="finalPriceWithoutTax" style="font-weight: bold;"></span>{$currency->suffix} {if $country_display_tax_label}({l s='tax excl.'}){/if}</span>
						</td>
					</tr>
					<tr>
						<td class="col-left"><label>&nbsp;</label></td>
						<td>
							<div class="hint clear" style="display: block;width: 70%;">{l s='You can define many discounts and specific price rules in the Prices tab'}</div>
						</td>
					</tr>
					{* [end] prices *}


<tr><td colspan="2" style="padding-bottom:5px;"><div class="separation"></div></td></tr>
				{if !$ps_stock_management}
						<tr>
							<td colspan="2">{l s='The stock management is disabled'}</td>
						</tr>
					{/if}
					{if !$has_attribute}
					<tr>
						<td class="col-left"><label>{l s='Minimum quantity:'}</label></td>
						<td style="padding-bottom:5px;">
							<input size="3" maxlength="6" name="minimal_quantity" id="minimal_quantity" type="text" value="{$product->minimal_quantity|default:1}" />
							<p>{l s='The minimum quantity to buy this product (set to 1 to disable this feature)'}</p>
						</td>
					</tr>
				{/if}
					<tr><td colspan="2" style="padding-bottom:5px;"><div class="separation"></div></td></tr>
				<tr>
					<td class="col-left"><label>{l s='Additional shipping cost:'}</label></td>
					<td style="padding-bottom:5px;">{$currency->prefix}<input type="text" name="additional_shipping_cost"
							value="{$product->additional_shipping_cost}" />{$currency->suffix}
						{if $country_display_tax_label}{l s='tax excl.'}{/if}
						<p>{l s='Carrier tax will be applied.'}</p>
				</td>
			</tr>
					<tr>
						<td class="col-left"><label>{l s='Displayed text when in-stock:'}</label></td>
						<td style="padding-bottom:5px;">
								{include file="products/input_text_lang.tpl"
									languages=$languages
									input_value=$product->available_now
									input_name='available_now'}
							<span class="hint" name="help_box">{l s='Forbidden characters:'} <>;=#{}<span class="hint-pointer">&nbsp;</span></span>
					</td>
					</tr>
					<tr>
						<td class="col-left"><label>{l s='Displayed text when allowed to be back-ordered:'}</label></td>
						<td style="padding-bottom:5px;">
								{include file="products/input_text_lang.tpl"
									languages=$languages
									input_value=$product->available_later
									input_name='available_later'}
							<span class="hint" name="help_box">{l s='Forbidden characters:'} <>;=#{}<span class="hint-pointer">&nbsp;</span></span>
				</td>
					</tr>
			{if $countAttributes}

{* .(($this->getFieldValue($product, 'available_date') != 0) ? stripslashes(htmlentities(Tools::displayDate($this->getFieldValue($product, 'available_date'), $language['id_lang']))) : '0000-00-00').'" *}
						<tr>
							<td class="col-left"><label>{l s='Available date:'}</label></td>
							<td style="padding-bottom:5px;">
							<input id="available_date" name="available_date" value="{$product->available_date}" class="datepicker"
							style="text-align: center;" type="text" />
								<p>{l s='The available date when this product is out of stock'}</p>
						</td>
						</tr>
			{/if}
					<script type="text/javascript">
						calcPriceTI();
					</script>
					<tr>
						<td class="col-left"><label>{l s='When out of stock:'}</label></td>
						<td style="padding-bottom:5px;">
							<input type="radio" name="out_of_stock" id="out_of_stock_1" value="0"  {if $product->out_of_stock == 0}checked="checked"{/if} />
								<label for="out_of_stock_1" class="t" id="label_out_of_stock_1">{l s='Deny orders'}</label>
							<br /><input type="radio" name="out_of_stock" id="out_of_stock_2" value="1" {if $product->out_of_stock == 1}checked="checked"{/if} />
								<label for="out_of_stock_2" class="t" id="label_out_of_stock_2">{l s='Allow orders'}</label>
							<br /><input type="radio" name="out_of_stock" id="out_of_stock_3" value="2" {if $product->out_of_stock == 2}checked="checked"{/if} />
								<label for="out_of_stock_3" class="t" id="label_out_of_stock_3">{l s='Default:'}
								<i>{if $ps_order_out_of_stock}{l s='Allow orders'}{else}{l s='Deny orders'}{/if}</i> ({l s='as set in'} <a href="{$link->getAdminLink('AdminPPreferences')}"
									onclick="return confirm(\'{l s='Are you sure you want to delete entered product information?'}')">{l s='Preferences'}</a>)</label>
						</td>
					</tr>
					<tr>
						<td colspan="2" style="padding-bottom:5px;">
							<div class="separation"></div>
						</td>
					</tr>

					<tr>
						<td class="col-left"><label for="id_category_default" class="t">
							{l s='Default category:'}
							</label></td>
						<td>
						<div id="no_default_category" style="color: red;font-weight: bold;display: none;">
							{l s='Please check a category in order to select the default category.'}
						</div>
						<script type="text/javascript">
							var post_selected_cat;
							post_selected_cat = '{$selected_cat_ids}';
						</script>
						<select id="id_category_default" name="id_category_default">
						{foreach from=$selected_cat item=cat}
							<option value="{$cat.id_category}" {if $product->id_category_default == $cat.id_category}selected="selected"{/if} >{$cat.name}</option>
						{/foreach}
						</select>
						</td>
					</tr>
					<tr><td colspan="2">{$category_tree}</td></tr>
					<tr><td colspan="2" style="padding-bottom:5px;"><div class="separation"></div></td></tr>
{************** DESCRIPTION *****************************}
				<tr><td colspan="2">
					<span onclick="$('#seo').slideToggle();" style="cursor: pointer"><img src="../img/admin/arrow.gif" alt="{l s='SEO'}" title="{l s='SEO'}" style="float:left; margin-right:5px;"/>{l s='Click here to improve product\'s rank in search engines (SEO)'}</span><br />
					<div id="seo" style="display: none; padding-top: 15px;">
					<table>
						<tr>
						<td class="col-left"><label>{l s='Meta title:'}</label></td>
						<td>
							{include file="products/input_text_lang.tpl"
								languages=$languages
								input_name='meta_title'
								input_value=$product->meta_title}
								<p class="clear">{l s='Product page title; leave blank to use product name'}</p>
						</td>
						</tr>
						<tr>
							<td class="col-left"><label>{l s='Meta description:'}</label></td>
							<td>
								{include file="products/input_text_lang.tpl"
									languages=$languages
									input_name='meta_description'
									input_value=$product->meta_description
									input_hint='{l s=\'Forbidden characters:\'\} <>;=#{\}'
								}
								<p class="clear">{l s='A single sentence for HTML header'}</p>
							</td>
						</tr>
						<tr>
							<td class="col-left"><label>{l s='Meta keywords:'}</label></td>
							<td>
							{include file="products/input_text_lang.tpl" languages=$languages
							input_value=$product->meta_keywords
							input_name='meta_keywords'}
								<p class="clear">{l s='Keywords for HTML header, separated by a comma'}</p>
									</td>
								</tr>
								<tr>
								<td class="col-left"><label>{l s='Friendly URL:'}</label></td>
									<td>
								{include file="products/input_text_lang.tpl"
									languages=$languages
									input_value=$product->link_rewrite
									input_name='link_rewrite'}

								<p class="clear" style="padding:10px 0 0 0">
									<a style="cursor:pointer" class="button"
									onmousedown="updateFriendlyURLByName();">{l s='Generate'}</a>&nbsp;{l s='Friendly-url from product\'s name.'}<br /><br />
								{l s='Product link will look like this:'}
								{if $ps_ssl_enabled}https://{else}http://{/if}{*$smarty.server.SERVER_NAME*}/<b>id_product</b>-<span id="friendly-url"></span>.html</p>
									</td>
								</tr>
		</td></tr></table>
						</div>
					</td></tr>
					<tr><td colspan="2" style="padding-bottom:5px;"><div class="separation"></div></td></tr>
					<tr>
						<td class="col-left"><label>{l s='Short description:'}<br /><br /><i>({l s='appears in the product lists and on the top of the product page'})</i></label></td>
						<td style="padding-bottom:5px;">
								{include file="products/textarea_lang.tpl"
								languages=$languages
								input_name='description_short'
								input_value=$product->description_short}

		<p class="clear"></p>
			</td>
					</tr>
					<tr>
						<td class="col-left"><label>{l s='Description:'}<br /><br /><i>({l s='appears in the body of the product page'})</i></label></td>
						<td style="padding-bottom:5px;">
								{include file="products/textarea_lang.tpl" languages=$languages
								input_name='description'
								input_value=$product->description
								}
		<p class="clear"></p>
					</td>
					</tr>

{if $images}

					<tr>
						<td class="col-left"><label></label></td>
						<td style="padding-bottom:5px;">
							<div style="display:block;width:620px;" class="hint clear">
								{l s='Do you want an image associated with the product in your description?'}
								<span class="addImageDescription" style="cursor:pointer">{l s='Click here'}</span>.
								<table id="createImageDescription" style="display:none;">
									<tr>
										<td colspan="2" height="10"></td>
									</tr>
									<tr>
										<td class="col-left"><label>{l s='Select your image:'}</label></td>
										<td style="padding-bottom:5px;">
											<ul>
											{foreach from=$images item=image key=key}
													<li>
														<input type="radio" name="smallImage" id="smallImage_{$key}" value="{$image.id_image}" {if $key == 0}checked="checked"{/if} >
														<label for="smallImage_{$key}" class="t">
															<img src="{$image.src}" alt="{$image.legend}" />
														</label>
													</li>
											{/foreach}
											</ul>
											<p class="clear"></p>
										</td>
									</tr>
									<tr>
										<td class="col-left"><label>{l s='Where to place it?'}</label></td>
										<td style="padding-bottom:5px;">
											<input type="radio" name="leftRight" id="leftRight_1" value="left" checked>
											<label for="leftRight_1" class="t">{l s='left'}</label>
											<br />
											<input type="radio" name="leftRight" id="leftRight_2" value="right">
											<label for="leftRight_2" class="t">{l s='right'}</label>
											<p class="clear"></p>
										</td>
									</tr>
									<tr>
										<td class="col-left"><label>{l s='Select the type of picture:'}</label></td>
										<td style="padding-bottom:5px;">
											{foreach from=$imagesTypes key=key item=type}
													<input type="radio" name="imageTypes" id="imageTypes_{$key}" value="{$type.name}" {if $key == 0}checked="checked"{/if}>
													<label for="imageTypes_{$key}" class="t">{$type.name} <span>({$type.width}px par {$type.height}px)</span></label>
													<br />
											{/foreach}

											<p class="clear"></p>
										</td>
									</tr>

									<tr>
										<td class="col-left"><label>{l s='Image tag to insert:'}</label></td>
										<td style="padding-bottom:5px;">
											<input type="text" id="resultImage" name="resultImage" />
											<p>{l s='The tag is to copy / paste in the description.'}</p>
										</td>
									</tr>
								</table>
							</div>
							<p class="clear"></p>
						</td>
					</tr>

					<script type="text/javascript">
						$(function() {
							changeTagImage();
							$("#createImageDescription input").change(function(){
								changeTagImage();
							});

							var i = 0;
							$(".addImageDescription").click(function(){
								if (i == 0){
									$("#createImageDescription").animate({
										opacity: 1, height: "toggle"
										}, 500);
									i = 1;
								}else{
									$("#createImageDescription").animate({
										opacity: 0, height: "toggle"
										}, 500);
									i = 0;
								}
							});
						});

						function changeTagImage(){
							var smallImage = $("input[name=smallImage]:checked").attr("value");
							var leftRight = $("input[name=leftRight]:checked").attr("value");
							var imageTypes = $("input[name=imageTypes]:checked").attr("value");
							$("#resultImage").val("{img-"+smallImage+"-"+leftRight+"-"+imageTypes+"}");
						}
					</script>
{/if}

				<tr>
					<td class="col-left"><label>{l s='Tags:'}</label></td>
					<td style="padding-bottom:5px;" class="translatable">

			{foreach from=$languages item=language}
				<div class="lang_{$language.id_lang}" style="{if !$language.is_default}display: none;{/if}float: left;">
						<input size="55" type="text" id="tags_{$language.id_lang}" name="tags_{$language.id_lang}"
						value="{$product->getTags($language.id_lang, true)|htmlentitiesUTF8}" />
						<span class="hint" name="help_box">{l s='Forbidden characters:'} !&lt;;&gt;;?=+#&quot;&deg;{}_$%<span class="hint-pointer">&nbsp;</span></span>
						</div>
			{/foreach}
				<p class="clear">{l s='Tags separated by commas (e.g., dvd, dvd player, hifi)'}</p>
					</td>
				</tr>
				
				<tr>
					<td class="col-left"><label>{l s='Accessories:'}<br /><br /><i>{l s='(Do not forget to Save the product afterward)'}</i></label></td>
					<td style="padding-bottom:5px;">
						<div id="divAccessories">
				{* @todo : donot use 3 foreach, but assign var *}
				{foreach from=$accessories item=accessory}
					{$accessory.name|htmlentitiesUTF8}{if !empty($accessory.reference)}{$accessory.reference}{/if} <span onclick="delAccessory({$accessory.id_product});" style="cursor: pointer;"><img src="../img/admin/delete.gif" class="middle" alt="" /></span><br />
				{/foreach}
				</div>
				<input type="hidden" name="inputAccessories" id="inputAccessories" value="{foreach from=$accessories item=accessory}{$accessory.id_product}-{/foreach}" />
				<input type="hidden" name="nameAccessories" id="nameAccessories" value="{foreach from=$accessories item=accessory}{$accessory.name|htmlentitiesUTF8}¤{/foreach}" />
<script type="text/javascript">
var formProduct;
var accessories = new Array();
</script>
						
						<div id="ajax_choose_product" style="padding:6px; padding-top:2px; width:600px;">
							<p class="clear">{l s='Begin typing the first letters of the product name, then select the product from the drop-down list:'}</p>
							<input type="text" value="" id="product_autocomplete_input" />
							<img onclick="$(this).prev().search();" style="cursor: pointer;" src="../img/admin/add.gif" alt="{l s='Add an accessory'}" title="{l s='Add an accessory'}" />
						</div>
						<script type="text/javascript">
							urlToCall = null;
							/* function autocomplete */
							$(document).ready(function() {
								$('#product_autocomplete_input')
									.autocomplete('ajax_products_list.php', {
										minChars: 1,
										autoFill: true,
										max:20,
										matchContains: true,
										mustMatch:true,
										scroll:false,
										cacheLength:0,
										formatItem: function(item) {
											return item[1]+' - '+item[0];
										}
									}).result(addAccessory);
								$('#product_autocomplete_input').setOptions({
									extraParams: {
										excludeIds : getAccessorieIds()
									}
								});
							});

							function getAccessorieIds()
							{
								var ids = {$product->id}+',';
								ids += $('#inputAccessories').val().replace(/\\-/g,',').replace(/\\,$/,'');
								ids = ids.replace(/\,$/,'');

								return ids;
							}
						</script>
					</td>
				</tr>
				<tr><td colspan="2" style="padding-bottom:10px;"><div class="separation"></div></td></tr>
			</table>
		<br />
		</div>

		
			<script type="text/javascript">
					toggleVirtualProduct(getE('is_virtual_good'));
					unitPriceWithTax('unit');
			</script>


<script type="text/javascript">
	var iso = '{$iso_tiny_mce}';
	var pathCSS = '{$smarty.const._THEME_CSS_DIR_}';
	var ad = '{$ad}';
</script>
<script type="text/javascript" src="../js/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript" src="../js/tinymce.inc.js"></script>
