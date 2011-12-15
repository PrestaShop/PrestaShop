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

<script type="text/javascript">

	$(document).ready(function() {
		$('input').keypress(function(e) { 
			var code = null; 
			code = (e.keyCode ? e.keyCode : e.which);
			return (code == 13) ? false : true;
		});
	});

</script>

<div id="step1">
	<h4 class="tab">1. {l s='Info.'}</h4>
	<h4>{l s='Product global information'}</h4>
	<script type="text/javascript">
		var token = '{$token}';
		var id_product = {if isset($product->id)}{$product->id}{else}0{/if};
		var tabs_preloaded = new Array();

		{$combinationImagesJs}
		$(document).ready(function(){

			$('#id_mvt_reason').change(function(){
				updateMvtStatus($(this).val());
			});
			updateMvtStatus($(this).val());

			{foreach $tabs_preloaded as $k => $tab}
				tabs_preloaded['{$k}'] = '{$tab}';
			{/foreach}

			$('.product-tab-content').each(function(){
				var id = $(this).attr('id').substr(20);
				var split_position = id.indexOf('-') + 1;
				var btn_name = id.substr(split_position);

			if (tabs_preloaded[btn_name])
				if ($("#product-tab-content-"+id).hasClass('not-loaded'))
					displayTabProductById('#link-'+id, id, false);
			});

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
<table cellpadding="5" style="width: 50%; float: left; margin-right: 20px; border-right: 1px solid #CCCCCC;">
{* global information *}
	<tr>
		<td class="col-left"><label>{l s='Name:'}</label></td>
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
						<td class="col-left"><label>{l s='Reference:'}</label></td>
						<td style="padding-bottom:5px;">
							<input size="55" type="text" name="reference" value="{$product->reference|htmlentitiesUTF8}" style="width: 130px; margin-right: 44px;" />
							<span class="hint" name="help_box">{l s='Special characters allowed:'}.-_#\<span class="hint-pointer">&nbsp;</span></span>
						</td>
					</tr>
					<tr>
						<td class="col-left"><label>{l s='EAN13 or JAN:'}</label></td>
						<td style="padding-bottom:5px;">
							<input size="55" maxlength="13" type="text" name="ean13" value="{$product->ean13|htmlentitiesUTF8}" style="width: 130px; margin-right: 5px;" /> <span class="small">{l s='(Europe, Japan)'}</span>
						</td>
					</tr>
					<tr>
						<td class="col-left"><label>{l s='UPC:'}</label></td>
						<td style="padding-bottom:5px;">
							<input size="55" maxlength="12" type="text" name="upc" value="{$product->upc}" style="width: 130px; margin-right: 5px;" /> <span class="small">{l s='(US, Canada)'}</span>
						</td>
					</tr>
</table>
{* status informations *}
<table cellpadding="5" style="width: 40%; float: left; margin-left: 10px;">
<tr>
	<td class="col-left"><label>{l s='Status:'}</label></td>
	<td style="padding-bottom:5px;">
		<input style="float:left;" onclick="toggleDraftWarning(false);showOptions(true);" type="radio" name="active" id="active_on" value="1" {if $product->active}checked="checked" {/if} />
		<label for="active_on" class="t">
		{l s='Enabled'}</label>
		<br class="clear" />
		<input style="float:left;" onclick="toggleDraftWarning(true);showOptions(false);"  type="radio" name="active" id="active_off" value="0" {if !$product->active}checked="checked"{/if} />
		<label for="active_off" class="t">{l s='Disabled'} </label>
	</td>
</tr>
	<tr id="product_options" {if !$product->active}style="display:none"{/if} >
		<td class="col-left"><label>{l s='Options:'}</label></td>
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
		<label for="available_for_order" class="t">{l s='available for order'}</label>
		<br class="clear" />
		<input style="float: left;" type="checkbox" name="show_price" id="show_price" value="1" {if $product->show_price}checked="checked"{/if} {if $product->available_for_order}disabled="disabled"{/if}/>
		<label for="show_price" class="t">{l s='show price'}</label>
		<br class="clear" />
		<input style="float: left;" type="checkbox" name="online_only" id="online_only" value="1" {if $product->online_only}checked="checked"{/if} />
		<label for="online_only" class="t">{l s='online only (not sold in store)'}</label>
		</td>
	</tr>
	<tr>
	<td class="col-left"><label>{l s='Condition:'}</label></td>
	<td style="padding-bottom:5px;">
	<select name="condition" id="condition">
	<option value="new" {if $product->condition == 'new'}selected="selected"{/if} >{l s='New'}</option>
	<option value="used" {if $product->condition == 'used'}selected="selected"{/if} >{l s='Used'}</option>
	<option value="refurbished" {if $product->condition == 'refurbished'}selected="selected"{/if}>{l s='Refurbished'}</option>
	</select>
	</td>
	</tr>
</table>
<table cellpadding="5" cellspacing="0" border="0" style="width: 100%;"><tr><td><div class="separation"></div></td></tr></table>
{* [begin] pack product *}
<script type='text/javascript'>
	var msg_select_one = '{l s='Thanks to select at least one product.'}';
	var msg_set_quantity = '{l s='Thanks to set a quantity to add a product.'}';
</script>
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

<script type="text/javascript">
var newLabel = '{l s='New label'}';
var choose_language = '{l s='Choose language:'}';
var required = '{l s='required'}';
var customizationUploadableFileNumber = '{$product->uploadable_files}';
var customizationTextFieldNumber = '{$product->text_fields}';
var uploadableFileLabel = 0;
var textFieldLabel = 0;
</script>
<div class="separation"></div>
<table cellpadding="5" cellspacing="0" border="0" style="width: 100%;">
<tr>
	<td colspan="2">
		<p><input type="checkbox" id="is_virtual_good" name="is_virtual_good" value="true" {*onclick="toggleVirtualProduct(this);"*} {if $product->is_virtual && $product->productDownload->active}checked="checked"{/if} />
			<label for="is_virtual_good" class="t bold">{l s='Is this a virtual product?'}</label>
		</p>
		{* [begin] virtual product *}
		<div id="virtual_good" class="toggleVirtualPhysicalProduct" {if !$product->productDownload->id || $product->productDownload->active}style="display:none"{/if} >
			<input type="hidden" id="is_virtual" name="is_virtual" value="{$product->is_virtual}" />
			<table cellpadding="5" style="width: 50%; float: left; margin-right: 20px; border-right: 1px solid #CCCCCC;">
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
							<div style="padding:5px;width:50%;float:left;margin-right:20px;border-right:1px solid #CCCCCC">
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
							<a id="delete_downloadable_product" style="display:none;" onclick="return confirm('{l s='Delete this file'}')" href="{$currentIndex}&deleteVirtualProduct=true&token={$token}&id_product={$product->id}" class="red">
								{l s='Delete this file'}
							</a>
							</div>
						{else}
							<input type="hidden" id="virtual_product_filename" name="virtual_product_filename" value="{$product->productDownload->filename}" />
							{l s='This is the link'}:&nbsp;{$product->productDownload->getHtmlLink(false, true)}
							<a onclick="return confirm('{l s='Delete this file'})')" href="{$currentIndex}&deleteVirtualProduct=true&token={$token}&id_product={$product->id}" class="red">{l s='Delete this file'}</a>
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
	</td>
</tr>


</table>
<div class="separation"></div>
<table cellspacing="0" cellpadding="5" border="0">	
					<tr>
						<td class="col-left"><label>{l s='Short description:'}<br /></label><p class="product_description">({l s='appears in the product lists and on the top of the product page'})</p></td>
						<td style="padding-bottom:5px;">
								{include file="products/textarea_lang.tpl"
								languages=$languages
								input_name='description_short'
								input_value=$product->description_short
								max=400}

		<p class="clear"></p>
			</td>
					</tr>
					<tr>
						<td class="col-left"><label>{l s='Description:'}<br /></label><p class="product_description">({l s='appears in the body of the product page'})</p></td>
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
							$('#createImageDescription input').change(function(){
								changeTagImage();
							});

							var i = 0;
							$('.addImageDescription').click(function(){
								if (i == 0){
									$('#createImageDescription').animate({
										opacity: 1, height: 'toggle'
										}, 500);
									i = 1;
								}else{
									$('#createImageDescription').animate({
										opacity: 0, height: 'toggle'
										}, 500);
									i = 0;
								}
							});
						});

						function changeTagImage(){
							var smallImage = $('input[name=smallImage]:checked').attr('value');
							var leftRight = $('input[name=leftRight]:checked').attr('value');
							var imageTypes = $('input[name=imageTypes]:checked').attr('value');
							var tag = '[img-'+smallImage+'-'+leftRight+'-'+imageTypes+']';
							$('#resultImage').val(tag);
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
				<p class="preference_description clear">{l s='Tags separated by commas (e.g., dvd, dvd player, hifi)'}</p>
					</td>
				</tr>
					</td>
				</tr>
			</table>
		<br />
		</div>
{*
		
			<script type="text/javascript">
					toggleVirtualProduct(getE('is_virtual_good'));
			</script>*}
