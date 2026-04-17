/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import OrderViewPageMap from '@pages/order/OrderViewPageMap';
import OrderShippingManager from '@pages/order/order-shipping-manager';
import InvoiceNoteManager from '@pages/order/invoice-note-manager';
import OrderViewPage from '@pages/order/view/order-view-page';
import OrderProductAutocomplete from '@pages/order/view/order-product-add-autocomplete';
import OrderProductAdd from '@pages/order/view/order-product-add';
import SplitShipmentManager from '@pages/order/split-shipment-manager';
import OrderViewPageMessagesHandler from './message/order-view-page-messages-handler';
import MergeShipmentManager from './merge-shipment-manager';
import EditShipmentManager from './edit-shipment-manager';

const {$} = window;

$(() => {
  const DISCOUNT_TYPE_AMOUNT = 'amount';
  const DISCOUNT_TYPE_PERCENT = 'percent';
  const DISCOUNT_TYPE_FREE_SHIPPING = 'free_shipping';
  // eslint-disable-next-line max-len
  const multishipmentIsEnabled = document.querySelector<HTMLElement>(OrderViewPageMap.productsTable)?.dataset.multishipmentEnabled === '1';

  new SplitShipmentManager();
  new MergeShipmentManager();
  new EditShipmentManager();
  new OrderShippingManager();

  window.prestashop.component.initComponents([
    'TextWithLengthCounter',
  ]);
  const orderViewPage = new OrderViewPage();

  if (!multishipmentIsEnabled) {
    const orderAddAutocomplete = new OrderProductAutocomplete($(OrderViewPageMap.productSearchInput));
    const orderAdd = new OrderProductAdd();

    orderAddAutocomplete.listenForSearch();
    orderAddAutocomplete.onItemClickedCallback = (product: Record<string, any> | undefined): void => orderAdd.setProduct(product);
  }

  orderViewPage.listenForProductPack();
  orderViewPage.listenForProductDelete();
  orderViewPage.listenForProductEdit();
  orderViewPage.listenForProductAdd();
  orderViewPage.listenForProductPagination();
  orderViewPage.listenForRefund();
  orderViewPage.listenForCancelProduct();

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
      $(OrderViewPageMap.addCartRuleSubmit).prop('disabled', true);
    });
    $modal.on('hidden.bs.modal', () => {
      $(OrderViewPageMap.addCartRuleNameInput).val('');
      $(OrderViewPageMap.addCartRuleTypeSelect).val(DISCOUNT_TYPE_PERCENT).trigger('change');
      $(OrderViewPageMap.addCartRuleValueInput).val('');
    });

    $form.find(OrderViewPageMap.addCartRuleNameInput).on('keyup', (event) => {
      const cartRuleName = <string>$(event.currentTarget).val();

      $(OrderViewPageMap.addCartRuleSubmit).prop('disabled', cartRuleName.trim().length === 0);
    });

    $form.find(OrderViewPageMap.addCartRuleApplyOnAllInvoicesCheckbox).on('change', (event) => {
      const isChecked = $(event.currentTarget).is(':checked');
      $invoiceSelect.prop('disabled', isChecked);
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

      $valueInput.prop('disabled', selectedCartRuleType === DISCOUNT_TYPE_FREE_SHIPPING);
      $valueFormGroup.toggleClass('d-none', selectedCartRuleType === DISCOUNT_TYPE_FREE_SHIPPING);
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

      $btn.prop('disabled', parseInt(<string>selectedOrderStatusId, 10) === $btn.data('orderStatusId'));
    });
  }

  function initChangeAddressFormHandler() {
    const $modal = $(OrderViewPageMap.updateCustomerAddressModal);

    $(OrderViewPageMap.openOrderAddressUpdateModalBtn).on('click', (event) => {
      $modal.find(OrderViewPageMap.updateOrderAddressTypeInput).val($(event.currentTarget).data('addressType'));
    });
  }
});
