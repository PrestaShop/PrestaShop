import $ from 'jquery';

export default class ProductMinitature {
  init(){
    $('.js-product-miniature').each((index, element) => {
      const FLAG_MARGIN = 10;
      let $percent = $(element).find('.discount-percentage');
      let $onsale =  $(element).find('.on-sale');
      let $new = $(element).find('.new');
      if($percent.length){
        $new.css('top', $percent.height() * 2 + FLAG_MARGIN);
        $percent.css('top',-$('.thumbnail-container').height() + $('.product-description').height() + FLAG_MARGIN);
      }
      if($onsale.length){
        $percent.css('top', parseFloat($percent.css('top')) + $onsale.height() + FLAG_MARGIN);
        $new.css('top', ($percent.height() * 2 + $onsale.height()) + FLAG_MARGIN * 2);
      }
      if($(element).find('.color').length > 5){
        let count = 0;
        $(element).find('.color').each((index, element) =>{
          if(index > 4){
            $(element).hide();
            count ++;
          }
        });
        $(element).find('.js-count').append(`+${count}`);
      }
    });
  }
}
