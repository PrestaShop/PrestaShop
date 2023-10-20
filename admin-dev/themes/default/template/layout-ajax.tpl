{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *}
{if isset($json)}
{strip}
{
{if isset($status) && is_string($status) && trim($status) != ''}{assign 'hasresult' 'ok'}"status" : "{$status}"{/if}
{if !empty($confirmations)}{if $hasresult == 'ok'},{/if}{assign 'hasresult' 'ok'}"confirmations" : {$confirmations}{/if}
{if !empty($informations)}{if $hasresult == 'ok'},{/if}{assign 'hasresult' 'ok'}"informations" : {$informations}{/if}
{if !empty($errors)}{if $hasresult == 'ok'},{/if}{assign 'hasresult' 'ok'}"error" : {$errors}{/if}
{if !empty($warnings)}{if $hasresult == 'ok'},{/if}{assign 'hasresult' 'ok'}"warnings" : {$warnings}{/if}
{if $hasresult == 'ok'},{/if}{assign 'hasresult' 'ok'}"content" : {$page}
}
{/strip}
{else}
	{if isset($conf)}
		<div class="alert alert-success">
			{$conf}
		</div>
	{/if}

	{if count($errors)}
		<div class="alert alert-danger">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			{if count($errors) == 1}
				{$errors[0]}
			{else}
				{l s='There are %d errors.' sprintf=[$errors|count] d='Admin.Notifications.Error'}
				<br/>
				<ul>
					{foreach $errors AS $error}
						<li>{$error}</li>
					{/foreach}
				</ul>
			{/if}
		</div>
	{/if}

	{if isset($informations) && count($informations) && $informations}
		<div class="alert alert-info" style="display:block;">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			{foreach $informations as $info}
				{$info}<br/>
			{/foreach}
		</div>
	{/if}

	{if isset($confirmations) && count($confirmations) && $confirmations}
		<div class="alert alert-success" style="display:block;">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			{foreach $confirmations as $confirm}
				{$confirm}<br />
			{/foreach}
		</div>
	{/if}

	{if count($warnings)}
		<div class="alert alert-warning">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			{if count($warnings) > 1}
				{l s='There are %d warnings.' sprintf=count($warnings) d='Admin.Notifications.Error'}
				<span style="margin-left:20px;" id="labelSeeMore">
					<a id="linkSeeMore" href="#" style="text-decoration:underline">{l s='See more' d='Admin.Actions'}</a>
					<a id="linkHide" href="#" style="text-decoration:underline;display:none">{l s='Hide warning' d='Admin.Actions'}</a>
				</span>
			{else}
				{l s='There are %d warnings.' sprintf=count($warnings) d='Admin.Notifications.Error'}
			{/if}
			<ul style="display:{if count($warnings) > 1}none{else}block{/if};" id="seeMore">
			{foreach $warnings as $warning}
				<li>{$warning}</li>
			{/foreach}
			</ul>
		</div>
	{/if}
	{$page}
{/if}
