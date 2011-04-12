{*
* 2007-2011 PrestaShop
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{capture name=path}{l s='Send to a friend' mod='sendtoafriend'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

<h1>{l s='Send to a friend' mod='sendtoafriend'}</h1>

<p class="bold">{l s='Send this page to a friend who might be interested in the item below.' mod='sendtoafriend'}.</p>
{include file="$tpl_dir./errors.tpl"}

{if isset($smarty.get.submited)}
	<p class="success">{l s='Your email has been sent successfully' mod='sendtoafriend'}</p>
{else}
	<form method="post" action="{$request_uri}" class="std">
		<fieldset>
			<h3>{l s='Send a message' mod='sendtoafriend'}</h3>

			<p class="align_center">
				<a href="{$productLink}"><img src="{$link->getImageLink($product->link_rewrite, $cover.id_image, 'small')}" alt="" title="{$cover.legend}" /></a><br/>
				<a href="{$productLink}">{$product->name}</a>
			</p>

			<p>
				<label for="friend-name">{l s='Friend\'s name:' mod='sendtoafriend'}</label>
				<input type="text" id="friend-name" name="name" value="{if isset($smarty.post.name)}{$smarty.post.name|escape:'htmlall':'UTF-8'|stripslashes}{/if}" />
			</p>
			<p>
				<label for="friend-address">{l s='Friend\'s email:' mod='sendtoafriend'}</label>
				<input type="text" id="friend-address" name="email" value="{if isset($smarty.post.name)}{$smarty.post.email|escape:'htmlall'|stripslashes}{/if}" />
			</p>

			<p class="submit">
				<input type="submit" name="submitAddtoafriend" value="{l s='send' mod='sendtoafriend'}" class="button" />
			</p>
		</fieldset>
	</form>
{/if}

<ul class="footer_links">
	<li><a href="{$productLink}" class="button_large">{l s='Back to product page' mod='sendtoafriend'}</a></li>
</ul>

