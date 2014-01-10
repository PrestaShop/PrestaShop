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
<div class="col-lg-8">
	<input id="{$id}" type="file" name="{$name}"{if isset($url)} data-url="{$url}"{/if} class="hide" />
	<button class="btn btn-default" data-style="expand-right" data-size="s" type="button" id="{$id}-add-button">
		<i class="icon-plus-sign"></i> {l s='Add file'}
	</button>
	<p class="help-block">{l s='Your server\'s maximum file-upload size is'}:&nbsp;{$post_max_size} {l s='MB'}</p>
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
		var {$id}_add_button = Ladda.create( document.querySelector('#{$id}-add-button' ));
		var success_message = '{l s='Upload successful' js=1}';

		$('#{$id}').fileupload({
			dataType: 'json',
			autoUpload: true,
			singleFileUploads: true,
			maxFileSize: {$post_max_size},
			start: function (e) {				
				{$id}_add_button.start();
			},
			fail: function (e, data) {
				showErrorMessage(data.errorThrown.message);
			},
			done: function (e, data) {
				if (data.result) {
					if (typeof data.result.virtual_product_file_uploader !== 'undefined') {
						if (typeof data.result.virtual_product_file_uploader.error !== 'undefined'
							&& data.result.virtual_product_file_uploader.error.length > 0)
							$.each(data.result.virtual_product_file_uploader.error, function(index, error) {
								showErrorMessage(data.result.virtual_product_file_uploader.name + ' : ' + error);
							});
						else {
							console.log(data.result.virtual_product_file_uploader.name);
							$('#virtual_product_filename').val(data.result.virtual_product_file_uploader.name);
							showSuccessMessage(success_message);
						}
					}
				}
			},
		}).on('fileuploadalways', function (e, data) {
			{$id}_add_button.stop();
		}).on('fileuploadprocessalways', function (e, data) {
			var index = data.index,	file = data.files[index];
		});

		$('#{$id}-add-button').on('click', function() {
			$('#{$id}').trigger('click');
		});
	});
</script>