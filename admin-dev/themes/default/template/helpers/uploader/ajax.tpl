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
<div class="form-group" style="display: none;">
	<div class="col-lg-12" id="{$id}-images-thumbnails">
		{if isset($files) && $files|count > 0}
		{foreach $files as $file}
		{if isset($file.image) && $file.type == 'image'}
		<div class="img-thumbnail text-center">
			<p>{$file.image}</p>
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
<div class="form-group">
	<div class="col-lg-12">
		<input id="{$id}" type="file" name="{$name}[]"{if isset($url)} data-url="{$url}"{/if}{if isset($multiple) && $multiple} multiple="multiple"{/if} class="hide" />
		<button class="btn btn-default" data-style="expand-right" data-size="s" type="button" id="{$id}-add-button">
			<i class="icon-plus-sign"></i> {if isset($multiple) && $multiple}{l s='Add files'}{else}{l s='Add file'}{/if}
		</button>
		<button class="ladda-button" data-style="expand-right" data-size="s" type="button" id="{$id}-upload-button" style="display:none;">
			<i class="icon-cloud-upload"></i> <span class="ladda-label">{if isset($multiple) && $multiple}{l s='Upload files'}{else}{l s='Upload file'}{/if}</span>
		</button>
	</div>
</div>
<div class="row" style="display:none">
	<div class="alert alert-info" id="{$id}-files-list">
		<strong>{l s='Files:'}</strong>
		<br />
	</div>
</div>
<div class="row" style="display:none">
	<div class="alert alert-danger" id="{$id}-errors"></div>
</div>

<script type="text/javascript">
	$( document ).ready(function() {
		{if isset($multiple) && isset($max_files)}
			var {$id}_max_files = {$max_files};
		{/if}

		{if isset($files) && $files}
		$('#{$id}-images-thumbnails').parent().show();
		{/if}

		var {$id}_total_files = 0;
		var {$id}_upload_button = Ladda.create( document.querySelector('#{$id}-upload-button' ));

		var data = $('#{$id}').fileupload({
			dataType: 'json',
			autoUpload: false,
			singleFileUploads: false,
			add: function(e, data) {
				if (typeof {$id}_max_files !== 'undefined') {
					if ({$id}_total_files >= {$id}_max_files || data.originalFiles.length > {$id}_max_files) {
						e.preventDefault();
						alert('{l s='You can upload a maximum of %s files'|sprintf:$max_files}');
						return;
					}
				}

				{$id}_total_files++;
				$('#{$id}-upload-button').show().on('click', function () {
					data.submit();
				});
			},
			start: function () {
				{$id}_upload_button.start();
			},
			fail: function (e, data) {
				$('#{$id}-errors').html(data.errorThrown.message).parent().show();
			},
			done: function (e, data) {
				if (typeof data.result.{$name} !== 'undefined') {
					for (var i=0; i<data.result.{$name}.length; i++) {
						if (typeof data.result.{$name}[i].image !== 'undefined') {
							var template = '<div class="img-thumbnail text-center">';
							template += '<p>'+data.result.{$name}[i].image+'</p>';
							
							if (typeof data.result.{$name}[i].delete_url !== 'undefined') {
								template += '<p><a class="btn btn-default" href="'+data.result.{$name}[i].delete_url+'"><i class="icon-trash"></i> {l s='Delete'}</a></p>';
							}

							template += '</div>';
							$('#{$id}-images-thumbnails').html($('#{$id}-images-thumbnails').html()+template);
							$('#{$id}-images-thumbnails').parent().show();
						}
					}
				}
			},
			always: function (e, data) {
				{$id}_upload_button.stop();
			}
		});

		$('#{$id}-add-button').on('click', function() {
			$('#{$id}').trigger('click');
		});
	});

















	/**/

	/*
	var total_files   = 0;
	var total_uploaded_files   = 0;
	{if isset($multiple) && isset($max_files)}
	var {$id}_max_files = {$max_files};
	{/if}

   	$('#{$id}').fileupload(
   	{
        dataType: 'json',
        autoUpload: false,
        acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
        maxFileSize: 5000000, // 5 MB
        // Enable image resizing, except for Android and Opera,
        // which actually support image resizing, but fail to
        // send Blob objects via XHR requests:
        disableImageResize: /Android(?!.*Chrome)|Opera/
            .test(window.navigator.userAgent),
        previewMaxWidth: 100,
        previewMaxHeight: 100,
        previewCrop: false,
       
        done: function (e, data) {
            $.each(data.result, function (index, file) {
                if (file[0].error)
                	$('#{$id}-errors').append(file[0].error+'<br/>').parent().show();
                else
                	total_uploaded_files ++;
            });

            if (total_uploaded_files == data.originalFiles.length)
            	$('#{$id}-upload-button').css('background-color', 'green').children('.icon-cloud-upload').removeClass('icon-cloud-upload').addClass('icon-check');
        },
        progressall: function (e, data) {
        	upload_button.setProgress(data.loaded / data.total);
	    },
	    add: function (e, data) {
	    	console.log(data.originalFiles.length);
	    	if (typeof {$id}_max_files !== 'undefined')
			{
				if (data.originalFiles.length > {$id}_max_files) {
					e.preventDefault();
					alert('{l s='You can upload a maximum of %s files'|sprintf:$max_files}');
					return;
				}
			}

	    	data.context = $('#{$id}-files-list');
	    	data.context.parent().show();

	        $.each(data.files, function (index, file) {
	        	var node = '<div class="row"><span>'+file.name+'</span><button class="btn btn-default pull-right" type="button"><i class="icon-trash"></i> Remove from list</button></div>';
	           data.context.append(node);
	        });

	        $('#{$id}-upload-button').show().on('click', function () {
            	total_files = data.originalFiles.length;
            	data.submit();
            });
   		},
   		fail: function (e, data) {
   			$('#{$id}-errors').html(data.errorThrown.message).parent().show();
   		},
   		always: function (e, data) {
   			total_files--;

   			if (total_files == 0)
   				upload_button.stop();
   		}
    });*/

</script>