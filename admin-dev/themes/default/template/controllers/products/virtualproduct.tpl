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
*  @copyright  2007-2011 PrestaShop SA
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

	$(document).ready(function(){
		$(".datepicker").datepicker({
			prevText: '',
			nextText: '',
			dateFormat: 'yy-mm-dd'
		});

		if ($('#is_virtual_good').attr('checked'))
		{
			$('#virtual_good').show();
			$('#virtual_good_more').show();
		}

		$('.is_virtual_good').hide();

		if ( $('input[name=is_virtual_file]:checked').val() == 1)
		{
			$('#virtual_good_more').show();
			$('#is_virtual_file_product').show();
		}
		else
		{
			$('#virtual_good_more').hide();
			$('#is_virtual_file_product').hide();
		}

		$('input[name=is_virtual_file]').live('change', function() {
			if($(this).val() == '1')
			{
				$('#virtual_good_more').show();
				$('#is_virtual_file_product').show();
			}
			else
			{
				$('#virtual_good_more').hide();
				$('#is_virtual_file_product').hide();
			}
		});
	});

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

</script>

<h4>{l s='Virtual Product (services, booking and downloadable products)'}</h4>
<div class="separation"></div>
<table cellpadding="5" cellspacing="0" border="0" style="width: 100%;">
	<tr>
		<td colspan="2">
			<div class="is_virtual_good">
				<input type="checkbox" id="is_virtual_good" name="is_virtual_good" value="true" {if $product->is_virtual && $product->productDownload->active}checked="checked"{/if} />
				<label for="is_virtual_good" class="t bold">{l s='Is this a virtual product?'}</label>
			</div>
			{* [begin] virtual product *}
			<div id="virtual_good" class="toggleVirtualPhysicalProduct" {if !$product->productDownload->id || $product->productDownload->active}style="display:none"{/if} >
				<input type="hidden" id="is_virtual" name="is_virtual" value="{$product->is_virtual}" />
				<table cellpadding="5" style="width: 50%; float: left; margin-right: 20px; border-right: 1px solid #CCCCCC;">
					<tr><td>
						<br/>{l s='Does this product has an associated file ?'}<br />
						<label style="width:50px"><input type="radio" value="1" id="virtual_good_file_1" name="is_virtual_file" {if $product_downloaded}checked="checked"{/if} />{l s='Yes'}</label>
						<label style="width:50px"><input type="radio" value="0" id="virtual_good_file_2" name="is_virtual_file" {if !$product_downloaded}checked="checked"{/if} />{l s='No'}</label><br /><br />
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
								<p>{l s='Your server\'s maximum upload file size is'}:&nbsp;{$upload_max_filesize} {l s='Mo'}</p>
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
									<img src="../img/admin/delete.gif" alt="{l s='Delete this file'}"/>
								</a>
								</div>
							{else}
								<input type="hidden" id="virtual_product_filename" name="virtual_product_filename" value="{$product->productDownload->filename}" />
								{l s='This is the link'}:<br />
								{$product->productDownload->getHtmlLink(false, true)}
								<a onclick="return confirm('{l s='Delete this file'})')" href="{$currentIndex}&deleteVirtualProduct=true&token={$token}&id_product={$product->id}" class="red">
									<img src="../img/admin/delete.gif" alt="{l s='Delete this file'}"/>
								</a>
							{/if}
							</p>

							<p class="block">
								<label for="virtual_product_name" class="t">{l s='Filename'}</label>
								<input type="text" id="virtual_product_name" name="virtual_product_name" style="width:200px" value="{$product->productDownload->display_filename|htmlentities}" />
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
								<input type="text" id="virtual_product_nb_downloable" name="virtual_product_nb_downloable" value="{$product->productDownload->nb_downloadable|htmlentities}" class="" size="6" />
								<span class="hint" name="help_box" style="display:none">{l s='Number of authorized downloads per customer'}</span>
							</p>
							<p class="block">
								<label for="virtual_product_expiration_date" class="t">{l s='Expiration date'}</label>
								<input class="datepicker" type="text" id="virtual_product_expiration_date" name="virtual_product_expiration_date" value="{$product->productDownload->date_expiration}" size="11" maxlength="10" autocomplete="off" /> {l s='Format: YYYY-MM-DD'}
								<span class="hint" name="help_box" style="display:none">{l s='No expiration date if you leave this blank'}</span>
							</p>
							<p class="block">
								<label for="virtual_product_nb_days" class="t">{l s='Number of days'}</label>
								<input type="text" id="virtual_product_nb_days" name="virtual_product_nb_days" value="{$product->productDownload->nb_days_accessible|htmlentities}" class="" size="4" /><sup> *</sup>
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