/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
import $ from 'jquery';
import DropDown from './drop-down';

export default class TopMenu extends DropDown {
  init() {
    let elmtClass;
    let self = this;
    this.el.find('li').hover((e) => {
      if (this.el.parent().hasClass('mobile')) {
        return;
      }
      const currentTargetClass = $(e.currentTarget).attr('class');
      if (elmtClass !== currentTargetClass) {
        elmtClass = currentTargetClass;
        
        if (elmtClass && $(e.target).data('depth') === 0) {
          $(`.${elmtClass} .js-sub-menu`).css({
            top: $(`.${elmtClass}`).height() + $(`.${elmtClass}`).position().top
          });
        }
      }
    });
    $('#menu-icon').on('click', function() {
      $('#mobile_top_menu_wrapper').toggle();
      self.toggleMobileMenu();
    });
    $('.js-top-menu .category').mouseleave(() => {
      if (this.el.parent().hasClass('mobile')) {
        return;
      }
    });
    this.el.on('click', (e) => {
      if (this.el.parent().hasClass('mobile')) {
        return;
      }
      e.stopPropagation();
    });
    prestashop.on('responsive update', function(event) {
      $('.js-sub-menu').removeAttr('style');
      self.toggleMobileMenu();
    });
    super.init();
  }

  toggleMobileMenu() {
    $('#header').toggleClass('is-open');
    if ($('#mobile_top_menu_wrapper').is(":visible")) {
      $('#notifications, #wrapper, #footer').hide();
    } else {
      $('#notifications, #wrapper, #footer').show();
    }
  }
}
