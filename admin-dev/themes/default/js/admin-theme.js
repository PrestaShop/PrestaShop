/*
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

$( document ).ready(function() {

	//nav side bar
	function navSidebar(){
		$('body.page-topbar').removeClass('page-topbar').addClass('page-sidebar');
		$('#nav-topbar').attr('id','nav-sidebar');
		$('#nav-sidebar').off();
		$('.expanded').removeClass('expanded');
		$('.maintab').not('.active').closest('.submenu').hide();
		$('#nav-sidebar li.maintab.has_submenu').append('<span class="submenu_expand"></span>');
		$('#nav-sidebar .submenu_expand').on('click',function(){
			var $navId = $(this).parent();
			$('.submenu-collapse').remove();
			if($('.expanded').length ){
				$('.expanded > ul').slideUp(
					'fast',
					function(){
						var $target = $('.expanded');
						$target.removeClass('expanded');
						$($navId).not($target).not('.active').addClass('expanded');
						$($navId).not($target).not('.active').children('ul:first').hide().slideDown();
					}
				);
			}
			else {
				$($navId).not('.active').addClass('expanded');
				$($navId).not('.active').children('ul:first').hide().slideDown();
			}
		});
	}
	//nav top bar
	function navTopbar(){
		$('body').removeClass('page-sidebar').removeClass('page-sidebar-closed').addClass('page-topbar');
		$('#nav-sidebar').attr('id','nav-topbar');
		$('#nav-topbar').off();
		$('span.submenu_expand').remove();
		$('.expanded').removeClass('expanded');

		$('#nav-topbar').on('mouseenter', 'li.maintab.has_submenu',
			function(){
				$(this).addClass('expanded');
			});
		$('#nav-topbar').on('mouseleave', 'li.maintab.has_submenu',
			function(){
				$(this).removeClass('expanded');
		});

		//todo hide element over menu width on load
		$('#nav-topbar').find('li.maintab').each(
			function(){
			if ($(this).position().top > 0) {
				$(this).addClass('hide');
			}
		});

		//todo hide element over menu width on resize
		$(window).on('resize', function() {
			$('#nav-topbar ul.menu').find('li.maintab').each(function(){
				if ($(this).position().top > 0) {
					$(this).addClass('hide');
				}
			});
		});
	}

	//nav switch
	function navSwitch(){
		if ($('body').hasClass('page-sidebar')||$('body').hasClass('page-sidebar-closed')){
			navTopbar();
		} else {
			navSidebar();
		}
	}

	//init menu
	if ($('body').hasClass('page-sidebar')){
		navSidebar();
	} else if ($('body').hasClass('page-topbar')) {
		navTopbar();
	}

	//tooltip
	$('.label-tooltip').tooltip();

	//sidebar menu collapse
	$('.menu-collapse').click(function(){
		$('body').toggleClass('page-sidebar-closed');
		$('.expanded').removeClass('expanded');
	});

	function animateGoTop() {
		if ($(window).scrollTop())
		{
			$('#go-top:hidden').stop(true, true).fadeIn();
			$('#go-top:hidden').removeClass('hide');
		} else {
			$('#go-top').stop(true, true).fadeOut();
		}
	}

	$("#go-top").click(function() {
		$("html, body").animate({ scrollTop: 0 }, "slow");
		return false;
	});
	$(window).scroll(function() {
		animateGoTop();
	});
});

function openModulesList() {
	if (!modules_list_loaded)
	{
		$.ajax({
			type: "POST",
			url : admin_modules_link,
			async: true,
			data : {
				ajax : "1",
				controller : "AdminModules",
				action : "getTabModulesList",
				tab_modules_list : tab_modules_list,
				back_tab_modules_list : window.location.href
			},
			success : function(data)
			{
				$('#modules_list_container_tab').html(data).slideDown();
				$('#modules_list_loader').hide();
				modules_list_loaded = true;
			}
		});
	}
	else
	{
		$('#modules_list_container_tab').slideDown();
		$('#modules_list_loader').hide();
	}
	return false;
}
