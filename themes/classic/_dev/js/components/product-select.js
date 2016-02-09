import $ from 'jquery';
import 'velocity-animate';

export default class ProductSelect {

  init() {
    $('.js-thumb').on('click',(event)=>{
      $('.js-product-cover').attr('src', $(event.target).data('image-large-src'));
    });
  }
}
