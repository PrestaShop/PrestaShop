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

<div style="margin:10px 0">
{if isset($LIVEZILLA_SCRIPT)}
	{$LIVEZILLA_SCRIPT}
{elseif isset($LIVEZILLA_URL)}
	<a href="javascript:void(window.open('{$LIVEZILLA_URL}chat.php','','width=590,height=610,left=0,top=0,resizable=yes,menubar=no,location=no,status=yes,scrollbars=yes'))">
		<img src="{$LIVEZILLA_URL}image.php?id=01" width="191" height="69" border="0" alt="{l s='LiveZilla Live Help'}" />
	</a>
	<noscript>
		<div>
			<a href="{$LIVEZILLA_URL}chat.php" target="_blank">Start Live Help Chat</a>
		</div>
	</noscript>
	<div id="livezilla_tracking" style="display:none"></div>
	<script type="text/javascript">
	/* <![CDATA[ */
		var script = document.createElement("script");
		script.type = "text/javascript";
		var src = "{$LIVEZILLA_URL}server.php?request=track&output=jcrpt&nse=" + Math.random();
		setTimeout("script.src = src;document.getElementById('livezilla_tracking').appendChild(script)", 1);
	/* ]]> */
	</script>
	<noscript>
		<img src="{$LIVEZILLA_URL}server.php?request=track&amp;output=nojcrpt" width="0" height="0" style="visibility:hidden" alt="" />
	</noscript>
{else}
	<img src="{$base_dir}modules/livezilla/offline.png" width="191" height="69" border="0" alt="LiveZilla" />
{/if}
</div>