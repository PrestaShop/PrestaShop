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
*  @version  Release: $Revision: 8971 $
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
						<p class="warn">{l s='If you delete this picture it\'s will be deleted for all shared shop'}</p>
					{/if}
					<br>
					<a href="{$current}&{$identifier}={$form_id}&token={$token}&{if $shared_category}forcedeleteImage=1{else}deleteImage=1{/if}">
						<img src="../img/admin/delete.gif" alt="{l s='Delete'}" /> {l s='Delete'}
					</a>
				</div><br />
			{/if}
		{/if}
		<input type="file" name="{$input.name}" {if isset($input.id)}id="{$input.id}"{/if} />
		{if !empty($input.hint)}<span class="hint" name="help_box">{$input.hint}<span class="hint-pointer">&nbsp;</span></span>{/if}
	{else}
		{$smarty.block.parent}
	{/if}
{/block}
{block name="description"}
	{$smarty.block.parent}
	{if ($input.name == 'groupBox')}
		<p class="hint" style="display:block; position:relative; display:inline-block;">
			<span>{$input.info_introduction}</span><br />
			<span>{$input.unidentified}</span><br />
			<span>{$input.guest}</span><br />
			<span>{$input.customer}</span><br />
		</p>
	{/if}
{/block}
