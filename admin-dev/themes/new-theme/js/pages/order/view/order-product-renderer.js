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

import OrderViewPageMap from '../OrderViewPageMap';

const $ = window.$;

export default class OrderProductRenderer {
  removeProductFromList(orderDetailId) {
    const $productRow = $(`#orderProduct_${orderDetailId}`);

    $productRow.hide('fast', () => $productRow.remove());
  }

  addOrUpdateProductFromToList(orderProductId, newRow) {
    const $productRow = $(`#orderProduct_${orderProductId}`);
    if ($productRow.length > 0) {
      $productRow.html($(newRow).html());
    } else {
      $(OrderViewPageMap.productAddRow).before($(newRow).hide().fadeIn());
    }
  }

  moveProductsPanelToModificationPosition() {
    const $modificationPosition = $(OrderViewPageMap.productModificationPosition);

    $(OrderViewPageMap.productsPanel).detach().appendTo($modificationPosition);

    $modificationPosition.closest('.row').removeClass('d-none');

    $(OrderViewPageMap.productActionBtn).addClass('d-none');
    $(OrderViewPageMap.productAddActionBtn).removeClass('d-none');
    $(OrderViewPageMap.productAddRow).removeClass('d-none');
  }

  moveProductPanelToOriginalPosition() {
    $(OrderViewPageMap.productModificationPosition).closest('.row').addClass('d-none');

    $(OrderViewPageMap.productsPanel).detach().appendTo(OrderViewPageMap.productOriginalPosition);

    $(OrderViewPageMap.productActionBtn).removeClass('d-none');
    $(OrderViewPageMap.productAddActionBtn).addClass('d-none');
    $(OrderViewPageMap.productAddRow).addClass('d-none');
  }
}
