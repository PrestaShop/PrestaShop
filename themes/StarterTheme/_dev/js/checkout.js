import $ from 'jquery';
import prestashop from 'prestashop';

import {psShowHide} from './common';

function collapsePaymentOptions() {
  $('.js-additional-information, .js-payment-option-form').hide();
}

function getSelectedPaymentOption () {
  return $('#payment-options input[name="advanced-payment-option"]:checked').attr('id');
}

function enableOrDisableOrderButton() {
  var show = true;
  $('#conditions-to-approve input[type="checkbox"]').each((_, checkbox) => {
    if (!checkbox.checked) {
      show = false;
    }
  });

  collapsePaymentOptions();

  var option = getSelectedPaymentOption();
  if (!option) {
    show = false;
  }

  $('#' + option + '-additional-information').show();
  $('#pay-with-' + option + '-form').show();
  $('#payment-confirmation button').attr('disabled', !show);
}

function confirmPayment () {
  var option = getSelectedPaymentOption();
  if (option) {
    $('#pay-with-' + option + '-form form').submit();
  }
}

function refreshDeliveryOptions () {
  let params = $('#delivery-method').serialize() + '&action=selectDeliveryOption';
  $.post('', params).then(resp => {
    $('#delivery-options').replaceWith(resp);
    psShowHide();
  });
}

function hideOrShow () {
  var elm = this.getAttribute('data-action-target');
  var show = this.checked;

  if (show) {
    $('body #'+elm).show();
  } else {
    $('body #'+elm).hide();
  }
}

function displayAddressEditForm (event) {
  event.preventDefault();
  var addressId = this.getAttribute('data-entity-id');

  $.ajax({
    url: prestashop.urls.pages.address,
    method: "POST",
    data: {
      ajax : true,
      id_address : addressId,
      action : 'getAddressEditForm'
    },
    dataType: "html"
  })
    .done( html => $(this).closest('article').html(html) );
}

function setupCheckoutScripts () {
  if (!$('body#order')) {
    return;
  }

  $('#payment-confirmation button').on('click', confirmPayment);
  $('#payment-options input[type="checkbox"][disabled]').attr('disabled', false);
  $('body').on('change', '#delivery-method input[type="radio"]', refreshDeliveryOptions);
  $('body').on('change', '#conditions-to-approve input[type="checkbox"]', enableOrDisableOrderButton);
  $('body').on('change', 'input[name="advanced-payment-option"]', enableOrDisableOrderButton);
  $('body').on('change', 'input[type="checkbox"][data-action="hideOrShow"]', hideOrShow);
  $('body').on('click', 'a[data-link-action="edit-address"]', displayAddressEditForm);
  $('body').on('click', 'button#submitAddress', displayAddressEditForm);

  collapsePaymentOptions();

  prestashop.on('cart updated', function () {
    $.get('', {
      action: 'getCartSummary'
    }).then(resp => {
      $('#cart-summary').html(resp);
    });
  });
}

$(document).ready(setupCheckoutScripts);
