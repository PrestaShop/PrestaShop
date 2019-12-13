/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

import OrderViewPageMap from '@pages/order/OrderViewPageMap';
import OrderShippingManager from '@pages/order/order-shipping-manager';
import InvoiceNoteManager from '@pages/order/invoice-note-manager';
import OrderViewPage from '@pages/order/view/order-view-page';
import OrderProductAutocomplete from '@pages/order/view/order-product-add-autocomplete';
import OrderProductAdd from '@pages/order/view/order-product-add';
import OrderViewPageMessagesHandler from './message/order-view-page-messages-handler';
import TextWithLengthCounter from '@components/form/text-with-length-counter';

const {$} = window;

$(() => {
  const DISCOUNT_TYPE_AMOUNT = 'amount';
  const DISCOUNT_TYPE_FREE_SHIPPING = 'free_shipping';

  new OrderShippingManager();
  new TextWithLengthCounter();
  const orderViewPage = new OrderViewPage();
  const orderAddAutocomplete = new OrderProductAutocomplete($(OrderViewPageMap.productSearchInput));
  const orderAdd = new OrderProductAdd();

  orderViewPage.listenForProductDelete();
  orderViewPage.listenForProductEdit();
  orderViewPage.listenForProductAdd();
  orderViewPage.listenForProductPagination();

  orderAddAutocomplete.listenForSearch();
  orderAddAutocomplete.onItemClickedCallback = product => orderAdd.setProduct(product);

  handlePaymentDetailsToggle();
  handlePrivateNoteChange();
  handleUpdateOrderStatusButton();

  new InvoiceNoteManager();
  const orderViewPageMessageHandler = new OrderViewPageMessagesHandler();
  orderViewPageMessageHandler.listenForPredefinedMessageSelection();
  orderViewPageMessageHandler.listenForFullMessagesOpen();
  $(OrderViewPageMap.privateNoteToggleBtn).on('click', (event) => {
    event.preventDefault();
    togglePrivateNoteBlock();
  });

  initAddCartRuleFormHandler();
  initChangeAddressFormHandler();
  initHookTabs();

  function initHookTabs() {
    $(OrderViewPageMap.orderHookTabsContainer).find('.nav-tabs li:first-child a').tab('show');
  }

  function handlePaymentDetailsToggle() {
    $(OrderViewPageMap.orderPaymentDetailsBtn).on('click', (event) => {
      const $paymentDetailRow = $(event.currentTarget).closest('tr').next(':first');

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

    $(OrderViewPageMap.privateNoteInput).on('input', (event) => {
      const note = $(event.currentTarget).val();
      $submitBtn.prop('disabled', !note);
    });
  }

  function initAddCartRuleFormHandler() {
    const $modal = $(OrderViewPageMap.addCartRuleModal);
    const $form = $modal.find('form');
    const $valueHelp = $modal.find(OrderViewPageMap.cartRuleHelpText);
    const $invoiceSelect = $modal.find(OrderViewPageMap.addCartRuleInvoiceIdSelect);
    const $valueInput = $form.find(OrderViewPageMap.addCartRuleValueInput);
    const $valueFormGroup = $valueInput.closest('.form-group');

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

    $(OrderViewPageMap.updateOrderStatusActionInput).on('change', (event) => {
      const selectedOrderStatusId = $(event.currentTarget).val();

      $btn.prop('disabled', parseInt(selectedOrderStatusId, 10) === $btn.data('orderStatusId'));
    });
  }

  function initChangeAddressFormHandler() {
    const $modal = $(OrderViewPageMap.updateCustomerAddressModal);

    $(OrderViewPageMap.openOrderAddressUpdateModalBtn).on('click', (event) => {
      $modal.find(OrderViewPageMap.updateOrderAddressTypeInput).val($btn.data('address-type'));
    });
  }
});
