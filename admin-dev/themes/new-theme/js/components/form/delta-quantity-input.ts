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
import ChangeEvent = JQuery.ChangeEvent;

const {$} = window;

export type DeltaQuantityConfig = {
  containerSelector: string;
  deltaInputSelector: string;
  updateQuantitySelector: string;
  modifiedQuantityClass: string;
  newQuantitySelector: string;
}
export type InputDeltaQuantityConfig = Partial<DeltaQuantityConfig>;

class DeltaQuantityInput {
  private config: DeltaQuantityConfig;

  constructor(config: InputDeltaQuantityConfig = {}) {
    this.config = {
      containerSelector: '.delta-quantity',
      deltaInputSelector: '.delta-quantity-delta',
      updateQuantitySelector: '.quantity-update',
      modifiedQuantityClass: 'quantity-modified',
      newQuantitySelector: '.new-quantity',
      ...config,
    };

    this.init();
  }

  private init(): void {
    $(this.config.containerSelector).on('change', this.config.deltaInputSelector, (event: ChangeEvent) => {
      const $deltaInput: JQuery = $(event.target);
      const $container: JQuery = $deltaInput.closest(this.config.containerSelector);

      let delta: number = parseInt(<string> $deltaInput.val(), 10);

      if (Number.isNaN(delta)) {
        delta = 0;
      }
      let initialQuantity = parseInt(<string> $container.data('initialQuantity'), 10);

      if (Number.isNaN(initialQuantity)) {
        initialQuantity = 0;
      }
      const updatedQuantity: number = initialQuantity + delta;

      const $newQuantity: JQuery = $container.find(this.config.newQuantitySelector);
      $newQuantity.text(updatedQuantity);

      const $updateElement = $container.find(this.config.updateQuantitySelector);
      $updateElement.toggleClass(this.config.modifiedQuantityClass, delta !== 0);
    });
  }
}

export default DeltaQuantityInput;
