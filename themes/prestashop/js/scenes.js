/*
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
*/

nb_move_available = null;
current_move = 0;
next_scene_is_at_right = true;

function loadScene(id_scene){
	$('#scenes').find('.screen_scene:visible').fadeTo(300, 0, function(){
		$(this).hide();
		$('#scenes').find('#screen_scene_' + id_scene).css('opacity', '0').show().fadeTo(500, 1);
	});
	return false;
}

function onSceneMove(){
	if (next_scene_is_at_right) current_move++;
	else current_move--;
	if (current_move == nb_move_available - 1)	$('#scenes .next').fadeOut();
	else $('#scenes .next:hidden').fadeIn().css('display','block');
	if (current_move == 0) $('#scenes .prev').fadeOut().css('display','block');
	else $('#scenes .prev').fadeIn().css('display','block');
	return true;
}

$(function () {
	/* calcul nb of click to see every scenes */
	var ul_width = parseInt($('#scenes_list ul').width());
	var div_width = parseInt($('#scenes_list').width());
	nb_move_available = Math.ceil((ul_width-div_width)/li_width)+1;
	if (nb_move_available < 2)
		$('#scenes .next').hide();
	
	/* set cluetip parameters */
	$('a.cluetip')
		.cluetip({
			local:true,
			cursor: 'pointer',
			attribute:'accesskey',
			cluetipClass: 'product_scene',
			dropShadow: false,
			dropShadowSteps: 0,
			showTitle: false,
			tracking: true,
			sticky: false,
			mouseOutClose: true,
			closeText: i18n_scene_close,
			fx: {             
		    open:       'fadeIn',
		    openSpeed:  'fast'
		  }
		})
		.css('opacity', 0.8);
	
	/* set serialscroll parameters */
	$('#scenes_list').serialScroll({
		items:'a',
		duration:1000,
		lock:false,
		axis:'x',
		cycle:false,
		force:true,
		lazy:true,
		step:1,
		onBefore:onSceneMove
	});
	$('#scenes_list').trigger( 'goto', 0);
	
});
