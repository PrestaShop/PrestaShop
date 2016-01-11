export default class TopMenu {
  constructor() {
    $('.js-top-menu > li').on('click', function(event) {
      if ($(event.target).data('depth') === 0 && $(this).find('ul').length) {
        event.preventDefault();
        $(this).find('ul').toggleClass('active');
      }
    });
  }
}
