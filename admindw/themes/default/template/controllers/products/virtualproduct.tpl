{*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 11204 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<script type="text/javascript">
	var newLabel = '{l s='New label'}';
	var choose_language = '{l s='Choose language:'}';
	var required = '{l s='required'}';
	var customizationUploadableFileNumber = '{$product->uploadable_files}';
	var customizationTextFieldNumber = '{$product->text_fields}';
	var uploadableFileLabel = 0;
	var textFieldLabel = 0;

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
					var fileName = data.getAttribute("filename");
					if (result == "error")
					{
						$("#upload-confirmation").hide();
						$("#upload-error td").html('<div class="error">{l s='Error:'} ' + msg + '</div>');
						$("#upload-error").show();
					}
					else
					{
						$('#upload-error').hide();
						$('#file_missing').hide();
						$('#virtual_product_name').attr('value', fileName);
						$("#upload-confirmation .error").remove();
						$('#upload-confirmation div').prepend('<span>{l s='The file'}&nbsp;"<a class="link" href="get-file-admin.php?file='+msg+'&filename='+fileName+'">'+fileName+'</a>"&nbsp;{l s='has successfully been uploaded'}' +
							'<input type="hidden" id="virtual_product_filename" name="virtual_product_filename" value="' + msg + '" /></span>');
						$("#upload-confirmation").show();
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

</script>

<input type="hidden" name="submitted_tabs[]" value="VirtualProduct" />
<h4>{l s='Virtual Product (services, booking and downloadable products)'}</h4>
<div class="separation"></div>
<div>
	<div class="is_virtual_good">
		<input type="checkbox" id="is_virtual_good" name="is_virtual_good" value="true" {if $product->is_virtual && $product->productDownload->active}checked="checked"{/if} />
			<label for="is_virtual_good" class="t bold">{l s='Is this a virtual product?'}</label>
	</div>
	{* [begin] virtual product *}
	<div id="virtual_good" {if !$product->productDownload->id || $product->productDownload->active}style="display:none"{/if} >
		<div>
			<label>{l s='Does this product have an associated file?'}</label>
			<label style="width:50px"><input type="radio" value="1"  name="is_virtual_file" {if $product_downloaded}checked="checked"{/if} />{l s='Yes'}</label>
			<label style="width:50px;"><input type="radio" value="0" name="is_virtual_file" {if !$product_downloaded}checked="checked"{/if} />{l s='No'}</label>
		</div><br />
		<div class="separation"></div>
		{if $download_product_file_missing}
			<p class="alert" id="file_missing">
				<b>{$download_product_file_missing} :<br/>
				{$smarty.const._PS_DOWNLOAD_DIR_}/{$product->productDownload->filename}</b>
			</p>
		{/if}

		<div id="is_virtual_file_product" style="display:none;">
			{if !$download_dir_writable}
				<p class="alert">
					{l s='Your download repository is not writable.'}<br/>
					{$smarty.const._PS_DOWNLOAD_DIR_}
				</p>
			{/if}
			{* Don't display file form if the product has combinations *}
			{if empty($product->cache_default_attribute)}
				{if $product->productDownload->id}
					<input type="hidden" id="virtual_product_id" name="virtual_product_id" value="{$product->productDownload->id}" />
				{/if}
				<table cellpadding="5" style="float: left; margin-left: 10px;">
					<tr id="upload_input" {if $is_file}style="display:none"{/if}>
						<td class="col-left">
							<label id="virtual_product_file_label" for="virtual_product_file" class="t">{l s='Upload a file'}</label>
						</td>
						<td class="col-right">
							<input type="file" id="virtual_product_file" name="virtual_product_file" onchange="uploadFile();" maxlength="{$upload_max_filesize}" />
							<p class="preference_description">{l s='Your server\'s maximum upload file size is'}:&nbsp;{$upload_max_filesize} {l s='MB'}</p>
						</td>
					</tr>
					<tr id="upload-error" style="display:none">
						<td colspan=2></td>
					</tr>
					<tr id="upload-confirmation" style="display:none">
						<td colspan=2>
							{if $up_filename}
								<input type="hidden" id="virtual_product_filename" name="virtual_product_filename" value="{$up_filename}" />
							{/if}
							<div class="conf">
								<a class="delete_virtual_product" id="delete_downloadable_product" onclick="return confirm('{l s='Delete this file'}')" href="{$currentIndex}&deleteVirtualProduct=true&token={$token}&id_product={$product->id}" class="red">
									<img src="../img/admin/delete.gif" alt="{l s='Delete this file'}"/>
								</a>
							</div>
						</td>
					</tr>
					{if $is_file}
						<tr>
							<td class="col-left">
								<input type="hidden" id="virtual_product_filename" name="virtual_product_filename" value="{$product->productDownload->filename}" />
								<label class="t">{l s='Link to the file:'}</label>
							</td>
							 <td class="col-right">
								{$product->productDownload->getHtmlLink(false, true)}
								<a onclick="return confirm('{l s='Delete this file'})')" href="{$currentIndex}&deleteVirtualProduct=true&token={$token}&id_product={$product->id}" class="red delete_virtual_product">
									<img src="../img/admin/delete.gif" alt="{l s='Delete this file'}"/>
								</a>
							</td>
						</tr>
					{/if}
					<tr>
						<td class="col-left">
							<label for="virtual_product_name" class="t">{l s='Filename'}</label>
						</td>
						<td class="col-right">
							<input type="text" id="virtual_product_name" name="virtual_product_name" style="width:200px" value="{$product->productDownload->display_filename|escape:'htmlall':'UTF-8'}" />
							<p class="preference_description" name="help_box">{l s='The full filename with its extension (e.g. Book.pdf)'}</p>
						</td>
					</tr>
					<tr>
						<td class="col-left">
							<label for="virtual_product_nb_downloable" class="t">{l s='Number of allowed downloads'}</label>
						</td>
						<td class="col-right">
							<input type="text" id="virtual_product_nb_downloable" name="virtual_product_nb_downloable" value="{$product->productDownload->nb_downloadable|htmlentities}" class="" size="6" />
							<p class="preference_description">{l s='Number of allowed downloads per customer - (Set to 0 for unlimited downloads)'}</p>
						</td>
					</tr>
					<tr>
						<td class="col-left">
							<label for="virtual_product_expiration_date" class="t">{l s='Expiration date'}</label>
						</td>
						<td class="col-right">
							<input class="datepicker" type="text" id="virtual_product_expiration_date" name="virtual_product_expiration_date" value="{$product->productDownload->date_expiration}" size="11" maxlength="10" autocomplete="off" /> {l s='Format: YYYY-MM-DD'}
							<p class="preference_description">{l s='If set, the file will not be downloadable anymore after this date. Leave this blank for no expiration date'}</p>
						</td>
					</tr>
						<td class="col-left">
							<label for="virtual_product_nb_days" class="t">{l s='Number of days'}</label>
						</td>
						<td class="col-right">
							<input type="text" id="virtual_product_nb_days" name="virtual_product_nb_days" value="{$product->productDownload->nb_days_accessible|htmlentities}" class="" size="4" /><sup> *</sup>
							<p class="preference_description">{l s='How many days this file can be accessed by customers'} - <em>({l s='Set to zero for unlimited access'})</em></p>
						</td>
					</tr>
					{* Feature not implemented *}
					{*<tr>*}
						{*<td class="col-left">*}
							{*<label for="virtual_product_is_shareable" class="t">{l s='is shareable'}</label>*}
						{*</td>*}
						{*<td class="col-right">*}
							{*<input type="checkbox" id="virtual_product_is_shareable" name="virtual_product_is_shareable" value="1" {if $product->productDownload->is_shareable}checked="checked"{/if} />*}
							{*<span class="hint" name="help_box" style="display:none">{l s='Specify if the file can be shared'}</span>*}
						{*</td>*}
					{*</tr>*}
				{else}
					<div class="hint clear" style="display: block;width: 70%;">{l s='You cannot edit your file here because you used combinations. Please edit it in the Combinations tab'}</div>
					<br />
					{$error_product_download}
				{/if}
			</table>
		</div>
	</div>
	<div style="clear:both"></div>
</div>