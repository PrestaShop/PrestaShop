export default class DropDown {
  constructor() {
    $('.js-drop-down').on('click', function(event) {
      event.preventDefault();
      $(this).find('ul').toggleClass('active');
    });
  }
}
