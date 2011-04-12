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
<script type="text/javascript">
	{if isset($ad) && isset($live_edit)}
	var ad = "{$smarty.get.ad}";
	{/if}
	var lastMove = '';
	var saveOK = '{l s='Module position saved'}';
	var confirmClose = '{l s='Are you sure ? If you close this window, position wont be save'}';
	var close = '{l s='Close'}';
	var cancel = '{l s='Cancel'}';
	var confirm = '{l s='Confirm'}';
	var add = '{l s='Add this module'}';
	var unableToUnregisterHook = '{l s='Unable to unregister hook'}';
	var unableToSaveModulePosition = '{l s='Unable to save module position'}';
	var loadFail = '{l s='Failed to load module list'}';
	var baseDir = '{$base_dir}';
</script>

<div style="width:100%;height:30px;padding-top:10px;background-color:#D0D3D8;border:solid 1px gray;position:fixed;bottom:0;left:0;opacity:0.7" onmouseover="$(this).css('opacity', 1);" onmouseout="$(this).css('opacity', 0.7);">
	<input type="submit" value="{l s='Save'}" id="saveLiveEdit" class="exclusive" style="float:left">
	<input type="submit" value="{l s='Close Live edit'}" id="closeLiveEdit" class="button" style="float:left">
	<div style="float:right;margin-right:20px;" id="live_edit_feed_back"></div>
</div>
<a href="#" style="display:none;" id="fancy"></a>
<div id="live_edit_feedback" style="width:400px"> 
	<p id="live_edit_feedback_str">
	</p> 
	<!-- <a href="javascript:;" onclick="$.fancybox.close();">{l s='Close'}</a> --> 
</div>	
