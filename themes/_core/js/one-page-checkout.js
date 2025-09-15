/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
import $ from 'jquery';

function isValidEmail(email) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function checkCustomerFormIsValid() {
  if (!isValidEmail($('#customer-form #field-email').val())) {
    console.log('email is invalid');
    return false;
  }
  if ($('#customer-form #field-password').val() === '') {
    console.log('password is empty');
    return false;
  }
  if ($('#customer-form #field-firstname').val() === '') {
    console.log('firstname is empty');
    return false;
  }
  if ($('#customer-form #field-lastname').val() === '') {
    console.log('lastname is empty');
    return false;
  }
  if (!$('#customer-form [name="customer_privacy"]').is(':checked')) {
    console.log('customer privacy not checked');
    return false;
  }
  console.log('all good');
  return true;
}

$(document).ready(() => {
  let lastSentEmail = '';
  let loginEmailReady = false;
  let loginPasswordReady = false;
  let debounceTimeout;

  $('#guest-form #field-email').on('input', function () {
    const input = $(this);
    clearTimeout(debounceTimeout);

    debounceTimeout = setTimeout(() => {
      const email = input.val().trim();

      const opcIsValidEmail = isValidEmail(email);

      if (opcIsValidEmail && email !== lastSentEmail) {
        lastSentEmail = email;

        prestashop.emit('opc.GuestEmailEntered', {
          detail: {
            email,
            timestamp: new Date(),
          },
        });
        console.log('Event "emailEntered" throw with :', email);
      }
    }, 5000);
  });

  $('#login-form input#field-email').on('input', function () {
    clearTimeout(debounceTimeout);

    debounceTimeout = setTimeout(() => {
      const email = $(this).val();

      const opcIsValidEmail = isValidEmail(email);

      if (opcIsValidEmail) {
        loginEmailReady = email;
        prestashop.emit('opc.LoginEmailReady', {
          detail: {
            email,
            timestamp: new Date(),
          },
        });
        console.log('Event "LoginEmailReady" throw with :', email);
        if (loginPasswordReady) {
          prestashop.emit('opc.LoginReady', {
            detail: {
              email,
              timestamp: new Date(),
            },
          });
          console.log('Event "LoginReady" throw with :', email);
        }
      }
    }, 4000);
  });

  $('#login-form input#field-password').on('input', () => {
    clearTimeout(debounceTimeout);

    debounceTimeout = setTimeout(() => {
      loginPasswordReady = true;
      prestashop.emit('opc.LoginPasswordReady', {
        detail: {
          timestamp: new Date(),
        },
      });
      console.log('Event "LoginPasswordReady"');
      if (loginEmailReady) {
        prestashop.emit('opc.LoginReady', {
          detail: {
            loginEmailReady,
            timestamp: new Date(),
          },
        });
        console.log('Event "LoginReady" send with :', loginEmailReady);
      }
    }, 5000);
  });

  $('#customer-form input').on('input', () => {
    clearTimeout(debounceTimeout);

    debounceTimeout = setTimeout(() => {
      if (checkCustomerFormIsValid()) {
        prestashop.emit('opc.CustomerFormReady', {detail: {timestamp: new Date()}});
      } else {
        console.log('form NOT ready');
      }
    }, 5000);
  });
});

prestashop.on('PersonalInformationError', (event) => {
  console.error('PersonalInformationError :', event);
  const errorDiv = $('#personal_information_error');
  errorDiv.html(event.detail.message);
  errorDiv.show('fast');
});

prestashop.on('GuestEmailEntered', (event) => {
  prestashop.email = event.detail.email;
  const $form = $('#guest-form');

  if ($form.length) {

    $.ajax({
      type: $form.attr('method') || 'POST',
      url: $form.attr('action'),
      data: $form.serialize(),
      success(response) {
        if (response.errors) {
          prestashop.emit('opc.PersonalInformationError', {
            detail: {
              message: response.message,
            },
          });
        }
        console.log('Form submit with success.', response);
        prestashop.emit('opc.GuestEmailSaved', {id_customer: response.idCustomer});
      },
      error(xhr, status, error) {
        prestashop.emit('opc.PersonalInformationError', {
          detail: {
            message: error,
          },
        });
      },
    });
  } else {
    console.warn('Form #guest-form not found.');
  }
});

prestashop.on('GuestEmailSaved', (event) => {
  console.log(event, $('#guest-form [name="id_customer"]'));

  prestashop.id_customer = event.id_customer;
  $('#guest-form input[name="id_customer"]').val(event.id_customer);
});

prestashop.on('LoginReady', () => {
  const $form = $('#login-form');

  if ($form.length) {
    $.ajax({
      type: $form.attr('method') || 'POST',
      url: $form.attr('action'),
      data: $form.serialize(),
      success(response) {
        console.log('Form submit with success.', response);
        if (response.errors) {
          console.error(response.message);
          prestashop.emit('opc.PersonalInformationError', {
            detail: {
              message: response.message,
            },
          });
        } else {
          prestashop.emit('opc.LoginSuccessfully', {id_customer: response.idCustomer});
        }
      },
      error(xhr, status, error) {
        console.error('error on submitting form :', error);
        prestashop.emit('opc.PersonalInformationError', {
          detail: {
            message: error,
          },
        });
      },
    });
  } else {
    console.warn('Form #guest-form not found.');
  }
});

prestashop.on('CustomerFormReady', () => {
  const $form = $('#customer-form');

  if ($form.length) {
    $.ajax({
      type: $form.attr('method') || 'POST',
      url: $form.attr('action'),
      data: $form.serialize(),
      success(response) {
        if (response.errors) {
          prestashop.emit('opc.PersonalInformationError', {
            detail: {
              message: response.message,
            },
          });
        }
        console.log('Form submit with success.', response);
        prestashop.emit('opc.CustomerSaved', {id_customer: response.idCustomer});
      },
      error(xhr, status, error) {
        prestashop.emit('opc.PersonalInformationError', {
          detail: {
            message: error,
          },
        });
      },
    });
  } else {
    console.warn('Form #guest-form not found.');
  }
});

prestashop.on('CustomerSaved', (event) => {
  $('#personal_information_error').hide();
  prestashop.id_customer = event.id_customer;
  $('#guest-form input[name="id_customer"]').val(event.id_customer);
});
