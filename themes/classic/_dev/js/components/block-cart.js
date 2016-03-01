import prestashop from 'prestashop';
import $ from 'jquery';

prestashop.blockcart = prestashop.blockcart || {};
prestashop.blockcart.showModal = html => {
  $('body').append(html);
  $('#blockcart-modal').modal('show');
};
