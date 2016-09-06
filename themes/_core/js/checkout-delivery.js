import $ from 'jquery'
import prestashop from 'prestashop'

export default function () {
  let $body = $('body');
  let deliveryFormSelector = '#js-delivery';
  let summarySelector = '#js-checkout-summary';
  let deliveryStepSelector = '#checkout-delivery-step';
  let editDeliveryButtonSelector = '.js-edit-delivery';

  let updateDeliveryForm = () => {
    let $deliveryMethodForm = $(deliveryFormSelector);
    let requestData = $deliveryMethodForm.serialize();

    $.post($deliveryMethodForm.data('url-update'), requestData).then((resp) => {
      $(summarySelector).replaceWith(resp.preview);
      prestashop.emit('updatedDeliveryForm');
    }).fail((resp) => {
      prestashop.trigger('handleError', {eventType: 'updateDeliveryOptions', resp: resp})
    });
  };

  $body.on('change', deliveryFormSelector + ' input', updateDeliveryForm);

  $body.on('click', editDeliveryButtonSelector, (event) => {
    event.stopPropagation();
    $(deliveryStepSelector).trigger('click');
    prestashop.emit('editDelivery');
  });
}
