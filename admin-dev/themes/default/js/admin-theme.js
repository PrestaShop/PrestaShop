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
		//$('body.page-topbar').removeClass('page-topbar').addClass('page-sidebar');
		//$('#nav-topbar').attr('id','nav-sidebar');
		var sidebar = $('#nav-sidebar');
		sidebar.off();
		$('.expanded').removeClass('expanded');
		$('.maintab').not('.active').closest('.submenu').hide();
		sidebar.find('li.maintab.has_submenu').append('<span class="submenu_expand"></span>');
		sidebar.on('click','.submenu_expand', function(){
			var $navId = $(this).parent();
			$('.submenu-collapse').remove();
			if($('.expanded').length ){
				$('.expanded > ul').slideUp('fast', function(){
					var $target = $('.expanded');
					$target.removeClass('expanded');
					$($navId).not($target).not('.active').addClass('expanded');
					$($navId).not($target).not('.active').children('ul:first').hide().slideDown();
				});
			}
			else {
				$($navId).not('.active').addClass('expanded');
				$($navId).not('.active').children('ul:first').hide().slideDown();
			}
		});
		//sidebar menu collapse
		sidebar.find('.menu-collapse').on('click',function(){
			$('body').toggleClass('page-sidebar-closed');
			$('.expanded').removeClass('expanded');
		});
	}
	//nav top bar
	function navTopbar(){
		//$('body').removeClass('page-sidebar').addClass('page-topbar').removeClass('page-sidebar-closed');
		$('#nav-sidebar').attr('id','nav-topbar');
		var topbar = $('#nav-topbar');
		topbar.off();
		$('span.submenu_expand').remove();
		$('.expanded').removeClass('expanded');
		// expand elements with submenu
		topbar.on('mouseenter', 'li.has_submenu', function(){
			$(this).addClass('expanded');
		});
		topbar.on('mouseleave', 'li.has_submenu', function(){
			$(this).removeClass('expanded');
		});
		// hide element over menu width on load
		topbar.find('li.maintab').each(function(){
			navEllipsis();
		});
		//hide element over menu width on resize
		$(window).on('resize', function() {
			navEllipsis();
		});
	}

	function navEllipsis() {
		var ellipsed = [];
		$('#ellipsistab').remove();
		$('#nav-topbar ul.menu').find('li.maintab').each(function(){
			$(this).removeClass('hide');
			if ($(this).position().top > 0) {
				ellipsed.push($(this));
				$(this).addClass('hide');
			}
		});
		if (ellipsed.length > 0) {
			$('#nav-topbar ul.menu').append('<li id="ellipsistab" class="subtab has_submenu"><a href="#"><i class="icon-ellipsis-horizontal"></i></a><ul id="ellipsis_submenu" class="submenu"></ul></li>');
			for (var i = 0; i < ellipsed.length; i++) {
				$('#ellipsis_submenu').append('<li class="subtab has_submenu">'+ellipsed[i].html()+'</li>');
			}
		}
	}

	function mobileNav() {
		//clean actual menu type
		// get it in navigation whatever type it is.
		var navigation = $('#nav-sidebar,#nav-topbar');
		var submenu = "";
		// clean trigger
		navigation.off().attr('id','nav-mobile');
		$('span.menu-collapse').off();
		navigation.on('click.collapse','span.menu-collapse',function(){
			if ($(this).hasClass('expanded')){
				$(this).html('<i class="icon-align-justify"></i>');
				navigation.find('ul.menu').hide();
				navigation.removeClass('expanded');
				$(this).removeClass('expanded');
				//remove submenu when closing nav
				$('#nav-mobile-submenu').remove();
			}
			else {
				$(this).html('<i class="icon-remove"></i>');
				navigation.find('ul.menu').removeClass('menu-close').show();
				navigation.addClass('expanded');
				$(this).addClass('expanded');
			}
		});
		//get click for item which has submenu
		navigation.on('click.submenu','.maintab.has_submenu a.title', function(e){
			e.preventDefault();
			navigation.find('.menu').addClass('menu-close');
			$('#nav-mobile-submenu').remove();
			//create submenu
			submenu = $('<ul id="nav-mobile-submenu" class="menu"><li><a href="#" id="nav-mobile-submenu-back"><i class="icon-arrow-left"></i>'+ $(this).html() +'</a></li></ul>');
			submenu.append($(this).closest('.maintab').find('.submenu').html());
			//show submenu
			navigation.append(submenu);
			submenu.show();
		});
		navigation.on('click.back','#nav-mobile-submenu-back',function(e){
			e.preventDefault();
			submenu.remove();
			navigation.find('.menu').removeClass('menu-close').show();
		});
	}

	function removeMobileNav(){
		navigation = $('#nav-mobile');
		$('#nav-mobile-submenu').remove();
		$('span.menu-collapse').html('<i class="icon-align-justify"></i>');
		navigation.off();
		if ($('body').hasClass('page-sidebar')){
			navigation.attr('id',"nav-sidebar");
			navSidebar();
		} else if ($('body').hasClass('page-sidebar')){
			navigation.attr('id',"nav-topbar");
			navTopbar();
		}
	}

	//nav switch - not used for now
	function navSwitch(){
		if ($('body').hasClass('page-sidebar')){
			navTopbar();
		} else {
			navSidebar();
		}
	}

	//init menu
	function initNav(){
		if ($('body').hasClass('page-sidebar')){
			navSidebar();
		}
		else if ($('body').hasClass('page-topbar')) {
			navTopbar();
		}
	}

	initNav();
	//tooltip
	$('.label-tooltip').tooltip();

	//scroll top
	function animateGoTop() {
		if ($(window).scrollTop())
		{
			$('#go-top:hidden').stop(true, true).fadeIn();
			$('#go-top:hidden').removeClass('hide');
		} else {
			$('#go-top').stop(true, true).fadeOut();
		}
	}

	$("#go-top").on('click',function() {
		$("html, body").animate({ scrollTop: 0 }, "slow");
		return false;
	});

	$(window).scroll(function() {
		animateGoTop();
	});

	//media queries - depends of enquire.js
	enquire.register("screen and (max-width: 992px)", {
		match : function() {
			$('body.page-sidebar').addClass('page-sidebar-closed');
		},
		unmatch : function() {
			$('body.page-sidebar').removeClass('page-sidebar-closed');
		}
	});
	enquire.register("screen and (max-width: 480px)", {
		match : function() {
			mobileNav();
		},
		unmatch : function() {
			removeMobileNav();
		}
	});
});