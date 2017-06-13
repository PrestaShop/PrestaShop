/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

$(document).ready(function()
{
	$('#mainForm').submit(function() {
		$('#btNext').hide();
	});

	// Ajax animation
	$("#loaderSpace").ajaxStart(function()
	{
		$(this).fadeIn('slow');
		$(this).children('div').fadeIn('slow');
	});

	$("#loaderSpace").ajaxComplete(function(e, xhr, settings)
	{
		$(this).fadeOut('slow');
		$(this).children('div').fadeOut('slow');
	});

	$('select.chosen').not('.no-chosen').chosen();

	// try to pre-compile the smarty templates
	function compile_smarty_templates(bo)
	{
		$.ajax(
		{
			url: 'index.php',
			data: {
				'compile_templates': 1,
				'bo':bo
			},
			global: false
		});
	}
	compile_smarty_templates(1);
	compile_smarty_templates(0);
});

function psinstall_twitter_click(message) {
	window.open('https://twitter.com/intent/tweet?button_hashtag=PrestaShop&text=' + message, 'sharertwt', 'toolbar=0,status=0,width=640,height=445');
}

function psinstall_facebook_click() {
	window.open('http://www.facebook.com/sharer.php?u=http://www.prestashop.com/', 'sharerfacebook', 'toolbar=0,status=0,width=660,height=445');
}

function psinstall_google_click() {
	window.open('https://plus.google.com/share?url=http://www.prestashop.com/', 'sharergplus', 'toolbar=0,status=0,width=660,height=445');
}

function psinstall_pinterest_click() {
	window.open('http://www.pinterest.com/pin/create/button/?media=http://img-cdn.prestashop.com/logo.png&url=http://www.prestashop.com/', 'sharerpinterest', 'toolbar=0,status=0,width=660,height=445');
}

function psinstall_linkedin_click() {
	window.open('https://www.linkedin.com/shareArticle?title=PrestaShop&url=http://www.prestashop.com/download', 'sharerlinkedin', 'toolbar=0,status=0,width=600,height=450');
}
