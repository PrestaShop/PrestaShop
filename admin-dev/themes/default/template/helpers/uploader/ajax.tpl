{*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div class="form-group" style="display: none;">
	<div class="col-lg-12" id="{$id}-images-thumbnails">
		{if isset($files) && $files|count > 0}
		{foreach $files as $file}
		{if isset($file.image) && $file.type == 'image'}
		<div>
			{$file.image}
			{if isset($file.size)}<p>{l s='File size'} {$file.size}kb</p>{/if}
			{if isset($file.delete_url)}
			<p>
				<a class="btn btn-default" href="{$file.delete_url}">
					<i class="icon-trash"></i> {l s='Delete'}
				</a>
			</p>
			{/if}
		</div>
		{/if}
		{/foreach}
		{/if}
	</div>
</div>
{if isset($max_files) && $files|count >= $max_files}
<div class="row">
	<div class="alert alert-warning">{l s='You have reached the limit (%s) of files to upload, please remove files to continue uploading' sprintf=$max_files}</div>
</div>
<script type="text/javascript">
	$( document ).ready(function() {
		{if isset($files) && $files}
		$('#{$id}-images-thumbnails').parent().show();
		{/if}
	});
</script>
{else}
<div class="form-group">
	<div class="col-lg-12">
		<input id="{$id}" type="file" name="{$name}[]"{if isset($url)} data-url="{$url}"{/if}{if isset($multiple) && $multiple} multiple="multiple"{/if} style="width:0px;height:0px;" />
		<button class="btn btn-default" data-style="expand-right" data-size="s" type="button" id="{$id}-add-button">
			<i class="icon-folder-open"></i> {if isset($multiple) && $multiple}{l s='Add files...'}{else}{l s='Add file...'}{/if}
		</button>
	</div>
</div>

<div class="well" style="display:none">
	<div id="{$id}-files-list"></div>
	<button class="ladda-button btn btn-primary" data-style="expand-right" type="button" id="{$id}-upload-button" style="display:none;">
		<span class="ladda-label"><i class="icon-check"></i> {if isset($multiple) && $multiple}{l s='Upload files'}{else}{l s='Upload file'}{/if}</span>
	</button>
</div>
<div class="row" style="display:none">
	<div class="alert alert-success" id="{$id}-success"></div>
</div>
<div class="row" style="display:none">
	<div class="alert alert-danger" id="{$id}-errors"></div>
</div>
<script type="text/javascript">
	function humanizeSize(bytes)
	{
		if (typeof bytes !== 'number') {
			return '';
		}

		if (bytes >= 1000000000) {
			return (bytes / 1000000000).toFixed(2) + ' GB';
		}

		if (bytes >= 1000000) {
			return (bytes / 1000000).toFixed(2) + ' MB';
		}

		return (bytes / 1000).toFixed(2) + ' KB';
	}

	$( document ).ready(function() {
		{if isset($multiple) && isset($max_files)}
			var {$id}_max_files = {$max_files - $files|count};
		{/if}

		{if isset($files) && $files}
		$('#{$id}-images-thumbnails').parent().show();
		{/if}

		var {$id}_upload_button = Ladda.create( document.querySelector('#{$id}-upload-button' ));
		var {$id}_total_files = 0;

		$('#{$id}').fileupload({
			dataType: 'json',
			async: false,
			autoUpload: false,
			singleFileUploads: true,
			{if isset($post_max_size)}maxFileSize: {$post_max_size},{/if}
			{if isset($drop_zone)}dropZone: {$drop_zone},{/if}
			start: function (e) {
				{$id}_upload_button.start();
				$('#{$id}-upload-button').unbind('click'); //Important as we bind it for every elements in add function
			},
			fail: function (e, data) {
				$('#{$id}-errors').html(data.errorThrown.message).parent().show();
			},
			done: function (e, data) {
				if (data.result) {
					if (typeof data.result.{$name} !== 'undefined') {
						for (var i=0; i<data.result.{$name}.length; i++) {
							if (data.result.{$name}[i] !== null) {
								if (typeof data.result.{$name}[i].error !== 'undefined' && data.result.{$name}[i].error != '') {
									$('#{$id}-errors').html('<strong>'+data.result.{$name}[i].name+'</strong> : '+data.result.{$name}[i].error).parent().show();
								}
								else 
								{
									$(data.context).appendTo($('#{$id}-success'));
									$('#{$id}-success').parent().show();

									if (typeof data.result.{$name}[i].image !== 'undefined')
									{
										var template = '<div>';
										template += data.result.{$name}[i].image;
										
										if (typeof data.result.{$name}[i].delete_url !== 'undefined')
											template += '<p><a class="btn btn-default" href="'+data.result.{$name}[i].delete_url+'"><i class="icon-trash"></i> {l s='Delete'}</a></p>';

										template += '</div>';
										$('#{$id}-images-thumbnails').html($('#{$id}-images-thumbnails').html()+template);
										$('#{$id}-images-thumbnails').parent().show();
									}
								}
							}
						}
					}

					$(data.context).find('button').remove();					
				}
			},
		}).on('fileuploadalways', function (e, data) {
				{$id}_total_files--;

				if ({$id}_total_files == 0)
				{
					{$id}_upload_button.stop();
					$('#{$id}-upload-button').unbind('click');
					$('#{$id}-files-list').parent().hide();
				}
		}).on('fileuploadadd', function(e, data) {
			if (typeof {$id}_max_files !== 'undefined') {
				if ({$id}_total_files >= {$id}_max_files) {
					e.preventDefault();
					alert('{l s='You can upload a maximum of %s files'|sprintf:$max_files}');
					return;
				}
			}

			data.context = $('<div/>').addClass('form-group').appendTo($('#{$id}-files-list'));
			var file_name = $('<span/>').append('<strong>'+data.files[0].name+'</strong> ('+humanizeSize(data.files[0].size)+')').appendTo(data.context);

			var button = $('<button/>').addClass('btn btn-default pull-right').prop('type', 'button').html('<i class="icon-trash"></i> {l s='Remove file'}').appendTo(data.context).on('click', function() {
				{$id}_total_files--;
				data.files = null;
				
				var total_elements = $(this).parent().siblings('div.row').length;
				$(this).parent().remove();

				if (total_elements == 0) {
					$('#{$id}-files-list').html('').parent().hide();
				}
			});

			$('#{$id}-files-list').parent().show();
			$('#{$id}-upload-button').show().bind('click', function () {
				if (data.files != null)
					data.submit();						
			});

			{$id}_total_files++;
		}).on('fileuploadprocessalways', function (e, data) {
			var index = data.index,	file = data.files[index];
			
			if (file.error) {
				$('#{$id}-errors').append('<div class="form-group"><strong>'+file.name+'</strong> ('+humanizeSize(file.size)+') : '+file.error+'</div>').parent().show();
				$(data.context).find('button').trigger('click');
			}
		});

		$('#{$id}-add-button').on('click', function() {
			$('#{$id}-success').html('').parent().hide();
			$('#{$id}-errors').html('').parent().hide();
			$('#{$id}-files-list').parent().hide();
			{$id}_total_files = 0;
			$('#{$id}').trigger('click');
		});
	});
</script>
{/if}