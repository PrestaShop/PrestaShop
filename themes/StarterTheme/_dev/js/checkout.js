import $ from 'jquery';

$(document).ready(function listenForTermsAndConditionsApprovalChange () {
  function hideSubmitButton () {
    $('#conditions-to-approve input[type="submit"]').hide();
  }

  function refreshPaymentOptions () {
    let params = $('#conditions-to-approve').serialize() + '&action=getPaymentOptions';
    $.post('', params).then(resp => {
      $('#payment-options').replaceWith(resp);
      hideSubmitButton();
    });
  }

  hideSubmitButton();
  $('body').on('change', '#conditions-to-approve input[type="checkbox"]', refreshPaymentOptions);

});
