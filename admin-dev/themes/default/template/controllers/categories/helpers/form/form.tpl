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
		{if isset($input.display_image) && $input.display_image}
			{if isset($fields_value.image) && $fields_value.image}
				<div id="image">
					{$fields_value.image}
					<p align="center">{l s='File size'} {$fields_value.size}kb</p>
					{if $shared_category}
						<p class="alert alert-warning">{l s='If you delete this picture, it will be deleted in all of your shared shops!'}</p>
					{/if}
					<a class="btn btn-default" href="{$current}&{$identifier}={$form_id}&token={$token}&{if $shared_category}forcedeleteImage=1{else}deleteImage=1{/if}">
						<i class="icon-trash"></i> {l s='Delete'}
					</a>
				</div>
			{/if}
		{/if}
		<input type="file" name="{$input.name}" {if isset($input.id)}id="{$input.id}"{/if} />
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