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

<script type="text/javascript">
	var newLabel = '{l s='New label'}';
	var choose_language = '{l s='Choose language:'}';
	var required = '{l s='Required'}';
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
						$("#upload-confirmation").closest('.form-group.').hide();
						$("#upload-error").html('{l s='Error:'} ' + msg);
						$("#upload-error").closest('.form-group.').show();
					}
					else
					{
						$('#upload-error').closest('.form-group.').hide();
						$('#file_missing').closest('.form-group.').hide();
						$('#virtual_product_name').attr('value', fileName);
						$('#upload-confirmation').prepend('<span>{l s='The file'}&nbsp;"<a class="link" href="get-file-admin.php?file='+msg+'&filename='+fileName+'">'+fileName+'</a>"&nbsp;{l s='has successfully been uploaded'}&nbsp;' +
							'<input type="hidden" id="virtual_product_filename" name="virtual_product_filename" value="' + msg + '" /></span>');
						$("#upload-confirmation").closest('.form-group.').show();
					}
				}
			}
		);
	}

	$(document).ready(function(){
		$('#virtual_product_file-selectbutton').click(function(e) {
			$('#virtual_product_file').trigger('click');
		});

		$('#virtual_product_file-name').click(function(e) {
			$('#virtual_product_file').trigger('click');
		});

		$('#virtual_product_file').change(function(e) {
			if ($(this)[0].files !== undefined)
			{
				var files = $(this)[0].files;
				var name  = '';

				$.each(files, function(index, value) {
					name += value.name+', ';
				});

				$('#virtual_product_file-name').val(name.slice(0, -2));
			}
			else // Internet Explorer 9 Compatibility
			{
				var name = $(this).val().split(/[\\/]/);
				$('#virtual_product_file-name').val(name[name.length-1]);
			}

			uploadFile();
		});
	});

</script>
<div id="product-virtualproduct" class="panel product-tab">
	<input type="hidden" name="submitted_tabs[]" value="VirtualProduct" />
	<h3>{l s='Virtual Product (services, booking or downloadable products)'}</h3>
		<div class="is_virtual_good" class="form-group">
			<input type="checkbox" id="is_virtual_good" name="is_virtual_good" value="true" {if $product->is_virtual && $product->productDownload->active}checked="checked"{/if} />
				<label for="is_virtual_good" class="t bold">{l s='Is this a virtual product?'}</label>
		</div>
		<div id="virtual_good" {if !$product->productDownload->id || $product->productDownload->active}style="display:none"{/if} class="form-group">
			<div class="form-group">
				<label class="control-label col-lg-3">{l s='Does this product have an associated file?'}</label>
				<div class="col-lg-9">
					<label class="switch-light prestashop-switch fixed-width-lg">
						<input name="is_virtual_file" id="is_virtual_file" type="checkbox"{if $product_downloaded} checked="checked"{/if} value="{if $product_downloaded}1{else}0{/if}"  />
						<span>
							<span>{l s='Yes'}</span>
							<span>{l s='No'}</span>
						</span>
						<a class="slide-button btn"></a>
					</label>
				</div>
			</div>
			<div id="is_virtual_file_product" style="display:none;">
				{if $download_product_file_missing}
				<div class="form-group">
					<div class="col-lg-push-3 col-lg-9">
						<div class="alert alert-danger" id="file_missing">
							{$download_product_file_missing} :<br/>
							<strong>{l s='Server file name : %s'|sprintf:$product->productDownload->filename}</strong>
						</div>
					</div>
				</div>
				{/if}
				{if !$download_dir_writable}
				<div class="form-group">
					<div class="col-lg-push-3 col-lg-9">
						<div class="alert alert-danger">
							{l s='Your download repository is not writable.'}
						</div>
					</div>
				</div>
				{/if}
				{* Don't display file form if the product has combinations *}
				{if empty($product->cache_default_attribute)}
					{if $product->productDownload->id}
						<input type="hidden" id="virtual_product_id" name="virtual_product_id" value="{$product->productDownload->id}" />
					{/if}
					<div class="form-group"{if $is_file} style="display:none"{/if}>
						<label id="virtual_product_file_label" for="virtual_product_file" class="control-label col-lg-3">{l s='Upload a file'}</label>
						<div class="col-lg-9">
							<input id="virtual_product_file" type="file" name="virtual_product_file" class="hide" maxlength="{$upload_max_filesize}" />
							<div class="dummyfile input-group">
								<span class="input-group-addon"><i class="icon-file"></i></span>
								<input id="virtual_product_file-name" type="text" name="filename" readonly />
								<span class="input-group-btn">
									<button id="virtual_product_file-selectbutton" type="button" name="submitAddAttachments" class="btn btn-default">
										<i class="icon-folder-open"></i> {l s='Add file'}
									</button>
								</span>
							</div>
							<p class="help-block">{l s='Your server\'s maximum file-upload size is'}:&nbsp;{$upload_max_filesize} {l s='MB'}</p>
						</div>
					</div>
					<div class="form-group" style="display:none">
						<div class="col-lg-push-3 col-lg-9">
							<div class="alert alert-danger" id="upload-error"></div>
						</div>
					</div>
					<div class="form-group" style="display:none">
						<div class="col-lg-push-3 col-lg-9">
						{if $up_filename}
							<input type="hidden" id="virtual_product_filename" name="virtual_product_filename" value="{$up_filename}" />
						{/if}
							<div class="alert alert-info" id="upload-confirmation">
								<a class="btn btn-default" id="delete_downloadable_product" href="{$currentIndex}&deleteVirtualProduct=true&token={$token}&id_product={$product->id}"><i class="icon-trash"></i> {l s='Delete this file'}
								</a>
							</div>
						</div>
					</div>
					{if $is_file}
					<div class="form-group">
						<label class="control-label col-lg-3">{l s='Link to the file:'}</label>
						<div class="col-lg-9">
							<input type="hidden" id="virtual_product_filename" name="virtual_product_filename" value="{$product->productDownload->filename}" />
							<a href="{$product->productDownload->getTextLink(true)}" class="btn btn-default"><i class="icon-download"></i> {l s='Download file'}</a>
							<a href="{$currentIndex}&deleteVirtualProduct=true&token={$token}&id_product={$product->id}" class="btn btn-default"><i class="icon-trash"></i> {l s='Delete this file'}</a>
						</div>
					</div>
					{/if}
					<div class="form-group">
						<label class="control-label col-lg-3">{l s='Filename'}</label>
						<div class="col-lg-9">
							<input type="text" id="virtual_product_name" name="virtual_product_name" style="width:200px" value="{$product->productDownload->display_filename|escape:'html':'UTF-8'}" />
							<p class="help-block">{l s='The full filename with its extension (e.g. Book.pdf)'}</p>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-lg-3">{l s='Number of allowed downloads'}</label>
						<div class="col-lg-9">
							<input type="text" id="virtual_product_nb_downloable" name="virtual_product_nb_downloable" value="{$product->productDownload->nb_downloadable|htmlentities}" class="" size="6" />
							<p class="help-block">{l s='Number of downloads allowed per customer. (Set to 0 for unlimited downloads)'}</p>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-lg-3">
							<span class="label-tooltip" data-toggle="tooltip" title="" data-original-title="{l s='Format: YYYY-MM-DD'}">
								{l s='Expiration date'}
							</span>
						</label>
						<div class="col-lg-9">
							<input class="datepicker" type="text" id="virtual_product_expiration_date" name="virtual_product_expiration_date" value="{$product->productDownload->date_expiration}" size="11" maxlength="10" autocomplete="off" />
							<p class="help-block">{l s='If set, the file will not be downloadable after this date. Leave blank if you do not wish to attach an expiration date.'}</p>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-lg-3 required">{l s='Number of days'}</label>
						<div class="col-lg-9">
							<input type="text" id="virtual_product_nb_days" name="virtual_product_nb_days" value="{$product->productDownload->nb_days_accessible|htmlentities}" class="" size="4" />
							<p class="help-block">{l s='Number of days this file can be accessed by customers'} - <em>({l s='Set to zero for unlimited access.'})</em></p>
						</div>
					</div>
					{* Feature not implemented *}
					{*<div class="form-group">*}
						{*<label for="virtual_product_is_shareable" class="control-label col-lg-3">{l s='is shareable'}</label>*}
						{*<div class="col-lg-9">*}
							{*<input type="checkbox" id="virtual_product_is_shareable" name="virtual_product_is_shareable" value="1" {if $product->productDownload->is_shareable}checked="checked"{/if} />*}
							{*<span class="alert alert-info" name="help_box" style="display:none">{l s='Please specify if the file can be shared.'}</span>*}
						{*</div>*}
					{*</div>*}
					<div class="alert alert-info">
						{l s='You cannot edit your file here because you used combinations. Please edit this file in the Combinations tab.'}
					</div>
					{if isset($error_product_download)}{$error_product_download}{/if}
				{/if}
			</div>
		</div>
	</div>
</div>