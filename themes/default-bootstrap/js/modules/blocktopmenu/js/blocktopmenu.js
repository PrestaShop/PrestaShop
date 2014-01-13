/*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
var responsiveflagMenu = false;

$(document).ready(function(){
	categoryMenu = $('ul.sf-menu');        //var rich menu
	categoryMenu.superfish();				   //menu initialization
	$('.sf-menu > li > ul').addClass('submenu-container'); //add class for width define
	i = 0;
	$('.sf-menu > li > ul > li:not(#category-thumbnail)').each(function(){  //add classes for clearing
		i++;
		if(i%2 == 1)
			$(this).addClass('first-in-line-xs');
		else if (i%5 == 1)
			$(this).addClass('first-in-line-lg');
	});
	responsiveMenu();
	$(window).resize(responsiveMenu);
});

// accordion for definition smaller that 767px
function menuChange(status)
{
	if(status == 'enable')
	{
		$('.sf-menu > li > ul').removeAttr('style');
		$('.sf-menu').removeAttr('style');
		$('.sf-contener .cat-title').on('click', function(){
			$(this).toggleClass('active').parent().find('ul.menu-content').stop().slideToggle('medium');
		}),
		$('.sf-menu > li:has(ul)').each(function(){
			$(this).prepend('<span></span>'),
			$(this).find('span').on('click touchend', function(){
				categoryMenu.superfish('hide');
			});
		});
	}
	else
	{
		$('.sf-contener .cat-title').off();	
		$('.sf-menu').removeAttr('style');
		$('.sf-menu > li > ul').removeAttr('style');
		$('.sf-contener .cat-title').removeClass('active');
	}
}

function responsiveMenu()
{
   if ($(document).width() <= 767 && responsiveflagMenu == false)
	{
		menuChange('enable');
		responsiveflagMenu = true;

	}
	else if ($(document).width() >= 768)
	{
		menuChange('disable');
		responsiveflagMenu = false;
	}
}