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

{extends file="helpers/list/list_header.tpl"}

{block name=leadin}
<div class="cat_bar2">
	{assign var=i value=0}
	{foreach $categories_tree key=key item=category}
		{if $i++ == 0}
			&nbsp;<img src="../img/admin/home.gif" alt="" />
			{assign var=params_url value=""}
		{else}
			{assign var=params_url value="&id_category={$category.id_category}&viewcategory"}
		{/if}
		{if $key == 0}
			{$category.name}
		{else}
			<a href="{$currentIndex}{$params_url}&token={$token}">{$category.name}</a>&nbsp;>&nbsp;
		{/if}
	{/foreach}
</div>
	{if isset($delete_category) && $delete_category}
		<form action="{$REQUEST_URI}" method="post">
			<div class="warn">
				<h2>
					{if $need_delete_mode}
						{l s='Do you want to delete the products too?'}
					{else}
						{l s='Deleting this category will remove products linked only within this category and no others. Are you sure you want to continue?'}
					{/if}
				</h2>
				{if $need_delete_mode}
				<ul class="listForm">
				<li>
					<input type="radio" name="deleteMode" value="linkanddisable" id="deleteMode_linkanddisable" />
					<label for="deleteMode_linkanddisable" style="float:none;">{l s='No. I want to link products without other categories -- within the parent category -- and then disable them.'}</label>
				</li>
				<li>
					<input type="radio" name="deleteMode" value="link" id="deleteMode_link" />
					<label for="deleteMode_link" style="float:none;">{l s='No. I want to link products without other categories and within the parent category.'}</label>
				</li>
				<li>
					<input type="radio" name="deleteMode" value="delete" id="deleteMode_delete" />
					<label for="deleteMode_delete" style="float:none">{l s='Yes. I want to remove products linked only within this category and no others.'}</label>
				</li>
				</ul>
				{else}
					<input type="hidden" name="deleteMode" value="delete" id="deleteMode_delete" />
				{/if}
				{foreach $POST as $key => $value}
					{if $key != 'deleteMode'}
						{if is_array($value)}
							{foreach $value as $val}
								<input type="hidden" name="{$key}[]" value="{$val}" />
							{/foreach}
						{else}
							<input type="hidden" name="{$key}" value="{$value}" />
						{/if}
					{/if}
				{/foreach}
				<br />
				<input type="submit" name="cancel" class="button" value="{l s='Cancel'}" />
				<input type="submit" class="button" value="{l s='Validate'}" />
			</div>
		</form>
		<div class="clear">&nbsp;</div>
	{/if}
{/block}
