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
	{if $input.type == 'radio' && $input.name == 'combination' && $input.disabled}
		<div class="alert alert-warning">
			{l s='This feature cannot be disabled because it is currently in use.'}
		</div>
	{/if}
	{$smarty.block.parent}
	{if in_array($input.type, array('radio', 'switch')) && $input.name == 'smarty_cache'}
			<a href="{$current}&token={$token}&empty_smarty_cache=1" class="clear button" href="">{l s='Clear Smarty cache & Autoload cache'}</a>
	{/if}		
{/block}

{block name="description"}
	{$smarty.block.parent}
	{if $input.type == 'radio' && $input.name == 'combination'}
		<ul>
			<li>{l s='Combinations tab on product page'}</li>
			<li>{l s='Value'}</li>
			<li>{l s='Attribute'}</li>
		</ul>
	{elseif $input.type == 'radio' && $input.name == 'feature'}
		<ul>
			<li>{l s='Features tab on product page'}</li>
			<li>{l s='Feature'}</li>
			<li>{l s='Feature value'}</li>
		</ul>
	{/if}
{/block}

{block name="other_input"}
	{if $key == 'memcachedServers'}
		<div id="memcachedServers">
			<div class="form-group">
				<div class="col-lg-9 col-lg-push-3">
					<button id="addMemcachedServer" class="btn btn-default" type="button" >
						<i class="icon-plus-sign-alt"></i>&nbsp;{l s='Add server'}
					</button>
				</div>
			</div>
			<div id="formMemcachedServer" style="display:none;">
				<form action="{$current}&token={$token}" method="post" class="form-horizontal">
					<div class="form-group">
						<label class="control-label col-lg-3">{l s='IP Address'} </label>
						<div class="col-lg-9">
							<input class="form-control" type="text" name="memcachedIp" />
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-lg-3">{l s='Port'} </label>
						<div class="col-lg-9">
							<input class="form-control" type="text" name="memcachedPort" value="11211" />
						</div>
					</div>
					<div class="form-group">	
						<label class="control-label col-lg-3">{l s='Weight'} </label>
						<div class="col-lg-9">
							<input class="form-control" type="text" name="memcachedWeight" value="1" />
						</div>
					</div>
					<div class="form-group">
						<div class="col-lg-9 col-lg-push-3">
							<input type="submit" value="{l s='Add Server'}" name="submitAddServer" class="btn btn-default" />
							<input type="button" value="{l s='Test Server'}" id="testMemcachedServer" class="btn btn-default" />
	                	</div>
					</div>
				</form>
			</div>
			{if $servers}
			<div class="row">
				<div class="form-group">
					<table class="table">
						<thead>
							<tr>
								<th class="fixed-width-xs"><span class="title_box">{l s='ID'}</span></th>
								<th><span class="title_box">{l s='IP address'}</span></th>
								<th class="fixed-width-xs"><span class="title_box">{l s='Port'}</span></th>
								<th class="fixed-width-xs"><span class="title_box">{l s='Weight'}</span></th>
								<th>&nbsp;</th>
							</tr>
						</thead>
						<tbody>
					{foreach $servers AS $server}
						<tr>
							<td>{$server.id_memcached_server}</td>
							<td>{$server.ip}</td>
							<td>{$server.port}</td>
							<td>{$server.weight}</td>
							<td align="right">
								<a class="btn btn-default" href="{$current}&token={$token}&deleteMemcachedServer={$server.id_memcached_server}" onclick="if (!confirm('{l s='Do you really want to remove the server %s:%s'|sprintf:$server.ip:$server.port}')) return false;"><i class="icon-minus-sign-alt"></i> {l s='Remove'}</a>
							</td>
						</tr>
					{/foreach}
						</tbody>
					</table>
				</div>
			</div>
			{/if}
		</div>
	{/if}
{/block}

{block name="script"}

	function showMemcached()
	{
		if ($('input[name="caching_system"]:radio:checked').val() == 'CacheMemcache')
		{
			$('#memcachedServers').show();
			$('#ps_cache_fs_directory_depth').parent().parent().hide();
		}
		else
		{
			$('#memcachedServers').hide();
			$('#ps_cache_fs_directory_depth').parent().parent().show();
		}
	}

	$(document).ready(function() {

		showMemcached();

		$('input[name="caching_system"]').change(function() {
			$('#cache_up').val(1);
			showMemcached();
		});

		$('#addMemcachedServer').click(function() {
			$('#formMemcachedServer').show();
			return false;
		});

		$('#testMemcachedServer').click(function() {
			var host = $('input:text[name=memcachedIp]').val();
			var port = $('input:text[name=memcachedPort]').val();
			if (host && port)
			{
				$.ajax({
					url: 'index.php',
					data:
					{
						controller: 'adminperformance',
						token: '{$token}',
						action: 'test_server',
						sHost: host,
						sPort: port,
						ajax: true
					},
					context: document.body,
					dataType: 'json',
					context: this,
					async: false,
					success: function(data)
					{
						if (data && $.isArray(data))
						{
							var color = data[0] != 0 ? 'green' : 'red';
							$('#formMemcachedServerStatus').show();
							$('input:text[name=memcachedIp]').css('background', color);
							$('input:text[name=memcachedPort]').css('background', color);
						}
					}
				});
			}
			return false;
		});

		$('input[name="smarty_force_compile"], input[name="smarty_cache"], input[name="smarty_console"], input[name="smarty_console_key"]').change(function(){
			$('#smarty_up').val(1);
		});

		$('input[name="combination"], input[name="feature"]').change(function(){
			$('#features_detachables_up').val('true');
		});

		$('input[name="_MEDIA_SERVER_1_"], input[name="_MEDIA_SERVER_2_"], input[name="_MEDIA_SERVER_3_"]').change(function(){
			$('#media_server_up').val(1);
		});

		$('input[name="PS_CIPHER_ALGORITHM"]').change(function(){
			$('#ciphering_up').val(1);
		});

		$('input[name="cache_active"]').change(function(){
			$('#cache_up').val(1);
		});
	});

{/block}
