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
import MobileDetect from 'mobile-detect';

export default class NavBar {
  constructor() {
    let md = new MobileDetect(window.navigator.userAgent);
    const MAX_MOBILE_WIDTH = 600;

    $(() => {
      $(".nav-bar").find(".link-levelone").hover(function() {
        $(this).addClass("-hover");
      }, function() {
        $(this).removeClass("-hover");
      });

      $('.nav-bar').on('click', '.menu-collapse', function() {
        $('body').toggleClass('page-sidebar-closed');
        $.ajax({
          url: "index.php",
          cache: false,
          data: {
            token: window.employee_token,
            ajax: 1,
            action: 'toggleMenu',
            tab: 'AdminEmployees',
            collapse: Number($('body').hasClass('page-sidebar-closed'))
          },
        });
      });
      if(md.isPhoneSized(MAX_MOBILE_WIDTH)) {
        this.mobileNav(md, MAX_MOBILE_WIDTH);
        $(window).on('resize', () => {
          if(!md.isPhoneSized(MAX_MOBILE_WIDTH) && $('body').hasClass('mobile')) {
            this.unbuildMobileMenu();
          }
          else if(md.isPhoneSized(MAX_MOBILE_WIDTH) && !$('body').hasClass('mobile')) {
            this.mobileNav(md, MAX_MOBILE_WIDTH);
          }
        });
      }
    });
  }
  mobileNav() {
    let $logout = $('#header_logout').addClass('link').removeClass('m-t-1').prop('outerHTML');
    let $employee = $('.employee_avatar').prop('outerHTML');
    let profileLink = $('.profile-link').attr('href');
    let $shoplist = $('.shop-list');
    let $shopText =  $('.shop-list .items-list .active a').html();

    $shoplist.find('.link').removeClass('link');

    $('body').addClass('mobile');
    $('.nav-bar').addClass('mobile-nav');
    $('.panel-collapse').addClass('collapse').removeClass('submenu');
    $('.link-levelone a').each((index, el)=> {
      let id = $(el).parent().find('.collapse').attr('id');
      if(id) {
        $(el).attr('href', `#${id}`).attr('data-toggle','collapse');
      }
    });
    $('.main-menu').append(`<li class="link-levelone">${$logout}</li>`);
    $('.main-menu').prepend(`<li class="link-levelone">${$employee}</li>`);

    $('.collapse').collapse({
      toggle: false
    });

    if($shoplist.hasClass('ps-dropdown')) {
      $('.main-menu li:first').append(`<p class="shop-list-title"><a href="#shop-list-collapse" data-toggle="collapse" aria-expanded="true">${$shopText}</a></p>`);
      $('.main-menu li:first').append( $('.shop-list .items-list').addClass('collapse').attr('id', 'shop-list-collapse'));
      $('#shop-list-collapse').collapse({
        toggle: true
      });
    }
    else {
      $('.main-menu li:first').append( $('.shop-list'));
    }

    $('.employee_avatar img, .employee_avatar span').wrap(`<a href="${profileLink}"></a>`);

    $('.js-mobile-menu').on('click', expand);

    $('.js-notifs_dropdown').css({
      'height' : window.innerHeight
    });

    $('.js-non-responsive').show();

    function expand (){
      if (!$('div.notification-center.dropdown').hasClass('open')) {
        $('.mobile-nav').toggleClass('expanded');
        if(!$('.mobile-nav').hasClass('expanded')){
          $('.mobile-layer').off();
          $('.mobile-layer').removeClass('expanded');
        }
        else {
          $('.mobile-layer').on('click', expand);
          $('.mobile-layer').addClass('expanded');
        }
      }
    };
  }
  unbuildMobileMenu() {
    $('body').removeClass('mobile');
    $('.main-menu li:first').remove();
    $('.js-notifs_dropdown').removeAttr('style');
    $('.nav-bar').removeClass('mobile-nav');
    $('.panel-collapse').removeClass('collapse').addClass('submenu');
    $('.shop-list-title').remove();
    $('.js-non-responsive').hide();
  }
}
