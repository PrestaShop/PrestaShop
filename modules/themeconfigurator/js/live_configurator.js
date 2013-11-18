/*
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
*/

$(document).ready(
	function ()
	{
		$('#color-box').find('li').click(
			function()
			{
				$('#theme').val($(this).attr('class'));
			}
		);

		$('#gear-right').click(
			function()
			{
				if ($(this).css('left') == '215px')
				{
					$('#tool_customization').animate({left : '-215px'}, 500);
					$(this).animate({left : '0px'}, 500);
				}
				else
				{
					$('#tool_customization').animate({left : '0px'}, 500);
					$(this).animate({left : '215px'}, 500);
				}
			}
		);

		$('#font-title').click(
			function()
			{
				if ($(this).children('i').hasClass('icon-caret-down'))
				{
					$(this).children('i').removeClass('icon-caret-down').addClass('icon-caret-up');
					$('#font-box').slideUp();
				}
				else
				{
					$(this).children('i').removeClass('icon-caret-up').addClass('icon-caret-down');
					$('#font-box').slideDown();
				}
			}
		);

		$('#theme-title').click(
			function()
			{
				if ($(this).children('i').hasClass('icon-caret-down'))
				{
					$(this).children('i').removeClass('icon-caret-down').addClass('icon-caret-up');
					$('#color-box').slideUp();
				}
				else
				{
					$(this).children('i').removeClass('icon-caret-up').addClass('icon-caret-down');
					$('#color-box').slideDown();
				}
			}
		);
	}
);