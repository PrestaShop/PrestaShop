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
{if $PS_SC_TWITTER || PS_SC_FACEBOOK || PS_SC_GOOGLE}
	<div class="socialsharing_product">
{if $PS_SC_TWITTER}
		<a href="javascript:void(0);" onclick="socialsharing_twitter_click('{$product->name|addslashes} {$link->getProductLink($product)|addslashes}');">
			<img src="{$link->getMediaLink("`$module_dir`img/twitter.gif")}" alt="Tweet" />
		</a>
{/if}
{if $PS_SC_FACEBOOK}
		<a href="javascript:void(0);" onclick="socialsharing_facebook_click();">
			<img src="{$link->getMediaLink("`$module_dir`img/facebook.gif")}" alt="Facebook Like" />
		</a>
{/if}
{if $PS_SC_GOOGLE}
		<a href="javascript:void(0);" onclick="socialsharing_google_click();">
			<img src="{$link->getMediaLink("`$module_dir`img/google.gif")}" alt="Google Plus" />
		</a>
{/if}
	</div>
{/if}