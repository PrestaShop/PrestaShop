import prestashop from 'prestashop';
import $ from 'jquery';

prestashop.selectors = {
  quantityWanted: '#quantity_wanted',
  product: {
    imageContainer: '.image-container',
    container: '.product-container',
    availability: '#product-availability',
    actions: '.product-actions',
    variants: '.product-variants',
    refresh: '.product-refresh',
    miniature: '.js-product-miniature',
    minimalQuantity: '.product-minimal-quantity',
    addToCart: '.product-add-to-cart',
  },
  listing: {
    quickview: '.quick-view',
  },
  checkout: {
    form: '.checkout-step form',
    currentStep: 'js-current-step',
    step: '.checkout-step',
    stepTitle: '.step-title',
    confirmationSelector: '#payment-confirmation',
    conditionsSelector: '#conditions-to-approve',
    conditionAlertSelector: '.js-alert-payment-conditions',
    additionalInformatonSelector: '.js-additional-information',
    optionsForm: '.js-payment-option-form',
    termsCheckboxSelector: '#conditions-to-approve input[name="conditions_to_approve[terms-and-conditions]"]',
    paymentBinary: '.payment-binary',
    deliveryFormSelector: '#js-delivery',
    summarySelector: '#js-checkout-summary',
    deliveryStepSelector: '#checkout-delivery-step',
    editDeliveryButtonSelector: '.js-edit-delivery',
    deliveryOption: '.delivery-option',
    cartPaymentStepRefresh: '.js-cart-payment-step-refresh',
    editAddresses: '.js-edit-addresses',
    deliveryAddressRadios: '#delivery-addresses input[type=radio], #invoice-addresses input[type=radio]',
    addressItem: '.address-item',
    addressesStep: '#checkout-addresses-step',
    addressItemChecked: '.address-item:has(input[type=radio]:checked)',
    addressError: '.js-address-error',
    notValidAddresses: '#not-valid-addresses',
    invoiceAddresses: '#invoice-addresses',
    addressForm: '.js-address-form',
  },
  cart: {
    detailedTotals: '.cart-detailed-totals',
    summaryItemsSubtotal: '.cart-summary-items-subtotal',
    summarySubTotalsContainer: '.cart-summary-subtotals-container',
    summaryTotals: '.cart-summary-products',
    summaryProducts: '.cart-summary-products',
    detailedActions: '.cart-detailed-actions',
    voucher: '.cart-voucher',
    overview: '.cart-overview',
    summaryTop: '.cart-summary-top',
    productCustomizationId: '#product_customization_id',
    lineProductQuantity: '.js-cart-line-product-quantity',
  },
};

$(document).ready(() => {
  prestashop.emit('selectorsInit');
});
