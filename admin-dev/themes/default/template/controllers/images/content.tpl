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

{$content}

{if isset($display_regenerate)}
	<h2 class="space">{l s='Regenerate thumbnails'}</h2>
	{l s='Regenerates thumbnails for all existing images'}<br /><br />
	<div  class="width4">
		<div class="warn">
			{l s='Please be patient. This can take several minutes.'}<br />
			{l s='Be careful! Manually uploaded thumbnails will be erased and replaced by automatically generated thumbnails.'}
		</div>
	</div>
	<form action="{$current}&token={$token}" method="post">
		<fieldset class="width4">
			<legend><img src="../img/admin/picture.gif" /> {l s='Regenerate thumbnails'}</legend><br />
			<label>{l s='Select an image'}</label>
			<div class="margin-form">
				<select name="type" onchange="changeFormat(this)">
					<option value="all">{l s='All'}</option>
					{foreach $types AS $k => $type}
						<option value="{$k}">{$type}</option>
					{/foreach}
				</select>
			</div>

			{foreach $types AS $k => $type}
				<label class="second-select format_{$k}" style="display:none;">{l s='Select a format'}</label>
				<div class="second-select margin-form format_{$k}" style="display:none;">
					<select class="second-select format_{$k}" name="format_{$k}">
						<option value="all">{l s='All'}</option>
						{foreach $formats[$k] AS $format}
							<option value="{$format['id_image_type']}">{$format['name']}</option>
						{/foreach}
					</select>
				</div>
			{/foreach}
			<script>
				function changeFormat(elt)
				{ldelim}
					$('.second-select').hide();
					$('.format_' + $(elt).val()).show();
				{rdelim}
			</script>
			<label>{l s='Erase previous images'}</label>
			<div class="margin-form">
				<input name="erase" type="checkbox" value="1" checked="checked" />
				<p>{l s='Deselect this checkbox only if your server timed out and you need to resume the regeneration.'}</p>
			</div>
			<div class="clear"></div>
			<center><input type="Submit" name="submitRegenerate{$table}" value="{l s='Regenerate thumbnails'}" class="button space" onclick="return confirm('{l s='Are you sure?'}');" /></center>
		</fieldset>
	</form>
{/if}

{if isset($display_move) && $display_move}
	<br /><h2 class="space">{l s='Move images'}</h2>
	{l s='PrestaShop now uses a new storage system for product images. It offers better performance if your shop has a large number of products.'}<br />
	<br />
	{if $safe_mode}
		<div class="warn">
			{l s='PrestaShop has detected that your server configuration is not compatible with the new storage system (directive "safe_mode" is activated). You should therefore continue to use the existing system.'}
		</div>
	{else}
		<form action="{$current}&token={$token}" method="post">
			<fieldset class="width4">
				<legend><img src="../img/admin/picture.gif" /> {l s='Move images'}</legend><br />
				{l s='You can choose to keep your images stored in the previous system. There\'s nothing wrong with that.'}<br />
				{l s='You can also decide to move your images to the new storage system. In this case, click on the "Move images" button below. Please be patient. This can take several minutes.'}
				<br /><br />
				<div class="hint clear" style="display: block;">&nbsp;
					{l s='After moving all of your product images, set the "Use the legacy image filesystem" option above to "No" for best performance.'}
				</div>
				<center><input type="Submit" name="submitMoveImages{$table}" value="{l s='Move images'}" class="button space" onclick="return confirm('{l s='Are you sure?'}');" /></center>
			</fieldset>
		</form>
	{/if}
{/if}