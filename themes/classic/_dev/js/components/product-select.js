import $ from 'jquery';
import 'velocity-animate';

export default class ProductSelect {
  init() {
    const MAX_THUMBS = 5;
    var $arrows =   $('.js-modal-arrows');
    var $thumbnails = $('.js-modal-product-images');
    $('.js-modal-thumb').on('click', (event) => {
      if($('.js-modal-thumb').hasClass('selected')){
        $('.js-modal-thumb').removeClass('selected');
      }
      $(event.currentTarget).addClass('selected');
      $('.js-modal-product-cover').attr('src', $(event.target).data('image-large-src'));
      $('.js-modal-product-cover').attr('title', $(event.target).attr('title'));
      $('.js-modal-product-cover').attr('alt', $(event.target).attr('alt'));
    });

    if ($('.js-modal-product-images li').length <= MAX_THUMBS) {
      $arrows.css('opacity', '.2');
    } else {
      $arrows.on('click', (event) => {
        if ($(event.target).hasClass('arrow-up') && $thumbnails.position().top < 0) {
          this.move('up');
          $('.js-modal-arrow-down').css('opacity','1');
        } else if ($(event.target).hasClass('arrow-down') && $thumbnails.position().top + $thumbnails.height() >  $('.js-modal-mask').height()) {
          this.move('down');
          $('.js-modal-arrow-up').css('opacity','1');
        }
      });
    }
  }

  move(direction) {
    const THUMB_MARGIN = 10;
    var $thumbnails = $('.js-modal-product-images');
    var thumbHeight = $('.js-modal-product-images li img').height() + THUMB_MARGIN;
    var currentPosition = $thumbnails.position().top;
    $thumbnails.velocity({
      translateY: (direction === 'up') ? currentPosition + thumbHeight : currentPosition - thumbHeight
    },function(){
      if ($thumbnails.position().top >= 0) {
        $('.js-modal-arrow-up').css('opacity','.2');
      } else if ($thumbnails.position().top + $thumbnails.height() <=  $('.js-modal-mask').height()) {
        $('.js-modal-arrow-down').css('opacity','.2');
      }
    });
  }
}
