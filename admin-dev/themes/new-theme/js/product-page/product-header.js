import $ from 'jquery';

export default function() {
  let tabWidth = 0;
  $('.js-arrow').on('click', (e) => {
    let navWidth = 40;

    $('.js-nav-tabs li').each((index, item) => {
      navWidth += $(item).width();
      $('.js-nav-tabs').width(navWidth);
    });

    tabWidth = navWidth - $('.js-tabs').width();

    if ($('.js-arrow').is(':visible')) {
      $('.js-nav-tabs').animate({
        left: $(e.currentTarget).hasClass('right-arrow') ? `-=${tabWidth}` : 0
      }, 400, 'easeOutQuad', () => {
        if ($(e.currentTarget).hasClass('right-arrow')) {
          $('.left-arrow').addClass('visible');
          $('.right-arrow').removeClass('visible');
        } else {
          $('.right-arrow').addClass('visible');
          $('.left-arrow').removeClass('visible');
        }
      });
    }
  });
}
