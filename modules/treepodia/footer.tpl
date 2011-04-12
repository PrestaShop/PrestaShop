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
<ul class="block_various_links" id="block_various_links_footer">
	<li class="first_item">
		<a href="#promovideo" onclick="javascript:document.getElementById('Promovideo').style.display = 'inline';">{l s='Watch Video' mod='treepodia'}</a><br/>
	</li>
</ul>
<div id="Promovideo" style="display:none;">
<a name="promovideo"/>
<center><embed width="400px" height="300px" flashvars="callback=_trpd_video_callback_0&amp;video=http://api.treepodia.com/video/get/{$account_id}/{$product_sku}&amp;auto-play=false&amp;backcolor=0x000000&amp;frontcolor=0xCCCCCC&amp;lightcolor=0x557722&amp;allowfullscreen=false&amp;ShowLogo=1&amp;play_on_click=true&amp;init_mute=false" wmode="opaque" allowfullscreen="false" swliveconnect="true" allowscriptaccess="always" quality="high" bgcolor="ffffff" name="PromoVid" id="PromoVidId" src="http://api.treepodia.com/video/treepodia_player.swf" type="application/x-shockwave-flash"/></center>
</div>

<script type="text/javascript">
<!--
    document.write(unescape("%3Cscript src='" + ((document.location.protocol == 'https:') ? 'https://' : 'http://') + "api.treepodia.com/video/Treepodia.js' type='text/javascript'%3E%3C/script%3E"));
// -->
</script>
<script type="text/javascript">
{literal}
<!--
    document.write(unescape("%3Cscript src='" + ((document.location.protocol == 'https:') ? 'https://' : 'http://') + "api.treepodia.com/utils/ps14-video-dialog.js?account={/literal}{$account_id}{literal}' type='text/javascript'%3E%3C/script%3E"));
// -->
{/literal}
</script>

<script type="text/javascript">
{literal}
<!--
    var video;
    function initTreepodia() {
        Treepodia.getProduct('{/literal}{$account_id}{literal}', '{/literal}{$product_sku}{literal}').requestVideo(handleVideo, '{/literal}{$lang_iso}{literal}');
    }
    function handleVideo(vid) {
        video = vid;
        if(vid.hasVideos()) {
            document.getElementById('trpd-img-btn').style.display = 'inline';
        }
    }
// -->
{/literal}
</script>