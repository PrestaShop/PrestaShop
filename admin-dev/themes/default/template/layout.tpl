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
{include file='header.tpl'}
{if isset($conf)}
	<div class="alert">
		{$conf}
	</div>
{/if}
{if count($errors) && (!isset($disableDefaultErrorOutPut) || $disableDefaultErrorOutPut == false)}
	<div class="alert alert-danger">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		
		{if count($errors) == 1}
			{$errors[0]}
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
{/if}
{if isset($informations) && count($informations) && $informations}
	<div class="alert alert-info">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<a id="see_more_infos" href="#" class="btn btn-link" onclick="$('#see_more_infos').hide(); $('#infos_block').show();return false;">
			<i class="icon-info-sign"></i> {l s='Click here to see more informations'}
		</a>
		<ul id="infos_block" class="list-unstyled" style="display:none;">
			{foreach $informations as $info}
				<li>{$info}</li>
			{/foreach}
		</ul>
	</div>
{/if}
{if isset($confirmations) && count($confirmations) && $confirmations}
	<div class="alert" style="display:block;">
		{foreach $confirmations as $conf}
			{$conf}<br />
		{/foreach}
	</div><br />
{/if}
{if count($warnings)}
	<div class="alert alert-block">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		{if count($warnings) > 1}
			{l s='There are %d warnings.' sprintf=count($warnings)}
			<span id="labelSeeMore">
				<a id="linkSeeMore" href="#" style="text-decoration:underline">{l s='Click here to see more'}</a>
				<a id="linkHide" href="#" style="text-decoration:underline;display:none">{l s='Hide warning'}</a>
			</span>
			<ul {if count($warnings) > 1}style="display:none;"{/if} id="seeMore">
			{foreach $warnings as $warning}
				<li>{$warning}</li>
			{/foreach}
			</ul>
		{else}
			<ul >
			{foreach $warnings as $warning}
				<li>{$warning}</li>
			{/foreach}
			</ul>
		{/if}
	</div>
{/if}

{if !$bootstrap}<div id="nobootstrap">{/if}
{$page}
{if !$bootstrap}</div>{/if}

{include file='footer.tpl'}