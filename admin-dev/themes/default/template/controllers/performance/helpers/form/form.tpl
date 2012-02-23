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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 9795 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{extends file="helpers/form/form.tpl"}

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
	{if $input.type == 'radio' && $input.name == 'combination' && $input.disabled}
		<div class="warn">
			{l s='This feature can\'t be disabled because this is currently in use.'}
		</div>
	{/if}
{/block}

{block name="end_field_block"}
	{if $input.type == 'radio' && $input.name == 'combination'}
		<ul style="list-style-type:disc;margin:0 0 0 30px;">
			<li>{l s='Combinations tab on product page'}</li>
			<li>{l s='Value'}</li>
			<li>{l s='Attribute'}</li>
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
				<a id="addMemcachedServer"  class="button" href="#" ><img src="../img/admin/add.gif" />{l s='Add server'}</a>
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

	$(document).ready(function() {

		showMemcached();

		$('#caching_system').change(function() {
			showMemcached();
		});
	
		$('#addMemcachedServer').click(function() {
			$('#formMemcachedServer').show();
			return false;
		});

		$('input[name="smarty_force_compile"], input[name="smarty_cache"], input[name="smarty_console"]').change(function(){
			$('#smarty_up').val(1);
		});

		$('input[name="combination"], input[name="feature"]').change(function(){
			$('#features_detachables_up').val('true');
		});

		$('input[name="PS_CSS_THEME_CACHE"], input[name="PS_JS_THEME_CACHE"], input[name="PS_HTML_THEME_COMPRESSION"], input[name="PS_JS_HTML_THEME_COMPRESSION"], input[name="PS_HIGH_HTML_THEME_COMPRESSION"]').change(function(){
			$('#ccc_up').val(1);
		});

		$('input[name="_MEDIA_SERVER_1_"], input[name="_MEDIA_SERVER_2_"], input[name="_MEDIA_SERVER_3_"]').change(function(){
			$('#media_server_up').val(1);
		});

		$('input[name="PS_CIPHER_ALGORITHM"]').change(function(){
			$('#ciphering_up').val(1);
		});

		$('input[name="active"], select[name="caching_system"]').change(function(){
			$('#cache_up').val(1);
		});

	});

{/block}
