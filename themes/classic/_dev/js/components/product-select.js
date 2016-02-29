import $ from 'jquery';
import 'velocity-animate';

export default class ProductSelect {
  init() {
    const MAX_THUMBS = 5;
    var $arrows =   $('.js-arrows');
    var $thumbnails = $('.js-product-images');
    $('.js-thumb').on('click', (event) => {
      if($('.js-thumb').hasClass('selected')){
        $('.js-thumb').removeClass('selected');
      }
      $(event.currentTarget).addClass('selected');
      $('.js-product-cover').attr('src', $(event.target).data('image-large-src'));
    });

    if ($('.js-product-images li').length <= MAX_THUMBS) {
      $arrows.css('opacity', '.2');
    } else {
      $arrows.on('click', (event) => {
        if ($(event.target).hasClass('arrow-up') && $('.js-product-images').position().top < 0) {
          this.move('up');
          $('.js-arrow-down').css('opacity','1');
        } else if ($(event.target).hasClass('arrow-down') && $thumbnails.position().top + $thumbnails.height() >  $('.js-mask').height()) {
          this.move('down');
          $('.js-arrow-up').css('opacity','1');
        }
      });
    }
  }

  move(direction) {
    const THUMB_MARGIN = 10;
    var $thumbnails = $('.js-product-images');
    var thumbHeight = $('.js-product-images li img').height() + THUMB_MARGIN;
    var currentPosition = $thumbnails.position().top;
    $thumbnails.velocity({
      translateY: (direction === 'up') ? currentPosition + thumbHeight : currentPosition - thumbHeight
    },function(){
      if ($thumbnails.position().top >= 0) {
        $('.js-arrow-up').css('opacity','.2');
      } else if ($thumbnails.position().top + $thumbnails.height() <=  $('.js-mask').height()) {
        $('.js-arrow-down').css('opacity','.2');
      }
    });
  }
}
