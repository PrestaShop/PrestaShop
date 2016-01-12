export default class DropDown {
  constructor(el) {
    this.el = el;
  }
  init(el) {
    this.el.on('click', function(event) {
      let currentMenu = $(this);

      if ($(event.target).data('depth') != undefined && ($(event.target).data('depth') !== 0 || $(this).find('ul').length === 0)) {
        return true;
      }
      event.preventDefault();
      event.stopPropagation();

      currentMenu.find('ul').toggleClass('active');

      $('html').one('click', function() {
        currentMenu.find('ul').toggleClass('active');
      });
    });
  }
}
