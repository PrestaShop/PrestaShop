export default class DropDown {
  constructor(el) {
    this.el = el;
  }
  init(el) {
    this.el.on('show.bs.dropdown', function(e) {
      $(this).find('.dropdown-menu').first().stop(true, true).slideDown().show();
    });

    this.el.on('hide.bs.dropdown', function(e) {
      $(this).find('.dropdown-menu').first().stop(true, true).slideUp();
    });
  }
}
