import $ from 'jquery';

export default class DropDown {
  constructor(el) {
    this.el = el;
  }
  init() {
    this.el.on('show.bs.dropdown', function(e, el) {
      if (el) {
        $(`#${el}`).find('.dropdown-menu').first().stop(true, true).slideDown();
      } else {
        $(e.target).find('.dropdown-menu').first().stop(true, true).slideDown();
      }
    });

    this.el.on('hide.bs.dropdown', function(e, el) {
      if (el) {
        $(`#${el}`).find('.dropdown-menu').first().stop(true, true).slideUp();
      } else {
        $(e.target).find('.dropdown-menu').first().stop(true, true).slideUp();
      }
    });
  }
}
