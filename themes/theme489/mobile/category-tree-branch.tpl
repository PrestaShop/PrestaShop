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
*  @version  Release: $Revision: 6594 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<li {if $node.children|@count > 0}data-icon="more"{/if}>
	{if $node.children|@count > 0}
		{$node.name|escape:'htmlall':'UTF-8'}
		<ul data-inset="true">
			<li>
				<a href="{$node.link|escape:'htmlall':'UTF-8'}" title="{$node.desc|escape:'htmlall':'UTF-8'}" data-ajax="false">
					{l s='See products'}
				</a>
			</li>
		{foreach from=$node.children item=child name=categoryTreeBranch}
			{include file="$tpl_dir./category-tree-branch.tpl" node=$child}
		{/foreach}
		</ul>
	{else}
		<a href="{$node.link|escape:'htmlall':'UTF-8'}" title="{$node.desc|escape:'htmlall':'UTF-8'}" data-ajax="false">
			{$node.name|escape:'htmlall':'UTF-8'}
		</a>
	{/if}
</li>
