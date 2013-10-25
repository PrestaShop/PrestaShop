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
{if isset($images) && $images}
<div class="form-group">
	<div class="col-lg-12">
		{foreach $images as $image}
		{if isset($image.image)}
		<div class="img-thumbnail text-center">
			<p>{$image.image}</p>
			{if isset($image.size)}<p>{l s='File size'} {$image.size}kb</p>{/if}
			{if isset($image.delete_url)}
			<p>
				<a class="btn btn-default" href="{$image.delete_url}">
				<i class="icon-trash"></i> {l s='Delete'}
				</a>
			</p>
			{/if}
		</div>
		{/if}
		{/foreach}
	</div>
</div>
{/if}
{if isset($thumb) && $thumb}
	<div class="form-group">
		<div class="col-lg-12">
			<img src="{$thumb}" alt="{$title}" title="{$title}" class="img-thumbnail" />
		</div>
	</div>
{/if}
<div class="form-group">
	<div class="col-lg-12">
		<input id="{$id}" type="file" name="{$name}"{if isset($multiple) && $multiple} multiple="multiple"{/if} class="hide" />
		<div class="dummyfile input-group">
			<span class="input-group-addon"><i class="icon-file"></i></span>
			<input id="{$id}-name" type="text" class="disabled" name="filename" readonly />
			<span class="input-group-btn">
				<button id="{$id}-selectbutton" type="button" name="submitAddAttachments" class="btn btn-default">
					<i class="icon-folder-open"></i> {if isset($multiple) && $multiple}{l s='Add files'}{else}{l s='Add file'}{/if}
				</button>
			</span>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$('#{$id}-selectbutton').click(function(e) {
			$('#{$id}').trigger('click');
		});

		$('#{$id}-name').click(function(e) {
			$('#{$id}').trigger('click');
		});

		$('#{$id}').change(function(e) {
			if ($(this)[0].files !== undefined)
			{
				var files = $(this)[0].files;
				var name  = '';

				$.each(files, function(index, value) {
					name += value.name+', ';
				});

				$('#{$id}-name').val(name.slice(0, -2));
			}
			else // Internet Explorer 9 Compatibility
			{
				var name = $(this).val().split(/[\\/]/);
				$('#{$id}-name').val(name[name.length-1]);
			}
		});
	});
</script>