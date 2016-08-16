import $ from 'jquery';
import DropDown from './drop-down';

export default class TopMenu extends DropDown {
  init() {
    let elmId;
    let self = this;
    this.el.find('li').hover((e) => {
      if (this.el.parent().hasClass('mobile')) {
        return;
      }
      if (elmId !== $(e.currentTarget).attr('id')) {
        if ($(e.target).data('depth') === 0) {
          $(`#${elmId} .js-sub-menu`).hide();
        }
        elmId = $(e.currentTarget).attr('id');
      }
      if (elmId && $(e.target).data('depth') === 0) {
        $(`#${elmId} .js-sub-menu`).show().css({
          top: $(`#${elmId}`).height() + $(`#${elmId}`).position().top
        });
      }
    });
    $('#menu-icon').on('click', function() {
      $('#mobile_top_menu_wrapper').toggle();
      self.toggleMobileMenu();
    });
    $('.js-top-menu').mouseleave(() => {
      if (this.el.parent().hasClass('mobile')) {
        return;
      }
      $(`#${elmId} .js-sub-menu`).hide();
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
      if ($('#mobile_top_menu_wrapper').is(":visible")) {
        $('#notifications').hide();
        $('#wrapper').hide();
        $('#footer').hide();
      } else {
        $('#notifications').show();
        $('#wrapper').show();
        $('#footer').show();
      }
  }
}
