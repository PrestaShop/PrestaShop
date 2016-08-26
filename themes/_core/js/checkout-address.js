import $ from 'jquery'
import prestashop from 'prestashop'

export default function () {
  $('.js-edit-addresses').on('click', (event) => {
    event.stopPropagation();
    $('#checkout-addresses-step').trigger('click');
    prestashop.emit('editAddress');
  });
}
