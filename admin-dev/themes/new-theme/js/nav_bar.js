/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

import PerfectScrollbar from 'perfect-scrollbar';
import '@node_modules/perfect-scrollbar/css/perfect-scrollbar.css';
import getAnimationEvent from './app/utils/animations';
import NavbarTransitionHandler from './components/navbar-transition-handler';

const {$} = window;

export default class NavBar {
  constructor() {
    $(() => {
      const $mainMenu = $('.main-menu');
      const $navBar = $('.nav-bar');
      const $body = $('body');
      new PerfectScrollbar($navBar.get(0));
      const NavBarTransitions = new NavbarTransitionHandler(
        $navBar,
        $mainMenu,
        getAnimationEvent('transition', 'end'),
        $body,
      );

      $navBar.find('.link-levelone').hover(
        function onMouseEnter() {
          $(this).addClass('link-hover');
        },
        function onMouseLeave() {
          $(this).removeClass('link-hover');
        },
      );

      $('.nav-bar li.link-levelone.has-submenu > a').on(
        'click',
        function onNavBarClick(e) {
          e.preventDefault();
          e.stopPropagation();
          const $submenu = $(this).parent();
          $('.nav-bar li.link-levelone.has-submenu a > i.material-icons.sub-tabs-arrow')
            .text('keyboard_arrow_down');
          const onlyClose = $(e.currentTarget).parent().hasClass('ul-open');

          if ($('body').is('.page-sidebar-closed:not(.mobile)')) {
            $('.nav-bar li.link-levelone.has-submenu.ul-open').removeClass('ul-open open submenu-hover');
            $('.nav-bar li.link-levelone.has-submenu.ul-open ul.submenu').removeAttr('style');
          } else {
            $('.nav-bar li.link-levelone.has-submenu.ul-open ul.submenu').slideUp({
              complete: function slideUpIsComplete() {
                $(this).parent().removeClass('ul-open open');
                $(this).removeAttr('style');
              },
            });
          }

          if (onlyClose) {
            return;
          }

          $submenu.addClass('ul-open');

          if ($('body').is('.page-sidebar-closed:not(.mobile)')) {
            $submenu.addClass('submenu-hover');
            $submenu.find('ul.submenu').removeAttr('style');
          } else {
            $submenu.find('ul.submenu').slideDown({
              complete: function slideDownIsComplete() {
                $submenu.addClass('open');
                $(this).removeAttr('style');
              },
            });
          }
          $submenu.find('i.material-icons.sub-tabs-arrow').text('keyboard_arrow_up');
        });

      $navBar.on(
        'click',
        '.menu-collapse',
        function onNavBarClick() {
          $('body').toggleClass('page-sidebar-closed');

          NavBarTransitions.toggle();

          $('.popover.show').remove();
          $('.help-box[aria-describedby]').removeAttr('aria-describedby');

          if ($('body').hasClass('page-sidebar-closed')) {
            $('nav.nav-bar ul.main-menu > li')
              .removeClass('ul-open open')
              .find('a > i.material-icons.sub-tabs-arrow').text('keyboard_arrow_down');
            addMobileBodyClickListener();
          } else {
            $('nav.nav-bar ul.main-menu > li.link-active')
              .addClass('ul-open open')
              .find('a > i.material-icons.sub-tabs-arrow').text('keyboard_arrow_up');
            $('body').off('click.mobile');
          }

          $.post({
            url: $(this).data('toggle-url'),
            cache: false,
            data: {
              shouldCollapse: Number($('body').hasClass('page-sidebar-closed')),
            },
          });
        },
      );

      addMobileBodyClickListener();
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

      function addMobileBodyClickListener() {
        if (!$('body').is('.page-sidebar-closed:not(.mobile)')) {
          return;
        }
        // To close submenu on mobile devices
        $('body').on(
          'click.mobile',
          () => {
            if ($('ul.main-menu li.ul-open').length > 0) {
              $('.nav-bar li.link-levelone.has-submenu.ul-open').removeClass('ul-open open submenu-hover');
              $('.nav-bar li.link-levelone.has-submenu.ul-open ul.submenu').removeAttr('style');
            }
          },
        );
      }
    });
  }

  mobileNav() {
    const $logout = $('#header_logout').addClass('link').removeClass('m-t-1').prop('outerHTML');
    const $employee = $('.employee-avatar').prop('outerHTML');
    const profileLink = $('.profile-link').attr('href');
    const $mainMenu = $('.main-menu');
 
    $('.nav-bar li.link-levelone.has-submenu:not(.open) a > i.material-icons.sub-tabs-arrow').text('keyboard_arrow_down');
    $('body').addClass('mobile');
    $('.nav-bar').addClass('mobile-nav').attr('style', 'margin-left: -100%;');
    $('.panel-collapse').addClass('collapse');
    $('.link-levelone a').each((index, el) => {
      const id = $(el).parent().find('.collapse').attr('id');
      if (id) {
        $(el).attr('href', `#${id}`).attr('data-toggle', 'collapse');
      }
    });

    $mainMenu.append(`<li class='link-levelone' data-submenu=''>${$logout}</li>`);
    $mainMenu.prepend(`<li class='link-levelone'>${$employee}</li>`);

    $('.collapse').collapse({
      toggle: false,
    });

    $mainMenu.find('.employee-avatar .material-icons, .employee-avatar span').wrap(`<a href='${profileLink}'></a>`);
    $('.js-mobile-menu').on('click', expand);
    $('.js-notifs_dropdown').css({
      height: window.innerHeight,
    });

    function expand() {
      if ($('div.notification-center.dropdown').hasClass('open')) {
        return;
      }

      if ($('.mobile-nav').hasClass('expanded')) {
        $('.mobile-nav').animate(
          {'margin-left': '-100%'},
          {
            complete: () => {
              $('.nav-bar, .mobile-layer').removeClass('expanded');
              $('.nav-bar, .mobile-layer').addClass('d-none');
            },
          },
        );
        $('.mobile-layer').off();
        return;
      }

      $('.nav-bar, .mobile-layer').addClass('expanded');
      $('.nav-bar, .mobile-layer').removeClass('d-none');
      $('.mobile-layer').on('click', expand);
      $('.mobile-nav').animate({'margin-left': 0});
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
