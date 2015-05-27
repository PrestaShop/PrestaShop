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

    $('p.payment_module').on('click', function(event){
        handler.selectOption($(this));
    });

    $('#confirmOrder').on('click', function(event){
        /* Avoid any further action */
        event.preventDefault();
        event.stopPropagation();

        if (!handler.checkTOS())
        {
            alert('Please check TOS box first !');
            return;
        }

        var payment_details = handler.getSelectedPaymentOptionDetails();
        if (payment_details !== false && payment_details.action) {
            handler.submitPaymentOption(payment_details.action, payment_details.data);
        } else if (!payment_details.action && payment_details.has_form === true) {
                payment_details.form_to_submit.submit();
        } else {
            alert('TECHNICAL ERROR: Something went wrong in processing payment!');
            return;
        }
    });

});

var PaymentOptionHandler = function() {

    this.selected_option = null;

    this.selectOption = function(elem) {
        if (typeof elem === 'undefined' || elem.hasClass('payment_selected')) {
            return;
        }
        if (this.selected_option !== null) {
            this.unselectOption();
        }
        this.selected_option = elem;
        this.selected_option.addClass('payment_selected');
        this.selected_option.children('a:first').css({
            'border': '1px solid #55c65e',
            'border-radius': '4px'
        });
        this.selected_option.children('a:first').children('.payment_option_selected:first').fadeIn();
    };

    this.unselectOption = function() {
        this.selected_option.children('a:first').css({
            'border': '1px solid #d6d4d4',
            'border-radius': '4px'
        });
        this.selected_option.children('a:first').children('.payment_option_selected:first').fadeOut();
        this.selected_option.removeClass('payment_selected');
    };

    /* Return array with all payment option information required */
    this.getSelectedPaymentOptionDetails = function()
    {
        if (typeof this.selected_option !== 'undefined' && this.selected_option !== null && this.selected_option.hasClass('payment_selected'))
        {
            var data_input = this.selected_option.children('a:first').children('span:last').children('input:last');

            if (typeof data_input === 'undefined') {
                return false;
            }

            var po_action = data_input.attr('data-payment-action');
            var po_name = data_input.attr('data-payment-option-name');
            var po_form = this.selected_option.next('form');
            var po_has_form = false;

            if (typeof po_form !== 'undefined') {
                var po_has_form = true;
            }

            return {name: po_name, action: po_action, data: {}, has_form: po_has_form, form_to_submit: po_form};
        }

        return false;
    };

    this.checkTOS = function() {

        if ($('#cgv').prop('checked'))
            return true;

        return false;
    };

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
};
