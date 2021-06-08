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

import OrderViewPageMap from '@pages/order/OrderViewPageMap';
import OrderShippingManager from '@pages/order/order-shipping-manager';
import InvoiceNoteManager from '@pages/order/invoice-note-manager';
import OrderViewPage from '@pages/order/view/order-view-page';
import OrderProductAutocomplete from '@pages/order/view/order-product-add-autocomplete';
import OrderProductAdd from '@pages/order/view/order-product-add';
import TextWithLengthCounter from '@components/form/text-with-length-counter';
import OrderViewPageMessagesHandler from './message/order-view-page-messages-handler';

const {$} = window;

$(() => {
  const DISCOUNT_TYPE_AMOUNT = 'amount';
  const DISCOUNT_TYPE_PERCENT = 'percent';
  const DISCOUNT_TYPE_FREE_SHIPPING = 'free_shipping';

  new OrderShippingManager();
  new TextWithLengthCounter();
  const orderViewPage = new OrderViewPage();
  const orderAddAutocomplete = new OrderProductAutocomplete($(OrderViewPageMap.productSearchInput));
  const orderAdd = new OrderProductAdd();

  orderViewPage.listenForProductPack();
  orderViewPage.listenForProductDelete();
  orderViewPage.listenForProductEdit();
  orderViewPage.listenForProductAdd();
  orderViewPage.listenForProductPagination();
  orderViewPage.listenForRefund();
  orderViewPage.listenForCancelProduct();

  orderAddAutocomplete.listenForSearch();
  orderAddAutocomplete.onItemClickedCallback = (product) => orderAdd.setProduct(product);

  handlePaymentDetailsToggle();
  handlePrivateNoteChange();
  handleOrderNoteChange();
  handleUpdateOrderStatusButton();

  new InvoiceNoteManager();
  const orderViewPageMessageHandler = new OrderViewPageMessagesHandler();
  orderViewPageMessageHandler.listenForPredefinedMessageSelection();
  orderViewPageMessageHandler.listenForFullMessagesOpen();
  $(OrderViewPageMap.privateNoteToggleBtn).on('click', (event) => {
    event.preventDefault();
    togglePrivateNoteBlock();
  });

  $(OrderViewPageMap.orderNoteToggleBtn).on('click', (event) => {
    event.preventDefault();
    toggleOrderNoteBlock();
  });

  $(OrderViewPageMap.printOrderViewPageButton).on('click', () => {
    const tempTitle = document.title;
    document.title = $(OrderViewPageMap.mainDiv).data('orderTitle');
    window.print();
    document.title = tempTitle;
  });

  initAddCartRuleFormHandler();
  initChangeAddressFormHandler();
  initHookTabs();

  function initHookTabs() {
    $(OrderViewPageMap.orderHookTabsContainer)
      .find('.nav-tabs li:first-child a')
      .tab('show');
  }

  function handlePaymentDetailsToggle() {
    $(OrderViewPageMap.orderPaymentDetailsBtn).on('click', (event) => {
      const $paymentDetailRow = $(event.currentTarget)
        .closest('tr')
        .next(':first');

      $paymentDetailRow.toggleClass('d-none');
    });
  }

  function togglePrivateNoteBlock() {
    const $block = $(OrderViewPageMap.privateNoteBlock);
    const $btn = $(OrderViewPageMap.privateNoteToggleBtn);
    const isPrivateNoteOpened = $btn.hasClass('is-opened');

    if (isPrivateNoteOpened) {
      $btn.removeClass('is-opened');
      $block.addClass('d-none');
    } else {
      $btn.addClass('is-opened');
      $block.removeClass('d-none');
    }

    const $icon = $btn.find('.material-icons');
    $icon.text(isPrivateNoteOpened ? 'add' : 'remove');
  }

  function handlePrivateNoteChange() {
    const $submitBtn = $(OrderViewPageMap.privateNoteSubmitBtn);

    $(OrderViewPageMap.privateNoteInput).on('input', () => {
      $submitBtn.prop('disabled', false);
    });
  }

  function toggleOrderNoteBlock() {
    const $block = $(OrderViewPageMap.orderNoteBlock);
    const $btn = $(OrderViewPageMap.orderNoteToggleBtn);
    const isNoteOpened = $btn.hasClass('is-opened');

    $btn.toggleClass('is-opened', !isNoteOpened);
    $block.toggleClass('d-none', isNoteOpened);

    const $icon = $btn.find('.material-icons');
    $icon.text(isNoteOpened ? 'add' : 'remove');
  }

  function handleOrderNoteChange() {
    const $submitBtn = $(OrderViewPageMap.orderNoteSubmitBtn);

    $(OrderViewPageMap.orderNoteInput).on('input', () => {
      $submitBtn.prop('disabled', false);
    });
  }

  function initAddCartRuleFormHandler() {
    const $modal = $(OrderViewPageMap.addCartRuleModal);
    const $form = $modal.find('form');
    const $invoiceSelect = $modal.find(OrderViewPageMap.addCartRuleInvoiceIdSelect);
    const $valueHelp = $modal.find(OrderViewPageMap.cartRuleHelpText);
    const $valueInput = $form.find(OrderViewPageMap.addCartRuleValueInput);
    const $valueFormGroup = $valueInput.closest('.form-group');

    $modal.on('shown.bs.modal', () => {
      $(OrderViewPageMap.addCartRuleSubmit).attr('disabled', true);
    });

    $form.find(OrderViewPageMap.addCartRuleNameInput).on('keyup', (event) => {
      const cartRuleName = $(event.currentTarget).val();
      $(OrderViewPageMap.addCartRuleSubmit).attr('disabled', cartRuleName.trim().length === 0);
    });

    $form.find(OrderViewPageMap.addCartRuleApplyOnAllInvoicesCheckbox).on('change', (event) => {
      const isChecked = $(event.currentTarget).is(':checked');
      $invoiceSelect.attr('disabled', isChecked);
    });

    $form.find(OrderViewPageMap.addCartRuleTypeSelect).on('change', (event) => {
      const selectedCartRuleType = $(event.currentTarget).val();
      const $valueUnit = $form.find(OrderViewPageMap.addCartRuleValueUnit);

      if (selectedCartRuleType === DISCOUNT_TYPE_AMOUNT) {
        $valueHelp.removeClass('d-none');
        $valueUnit.html($valueUnit.data('currencySymbol'));
      } else {
        $valueHelp.addClass('d-none');
      }

      if (selectedCartRuleType === DISCOUNT_TYPE_PERCENT) {
        $valueUnit.html('%');
      }

      if (selectedCartRuleType === DISCOUNT_TYPE_FREE_SHIPPING) {
        $valueFormGroup.addClass('d-none');
        $valueInput.attr('disabled', true);
      } else {
        $valueFormGroup.removeClass('d-none');
        $valueInput.attr('disabled', false);
      }
    });
  }

  function handleUpdateOrderStatusButton() {
    const $btn = $(OrderViewPageMap.updateOrderStatusActionBtn);
    const $wrapper = $(OrderViewPageMap.updateOrderStatusActionInputWrapper);

    $(OrderViewPageMap.updateOrderStatusActionInput).on('change', (event) => {
      const $element = $(event.currentTarget);
      const $option = $('option:selected', $element);
      const selectedOrderStatusId = $element.val();

      $wrapper.css('background-color', $option.data('background-color'));
      $wrapper.toggleClass('is-bright', $option.data('is-bright') !== undefined);

      $btn.prop('disabled', parseInt(selectedOrderStatusId, 10) === $btn.data('orderStatusId'));
    });
  }

  function initChangeAddressFormHandler() {
    const $modal = $(OrderViewPageMap.updateCustomerAddressModal);

    $(OrderViewPageMap.openOrderAddressUpdateModalBtn).on('click', (event) => {
      $modal.find(OrderViewPageMap.updateOrderAddressTypeInput).val($(event.currentTarget).data('addressType'));
    });
  }
});
