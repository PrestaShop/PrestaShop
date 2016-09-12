import $ from 'jquery';

export default function() {
  let tabWidth = 0;
  let navWidth = 50;

  $(window).on('resize', () => {
    init();
  });

  $('.js-nav-tabs li').each((index, item) => {
    navWidth += $(item).width();
    $('.js-nav-tabs').width(navWidth);
  });

  $('.js-arrow').on('click', (e) => {
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

  $('.js-btn-save').on('click', () =>{
    $('.js-spinner').show();
    $( document ).ajaxComplete(()=> {
      $('.js-spinner').hide();
    });
  });

  var init = () => {
    if($('.js-nav-tabs').width() < $('.js-tabs').width()) {
      $('.js-nav-tabs').width($('.js-tabs').width());
      return $('.js-arrow').hide();
    }
    else {
      $('.js-arrow').show();
    }
  };

  init();
}
