/*
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

$(document).ready(function(){

    var handler = new PaymentOptionHandler();

    $('#confirmOrder').on('click', function(event){
        /* Avoid any further action */
        event.preventDefault();
        event.stopPropagation();

        if (!handler.checkTOS())
        {
            alert('Please check TOS box first !')
            return;
        }

        var payment_details = handler.getSelectedPaymentOptionDetails();
        if (payment_details !== false && payment_details.action) {
            handler.submitPaymentOption(payment_details.action, payment_details.data);
        } else if (!payment_details.action && payment_details.has_form === true) {
                payment_details.form_to_submit.submit();
        } else {
            alert('An error occured please be sure you have properly selected your payment option!');
            return;
        }
    });


});

var PaymentOptionHandler = function() {

    /* Return array with all payment option information required */
    this.getSelectedPaymentOptionDetails = function()
    {
        var payment_option = $('input:checked', '#HOOK_ADVANCED_PAYMENT');
        if (typeof payment_option.prop('checked') != 'undefined' && payment_option.prop('checked') === true)
        {
            // @TODO: Check if there's an embedded form within this payment option to get it as well !
            var po_action = payment_option.attr('data-payment-action');
            var po_name = payment_option.attr('data-payment-option-name');
            var po_form = $('input:checked', '#HOOK_ADVANCED_PAYMENT').parents('.payment_module').first().next('form');
            if (po_form) {
                var po_has_form = true;
            } else {
                var po_has_form = false;
            }
            return {name: po_name, action: po_action, data: {}, has_form: po_has_form, form_to_submit: po_form};
        }

        return false;
    }

    this.checkTOS = function() {

        if ($('#cgv').prop('checked'))
            return true;

        return false;
    }





    this.submitPaymentOption = function(action, params, method) {

        var method = method || "post"; // Set method to post by default if not specified.

        // The rest of this code assumes you are not using a library.
        // It can be made less wordy if you use one.
        var form = document.createElement("form");
        form.setAttribute("method", method);
        form.setAttribute("action", action);

        for(var key in params)
        {
            if(params.hasOwnProperty(key))
            {
                var hiddenField = document.createElement("input");
                hiddenField.setAttribute("type", "hidden");
                hiddenField.setAttribute("name", key);
                hiddenField.setAttribute("value", params[key]);
                // Add input to global form
                form.appendChild(hiddenField);
            }
        }
        form.submit();
    }
}
