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

{if isset($obj->id)}
<div class="panel">
	<input type="hidden" name="submitted_tabs[]" value="Attachments" />
	<h3>{l s='Attachment'}</h3>

	<div class="form-group">
		<label class="control-label col-lg-3 required" for="attachment_name_{$id_lang}">
			<span class="label-tooltip" data-toggle="tooltip"
			title="{l s='Maximum 32 characters.'}">
			{l s='Filename:'}
			</span>
		</label>
		<div class="col-lg-9">
			{include file="controllers/products/input_text_lang.tpl"
				languages=$languages
				input_value=$attachment_name
				input_name="attachment_name"
			}
		</div>
	</div>

	<div class="form-group">
		<label class="control-label col-lg-3" for="attachment_description_{$id_lang}">{l s='Description:'} </label>
		<div class="col-lg-9">
			<div class="row">
			{include
				file="controllers/products/textarea_lang.tpl"
				languages=$languages
				input_name="attachment_description"
				input_value=$attachment_description
			}
			</div>
		</div>
	</div>

	<div class="form-group">
		<label class="control-label col-lg-3" for="attachement_filename">
			<span class="label-tooltip" data-toggle="tooltip"
				title="{l s='Upload a file from your computer'} ({$PS_ATTACHMENT_MAXIMUM_SIZE|string_format:'%.2f'} {l s='MB max.'})">
				{l s='File:'}
			</span>
		</label>
		<div class="col-lg-8">
			<input id="attachment_file" type="file" name="attachment_file" data-url="{$current}&token={$token}&id_product={$obj->id}&ajax=1&action=AddAttachment" class="hide" />
			<button class="ladda-button btn btn-default" data-style="expand-right" data-size="s" type="button" id="attachment_file-add-button">
				<i class="icon-folder-open"></i>
				{l s='Add file'}
			</button>
			<div class="alert alert-danger" id="attachment_file-errors" style="display:none"></div>
			<div class="alert alert-success" id="attachment_file-success" style="display:none">{l s='Upload successful'}</div>
		</div>
	</div>

	<script type="text/javascript">
		function humanizeSize(bytes) {
			if (typeof bytes !== 'number')
				return '';
			if (bytes >= 1000000000)
				return (bytes / 1000000000).toFixed(2) + ' GB';
			if (bytes >= 1000000)
				return (bytes / 1000000).toFixed(2) + ' MB';
			return (bytes / 1000).toFixed(2) + ' KB';
		}

		$(document).ready(function() {
			var attachment_file_add_button = Ladda.create( document.querySelector('#attachment_file-add-button' ));
			var attachment_file_total_files = 0;

			$('#attachment_file').fileupload({
				dataType: 'json',
				autoUpload: true,
				singleFileUploads: true,
				{if isset ($post_max_size)}maxFileSize: {$post_max_size},{/if}
				start: function (e) {
					attachment_file_add_button.start();
				},
				fail: function (e, data) {
					$('#attachment_file-errors').html(data.errorThrown.message).show();
				},
				done: function (e, data) {
					if (data.result) {
						if (typeof data.result.attachment_file !== 'undefined') {
							if (typeof data.result.attachment_file.error !== 'undefined' && data.result.attachment_file.error.length > 0)
								$.each(data.result.attachment_file.error, function(index, error) {
									$('#attachment_file-errors').append('<p><strong>'+data.result.attachment_file.name+'</strong> : '+error+'</p>').show();
								});
							else {
								$('#attachment_file-success').show();
								$('#selectAttachment1').append('<option value="'+data.result.attachment_file.id_attachment+'">'+data.result.attachment_file.filename+'</option>');
							}
						}
					}
				},
			}).on('fileuploadalways', function (e, data) {
				attachment_file_add_button.stop();
			}).on('fileuploadprocessalways', function (e, data) {
				var index = data.index,	file = data.files[index];
				
				if (file.error) {
					$('#attachment_file-errors').append('<strong>'+file.name+'</strong> ('+humanizeSize(file.size)+') : '+file.error).show();
				}
			}).on('fileuploadsubmit', function (e, data) {
				var params = new Object();

				$('input[id^="attachment_name_"]').each(function()
				{
					id = $(this).prop("id").replace("attachment_name_", "attachment_name[") + "]";
					params[id] = $(this).val();
				});

				$('textarea[id^="attachment_description_"]').each(function()
				{
					id = $(this).prop("id").replace("attachment_description_", "attachment_description[") + "]";
					params[id] = $(this).val();
				});

				data.formData = params;
			});

			$('#attachment_file-add-button').on('click', function(e) {
				e.preventDefault();
				$('#attachment_file-success').hide();
				$('#attachment_file-errors').html('').hide();
				$('#attachment_file').trigger('click');
			});
		});
	</script>

	<hr/>

	<div class="form-group">
		<div class="col-lg-9 col-lg-offset-3">
			<div class="row">
				<div class="col-lg-6">
					<p>{l s='Available attachments:'}</p>
					<select multiple id="selectAttachment2">
						{foreach $attach2 as $attach}
							<option value="{$attach.id_attachment}">{$attach.name}</option>
						{/foreach}
					</select>
					<a href="#" id="addAttachment" class="btn btn-default btn-block">{l s='Add'} <i class="icon-arrow-right"></i></a>
				</div>
				<div class="col-lg-6">
					<p>{l s='Attachments for this product:'}</p>
					<select multiple id="selectAttachment1" name="attachments[]">
						{foreach $attach1 as $attach}
							<option value="{$attach.id_attachment}">{$attach.name}</option>
						{/foreach}
					</select>
					<a href="#" id="removeAttachment" class="btn btn-default btn-block"><i class="icon-arrow-left"></i> {l s='Remove'}</a>
				</div>
			</div>
		</div>
	</div>

	<input type="hidden" name="arrayAttachments" id="arrayAttachments" value="{foreach $attach1 as $attach}{$attach.id_attachment},{/foreach}" />

	<script type="text/javascript">
		var iso = '{$iso_tiny_mce}';
		var pathCSS = '{$smarty.const._THEME_CSS_DIR_}';
		var ad = '{$ad}';
		hideOtherLanguage({$id_lang});
	</script>
</div>
{/if}