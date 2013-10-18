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
{extends file="helpers/form/form.tpl"}

{block name="input"}
	{if $input.type == 'file'}
		<input id="{$input.name}" type="file" name="{$input.name}" class="hide" />
		<div class="dummyfile input-group">
			<span class="input-group-addon"><i class="icon-file"></i></span>
			<input id="{$input.name}-name" type="text" class="disabled" name="filename" readonly />
			<span class="input-group-btn">
				<button id="{$input.name}-selectbutton" type="button" name="submitAddAttachments" class="btn btn-default">
					<i class="icon-folder-open"></i> {l s='Choose a file'}
				</button>
			</span>
		</div>
		{if isset($input.desc)}<p>{$input.desc}</p>{/if}
		{if isset($fields_value.image) && $fields_value.image}
			<div class="clearfix">&nbsp;</div>
			<div id="image" class="img-thumbnail">
				{$fields_value.image}
				<div class="text-center">
					<p>{l s='File size'} {$fields_value.size}kb</p>
					<a class="btn btn-default" href="{$current}&{$identifier}={$form_id}&token={$token}&deleteImage=1">
						<i class="icon-trash"></i> {l s='Delete'}
					</a>
				</div>
			</div>
		{/if}
		<script>
			$(document).ready(function(){
				$('#{$input.name}-selectbutton').click(function(e){
					$('#{$input.name}').trigger('click');
				});
				$('#{$input.name}-name').click(function(e){
					$('#{$input.name}').trigger('click');
				});
				$('#{$input.name}').change(function(e){
					var val = $(this).val();
					var file = val.split(/[\\/]/);
					$('#{$input.name}-name').val(file[file.length-1]);
				});
			});
		</script>
	{else}
		{$smarty.block.parent}
	{/if}
{/block}
{block name="input"}
	{if $input.name == "link_rewrite"}
		<script type="text/javascript">
		{if isset($PS_ALLOW_ACCENTED_CHARS_URL) && $PS_ALLOW_ACCENTED_CHARS_URL}
			var PS_ALLOW_ACCENTED_CHARS_URL = 1;
		{else}
			var PS_ALLOW_ACCENTED_CHARS_URL = 0;
		{/if}
		</script>
		{$smarty.block.parent}
	{else}
		{$smarty.block.parent}
	{/if}
{/block}
{block name="description"}
	{$smarty.block.parent}
	{if ($input.name == 'groupBox')}
		<p class="alert alert-info">
			<span>{$input.info_introduction}</span><br />
			<span>{$input.unidentified}</span><br />
			<span>{$input.guest}</span><br />
			<span>{$input.customer}</span><br />
		</p>
	{/if}
{/block}