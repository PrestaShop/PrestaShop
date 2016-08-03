import $ from 'jquery';
import DropDown from './drop-down';

export default class TopMenu extends DropDown {
  init() {
    let elmId;
    this.el.find('li').hover((e) => {
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
    $('.js-top-menu').mouseleave(() => {
      $(`#${elmId} .js-sub-menu`).hide();
    });
    this.el.on('click', (e) => {
      e.stopPropagation();
    });
    super.init();
  }
}
