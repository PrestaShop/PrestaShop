{*
* 2007-2011 PrestaShop
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
*  @version  Release: $Revision: 9795 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{extends file="../helper/form/form.tpl"}

{block name="label"}

	{if $input.type == 'text' && $input.name == 'ps_cache_fs_directory_depth'}
		<div id="directory_depth">
			<div class="warn">{l s='The system CacheFS should be used only when the infrastructure contain only one front-end server. Ask your hosting company if you don\'t know.'}</div>
	{/if}

	{if isset($input.label)}
		<label>{$input.label} </label>
	{/if}

{/block}

{block name="start_field_block"}
	<div class="margin-form">
	{if $input.type == 'radio' && $input.name == 'combination' && $combination}
		<div class="warn">
			<img src="../img/admin/warn2.png">
			{l s='This feature can\'t be disabled because this is currently in use.'}
		</div>
	{/if}
{/block}

{block name="end_field_block"}
	{if $input.type == 'radio' && $input.name == 'combination'}
		<ul style="list-style-type:disc;margin:0 0 0 30px;">
			<li>{l s='Combinations tab on product page'}</li>
			<li>{l s='Attribute'}</li>
			<li>{l s='Group of attribute'}</li>
		</ul>
	{elseif $input.type == 'radio' && $input.name == 'feature'}
		<ul style="list-style-type:disc;margin:0 0 0 30px;">
			<li>{l s='Features tab on product page'}</li>
			<li>{l s='Feature'}</li>
			<li>{l s='Feature value'}</li>
		</ul>
	{elseif $input.type == 'text' && $input.name == 'ps_cache_fs_directory_depth'}
		</div>
	{/if}
	</div>
{/block}

{block name="other_input"}
	{if $key == 'memcachedServers'}
		<div id="memcachedServers">
			<div class="margin-form">
				<a id="addMemcachedServer" href="#" ><img src="../img/admin/add.gif" />{l s='Add server'}</a>
			</div>
			<div id="formMemcachedServer" style="margin-top: 10px; display:none;">
				<form action="{$current}&token={$token}" method="post">
					<label>{l s='IP Address:'} </label>
					<div class="margin-form">
						<input type="text" name="memcachedIp" />
					</div>
					<label>{l s='Port:'} </label>
					<div class="margin-form">
						<input type="text" name="memcachedPort" value="11211" />
					</div>
					<label>{l s='Weight:'} </label>
					<div class="margin-form">
						<input type="text" name="memcachedWeight" value="1" />
					</div>
					<div class="margin-form">
						<input type="submit" value="{l s='   Add Server   '}" name="submitAddServer" class="button" />
					</div>
				</form>
			</div>
			{if $servers}
				<div class="margin-form">
					<table style="width: 320px;" cellspacing="0" cellpadding="0" class="table">
					<tr>
						<th style="width: 20px; text-align: center">{l s='Id'}</th>
						<th style="width: 200px; text-align: center">{l s='Ip'}</th>
						<th style="width: 50px; text-align: center">{l s='Port'}</th>
						<th style="width: 30px; text-align: right; font-weight: bold;">{l s='Weight'}</th>
						<th style="width: 20px; text-align: right;">&nbsp;</th>
					</tr>
					{foreach $servers AS $server}
						<tr>
							<td>{$server.id_memcached_server}</td>
							<td>{$server.ip}</td>
							<td>{$server.port}</td>
							<td>{$server.weight}</td>
							<td>
								<a href="{$current}&token={$token}&deleteMemcachedServer={$server.id_memcached_server}" >
									<img src="../img/admin/delete.gif" />
								</a>
							</td>
						</tr>
					{/foreach}
					</table>
				</div>
			{/if}
		</div>
	{/if}
{/block}

{block name="script"}

	$(document).ready(function() {

		showMemcached();

		$('#caching_system').change(function() {
			showMemcached();
		});
	
		function showMemcached()
		{
			if ($('#caching_system option:selected').val() == 'CacheMemcache')
			{
				$('#memcachedServers').show();
				$('#directory_depth').hide();
			}
			else
			{
				$('#memcachedServers').hide();
				$('#directory_depth').show();
			}
		}
	
		$('#addMemcachedServer').click(function() {
			$('#formMemcachedServer').show();
			return false;
		});

		var btn_save_feature = $('span[class~="process-icon-save-feature"]').parent();
		var btn_submit_feature = $('#submitFeaturesDetachables');

		if (btn_save_feature.length > 0 && btn_submit_feature.length > 0)
		{
			btn_submit_feature.hide();
			btn_save_feature.find('span').removeClass('process-icon-save-feature');
			btn_save_feature.find('span').addClass('process-icon-save');
			btn_save_feature.click(function() {
				btn_submit_feature.before('<input type="hidden" name="'+btn_submit_feature.attr("name")+'" value="1" />');

				$('#features_detachables_form').submit();
			});
		}

		var btn_save_ccc = $('span[class~="process-icon-save-ccc"]').parent();
		var btn_submit_ccc = $('#submitCCC');

		if (btn_save_ccc.length > 0 && btn_submit_ccc.length > 0)
		{
			btn_submit_ccc.hide();
			btn_save_ccc.find('span').removeClass('process-icon-save-ccc');
			btn_save_ccc.find('span').addClass('process-icon-save');
			btn_save_ccc.click(function() {
				btn_submit_ccc.before('<input type="hidden" name="'+btn_submit_ccc.attr("name")+'" value="1" />');

				$('#ccc_form').submit();
			});
		}

		var btn_save_media = $('span[class~="process-icon-save-media"]').parent();
		var btn_submit_media = $('#submitMediaServers');

		if (btn_save_media.length > 0 && btn_submit_media.length > 0)
		{
			btn_submit_media.hide();
			btn_save_media.find('span').removeClass('process-icon-save-media');
			btn_save_media.find('span').addClass('process-icon-save');
			btn_save_media.click(function() {
				btn_submit_media.before('<input type="hidden" name="'+btn_submit_media.attr("name")+'" value="1" />');

				$('#media_server_form').submit();
			});
		}

		var btn_save_ciphering = $('span[class~="process-icon-save-ciphering"]').parent();
		var btn_submit_ciphering = $('#submitCiphering');

		if (btn_save_ciphering.length > 0 && btn_submit_ciphering.length > 0)
		{
			btn_submit_ciphering.hide();
			btn_save_ciphering.find('span').removeClass('process-icon-save-ciphering');
			btn_save_ciphering.find('span').addClass('process-icon-save');
			btn_save_ciphering.click(function() {
				btn_submit_ciphering.before('<input type="hidden" name="'+btn_submit_ciphering.attr("name")+'" value="1" />');

				$('#ciphering_form').submit();
			});
		}

		var btn_save_caching = $('span[class~="process-icon-save-caching"]').parent();
		var btn_submit_caching = $('#submitCaching');

		if (btn_save_caching.length > 0 && btn_submit_caching.length > 0)
		{
			btn_submit_caching.hide();
			btn_save_caching.find('span').removeClass('process-icon-save-caching');
			btn_save_caching.find('span').addClass('process-icon-save');
			btn_save_caching.click(function() {
				btn_submit_caching.before('<input type="hidden" name="'+btn_submit_caching.attr("name")+'" value="1" />');

				$('#caching_form').submit();
			});
		}
	});

{/block}
