/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

export default class NavBar {
  constructor() {
    $(() => {
      $(".nav-bar").find(".link-levelone").hover(function() {
        $(this).addClass("-hover");
      }, function() {
        $(this).removeClass("-hover");
      });

      $('.nav-bar li.link-levelone.has_submenu > a').on('click', function(e) {
          e.preventDefault();
          e.stopPropagation();
          let $submenu = $(this).parent();

          if (!$submenu.hasClass('open')) {
              $('.nav-bar li.link-levelone.has_submenu a > i.material-icons.sub-tabs-arrow')
                  .text('keyboard_arrow_down');
              $('.nav-bar li.link-levelone.has_submenu.open ul.submenu').slideUp({
                  complete: function() {
                      $(this).parent().removeClass('ul-open open');
                      $(this).removeAttr('style');
                  }
              });
              $submenu.addClass('ul-open');
              $submenu.find('ul.submenu').slideDown({
                  complete: function() {
                      $submenu.addClass('open');
                      $(this).removeAttr('style');
                  }
              });
              $(this).find('i.material-icons.sub-tabs-arrow').text('keyboard_arrow_up');
          } else {
              $submenu.find('ul.submenu').slideUp({
                  complete: function() {
                      $submenu.removeClass('ul-open open');
                      $(this).removeAttr('style');
                  }
              });
              $(this).find('i.material-icons.sub-tabs-arrow').text('keyboard_arrow_down');
          }
      });

      $('.nav-bar').on('click', '.menu-collapse', function() {
        $('body').toggleClass('page-sidebar-closed');

        if ($('body').hasClass('page-sidebar-closed')) {
          $('nav.nav-bar ul.main-menu > li')
              .removeClass('ul-open open')
              .find('a > i.material-icons.sub-tabs-arrow').text('keyboard_arrow_down');
        } else {
            $('nav.nav-bar ul.main-menu > li.-active')
                .addClass('ul-open open')
                .find('a > i.material-icons.sub-tabs-arrow').text('keyboard_arrow_up');
        }

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
        const MAX_MOBILE_WIDTH = 1023;

        if ($(window).width() <= MAX_MOBILE_WIDTH) {
            this.mobileNav(MAX_MOBILE_WIDTH);
        }

        $(window).on('resize', () => {
            if ($('body').hasClass('mobile') && $(window).width() > MAX_MOBILE_WIDTH) {
                this.unbuildMobileMenu();
            } else if (!$('body').hasClass('mobile') && $(window).width() <= MAX_MOBILE_WIDTH) {
                this.mobileNav(MAX_MOBILE_WIDTH);
            }
        });
    });
  }

  mobileNav() {
    let $logout = $('#header_logout').addClass('link').removeClass('m-t-1').prop('outerHTML');
    let $employee = $('.employee_avatar').prop('outerHTML');
    let profileLink = $('.profile-link').attr('href');

    $('.nav-bar li.link-levelone.has_submenu:not(.open) a > i.material-icons.sub-tabs-arrow').text('keyboard_arrow_down');
    $('body').addClass('mobile');
    $('.nav-bar').addClass('mobile-nav').attr('style', 'margin-left: -100%;');
    $('.panel-collapse').addClass('collapse');
    $('.link-levelone a').each((index, el)=> {
      let id = $(el).parent().find('.collapse').attr('id');
      if(id) {
        $(el).attr('href', `#${id}`).attr('data-toggle','collapse');
      }
    });
    $('.main-menu').append(`<li class="link-levelone" data-submenu="">${$logout}</li>`);
    $('.main-menu').prepend(`<li class="link-levelone">${$employee}</li>`);
    $('.collapse').collapse({
      toggle: false
    });
    $('.employee_avatar .material-icons, .employee_avatar span').wrap(`<a href="${profileLink}"></a>`);
    $('.js-mobile-menu').on('click', expand);
    $('.js-notifs_dropdown').css({
      'height' : window.innerHeight
    });

    function expand(e) {
        if ($('div.notification-center.dropdown').hasClass('open')) {
            return;
        }

        if ($('.mobile-nav').hasClass('expanded')) {
            $('.mobile-nav').animate({'margin-left': '-100%'}, {
                complete: function() {
                    $('.nav-bar, .mobile-layer').removeClass('expanded');
                    $('.nav-bar, .mobile-layer').addClass('d-none');
                }
            });
            $('.mobile-layer').off();
        } else {
            $('.nav-bar, .mobile-layer').addClass('expanded');
            $('.nav-bar, .mobile-layer').removeClass('d-none');
            $('.mobile-layer').on('click', expand);
            $('.mobile-nav').animate({'margin-left': 0});
        }
    }
  }

  unbuildMobileMenu() {
    $('body').removeClass('mobile');
    $('body.page-sidebar-closed .nav-bar .link-levelone.open').removeClass('ul-open open');
    $('.main-menu li:first, .main-menu li:last').remove();
    $('.js-notifs_dropdown').removeAttr('style');
    $('.nav-bar').removeClass('mobile-nav expanded').addClass('d-none').css('margin-left', 0);
    $('.js-mobile-menu').off();
    $('.panel-collapse').removeClass('collapse').addClass('submenu');
    $('.shop-list-title').remove();
    $('.js-non-responsive').hide();
    $('.mobile-layer').addClass('d-none').removeClass('expanded');
  }
}
