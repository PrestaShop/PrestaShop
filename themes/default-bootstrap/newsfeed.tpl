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
{if isset($newsfeed) && !isset($newsfeed_category)}
	{if !$newsfeed->active}
		<br />
		<div id="admin-action-newsfeed">
			<p>{l s='This Newsfeed page is not visible to your customers.'}
			<input type="hidden" id="admin-action-newsfeed-id" value="{$newsfeed->id}" />
			<input type="submit" value="{l s='Publish'}" class="exclusive btn btn-default" onclick="submitPublishNewsfeed('{$base_dir}{$smarty.get.ad|escape:'html':'UTF-8'}', 0, '{$smarty.get.adtoken|escape:'html':'UTF-8'}')"/>
			<input type="submit" value="{l s='Back'}" class="exclusive btn btn-default" onclick="submitPublishNewsfeed('{$base_dir}{$smarty.get.ad|escape:'html':'UTF-8'}', 1, '{$smarty.get.adtoken|escape:'html':'UTF-8'}')"/>
			</p>
			<div class="clear" ></div>
			<p id="admin-action-result"></p>
			</p>
		</div>
	{/if}
	<div class="rte{if $content_only} content_only{/if}">
		<h3>{dateFormat date=$newsfeed->date_add|escape:'html':'UTF-8' full=0} - {$newsfeed->meta_title}</h3>
		{$newsfeed->content}
	</div>
{elseif isset($newsfeed_category)}
	<div class="block-newsfeed">
		<h1><a href="{if $newsfeed_category->id eq 1}{$base_dir}{else}{$link->getNewsfeedCategoryLink($newsfeed_category->id, $newsfeed_category->link_rewrite)}{/if}">{$newsfeed_category->name|escape:'html':'UTF-8'}</a></h1>
		{if isset($sub_category) && !empty($sub_category)}	
			<p class="title_block">{l s='Sub categories in %s:' sprintf=$newsfeed_category->name}</p>
			<ul class="bullet">
				{foreach from=$sub_category item=subcategory}
					<li>
						<a href="{$link->getNewsfeedCategoryLink($subcategory.id_newsfeed_category, $subcategory.link_rewrite)|escape:'html':'UTF-8'}">{$subcategory.name|escape:'html':'UTF-8'}</a>
					</li>
				{/foreach}
			</ul>
		{/if}
		{if isset($newsfeed_pages) && !empty($newsfeed_pages)}
		<p class="title_block">{l s='Pages in %s:' sprintf=$newsfeed_category->name}</p>
			<ul class="bullet">
				{foreach from=$newsfeed_pages item=newsfeedpages}
					<li>
						<a href="{$link->getNewsfeedLink($newsfeedpages.id_newsfeed, $newsfeedpages.link_rewrite)|escape:'html':'UTF-8'}">{dateFormat date=$newsfeedpages.date_add|escape:'html':'UTF-8' full=0} - {$newsfeedpages.meta_title|escape:'html':'UTF-8'}</a>
						<a href="{$link->getNewsfeedLink($newsfeedpages.id_newsfeed, $newsfeedpages.link_rewrite)|escape:'html':'UTF-8'}" class="newsfeed_short_content">
							{if isset($newsfeedpages.short_content) && !empty($newsfeedpages.short_content)}
								{nl2br($newsfeedpages.short_content)}
							{else}
								{nl2br( substr( strip_tags($newsfeedpages.content),0,300) ) }...
							{/if}
						</a>
					</li>
				{/foreach}
			</ul>
		{/if}
	</div>
{else}
	<div class="alert alert-danger">
		{l s='This page does not exist.'}
	</div>
{/if}
<br />