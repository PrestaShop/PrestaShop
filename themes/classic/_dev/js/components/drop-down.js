/* global $ */

export default class DropDown {
  constructor(el) {
    this.el = el;
  }
  init() {
    this.el.on('show.bs.dropdown', function(e) {
      $(e.target).find('.dropdown-menu').first().stop(true, true).slideDown();
    });

    this.el.on('hide.bs.dropdown', function(e) {
      $(e.target).find('.dropdown-menu').first().stop(true, true).slideUp();
    });
  }
}
