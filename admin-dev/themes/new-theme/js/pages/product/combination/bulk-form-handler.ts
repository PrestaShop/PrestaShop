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

import ConfirmModal from '@components/modal';
import ProductMap from '@pages/product/product-map';
import CombinationsService from '@pages/product/services/combinations-service';

const CombinationMap = ProductMap.combinations;

export default class BulkFormHandler {
  //@todo: actually productId is redundant here, but its necessary for combinationsService (probably its not right)
  private combinationsService: CombinationsService;

  constructor() {
    this.combinationsService = new CombinationsService();
    this.init();
  }

  private init() {
    const template = document.querySelector(CombinationMap.bulkCombinationFormTemplate) as HTMLScriptElement;
    const content = template.innerHTML;
    const modal = new ConfirmModal(
      {
        id: CombinationMap.bulkCombinationModalId,
        confirmMessage: content,
      },
      () => this.submitForm(),
    );

    //@todo: probably this should be wrapped into some public method reachable from outsid
    const btn = document.querySelector(CombinationMap.bulkCombinationFormBtn) as HTMLButtonElement;
    btn.addEventListener('click', () => modal.show());
  }

  private submitForm() {
    const form = document.querySelector(CombinationMap.bulkCombinationForm) as HTMLFormElement;
    this.bulkUpdate(form);
  }

  private bulkUpdate(form: HTMLFormElement): void {
    const combinationsContainer = document.querySelector(CombinationMap.combinationsContainer) as HTMLDivElement;
    const checkedBoxes = combinationsContainer.querySelectorAll(`${CombinationMap.tableRow.isSelectedCombination}:checked`);

    checkedBoxes.forEach((checkbox: Element) => {
      const idInput = checkbox.closest(CombinationMap.tableRow.tableRowSelector)
        ?.querySelector(CombinationMap.combinationIdInputsSelector) as HTMLInputElement;

      this.combinationsService.bulkUpdate(Number(idInput.value), $(form).serializeArray());
    });
  }
}
