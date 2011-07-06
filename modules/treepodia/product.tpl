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
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div style="width: 100%; text-align: {$position}"><img id="trpd-img-btn"  style="display: none;" src="{$img_src}" onclick="showVideoDialog(video);"></div>
{literal}
<script type="text/javascript">document.write(unescape("%3Cstyle%3E.trpdVidDvs {display:none;}%3C/style%3E"));</script>
<noscript><style>.trpdVidDvs {display:inline-block;}</style></noscript>
<div id="trpdVideoDiv" class="trpdVidDvs">
<object type="application/x-shockwave-flash" data="http://api.treepodia.com/video/treepodia_player.swf" type="application/x-shockwave-flash" width="400px" height="300px" title="product video player" rel="media:video">
<param name="src" value="http://api.treepodia.com/video/treepodia_player.swf"/>
<param name="flashvars" value="video=http://api.treepodia.com/video/get/{/literal}{$account_id}{literal}/{/literal}{$product_sku}{literal}&amp;auto-play=false&amp;backcolor=0x000000&amp;frontcolor=0xCCCCCC&amp;lightcolor=0x557722&amp;allowfullscreen=false&amp;ShowLogo=1&amp;play_on_click=true"/></object>
{/literal}
</div>
