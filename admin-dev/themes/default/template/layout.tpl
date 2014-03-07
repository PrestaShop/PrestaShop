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
{$header}

{if isset($conf)}
	<div class="bootstrap">
		<div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			{$conf}
		</div>
	</div>
{/if}
{if count($errors) && (!isset($disableDefaultErrorOutPut) || $disableDefaultErrorOutPut == false)}
	<div class="bootstrap">
		<div class="alert alert-danger">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		{if count($errors) == 1}
			{reset($errors)}
		{else}
			{l s='%d errors' sprintf=$errors|count}
			<br/>
			<ol>
				{foreach $errors as $error}
					<li>{$error}</li>
				{/foreach}
			</ol>
		{/if}
		</div>
	</div>
{/if}
{if isset($informations) && count($informations) && $informations}
	<div class="alert alert-info">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<ul id="infos_block" class="list-unstyled">
			{foreach $informations as $info}
				<li>{$info}</li>
			{/foreach}
		</ul>
	</div>
{/if}
{if isset($confirmations) && count($confirmations) && $confirmations}
	<div class="alert alert-success" style="display:block;">
		{foreach $confirmations as $conf}
			{$conf}
		{/foreach}
	</div>
{/if}
{if count($warnings)}
	<div class="alert alert-warning">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		{if count($warnings) > 1}
			<strong>{l s='There are %d warnings:' sprintf=count($warnings)}</strong>
		{/if}
		<ul class="list-unstyled">
			{foreach $warnings as $warning}
				<li>{$warning}</li>
			{/foreach}
		</ul>
	</div>
{/if}
{$page}
{$footer}
