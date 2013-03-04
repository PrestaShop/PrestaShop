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
<table style="width: 100%">
<tr>
	<td style="width: 50%">
		{if $logo_path}
			<img src="{$logo_path}" style="width:{$width_logo}px; height:{$height_logo}px;" />
		{/if}
	</td>
	<td style="width: 50%; text-align: right;">
		<table style="width: 100%">
			<tr>
				<td style="font-weight: bold; font-size: 14pt; color: #444; width: 100%">{$shop_name|escape:'htmlall':'UTF-8'}</td>
			</tr>
			<tr>
				<td style="font-size: 14pt; color: #9E9F9E">{$date|escape:'htmlall':'UTF-8'}</td>
			</tr>
			<tr>
				<td style="font-size: 14pt; color: #9E9F9E">{$title|escape:'htmlall':'UTF-8'}</td>
			</tr>
		</table>
	</td>
</tr>
</table>

