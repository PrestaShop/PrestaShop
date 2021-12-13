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
import ComponentsMap from '@components/components-map';

import ChangeEvent = JQuery.ChangeEvent;

const {$} = window;

export type DeltaQuantityConfig = {
  containerSelector: string;
  deltaInputSelector: string;
  updateQuantitySelector: string;
  modifiedQuantityClass: string;
  newQuantitySelector: string;
  initialQuantityPreviewSelector: string;
}

export default class DeltaQuantityInput {
  private config: DeltaQuantityConfig;

  constructor(config: Partial<DeltaQuantityConfig> = {}) {
    const componentMap = ComponentsMap.deltaQuantityInput;
    this.config = {
      containerSelector: componentMap.containerSelector,
      deltaInputSelector: componentMap.deltaInputSelector,
      updateQuantitySelector: componentMap.updateQuantitySelector,
      modifiedQuantityClass: componentMap.modifiedQuantityClass,
      newQuantitySelector: componentMap.newQuantitySelector,
      initialQuantityPreviewSelector: componentMap.initialQuantityPreviewSelector,
      ...config,
    };

    this.init();
  }

  public applyNewQuantity(input: HTMLInputElement): void {
    const $container: JQuery = $(input).closest(this.config.containerSelector);

    if ($container.length === 0) {
      console.error(`container not found by ${this.config.containerSelector}`);
      return;
    }

    const deltaQuantity = this.getDeltaQuantity(input);
    const initialQuantity = this.getInitialQuantity($container);
    const newQuantity: number = initialQuantity + deltaQuantity;

    $container.data('initialQuantity', newQuantity);
    $container.find(this.config.initialQuantityPreviewSelector).text(newQuantity);
    $container.find(this.config.newQuantitySelector).text(0);
    $container.find(this.config.updateQuantitySelector).removeClass(this.config.modifiedQuantityClass);
  }

  private init(): void {
    $(document).on('change', `${this.config.containerSelector} ${this.config.deltaInputSelector}`, (event: ChangeEvent) => {
      const deltaInput: HTMLElement = event.target;
      const $container: JQuery = $(deltaInput).closest(this.config.containerSelector);
      const deltaQuantity = this.getDeltaQuantity(event.target);
      const initialQuantity = this.getInitialQuantity($container);
      const updatedQuantity: number = initialQuantity + deltaQuantity;

      const $newQuantity: JQuery = $container.find(this.config.newQuantitySelector);
      $newQuantity.text(updatedQuantity);

      const $updateElement = $container.find(this.config.updateQuantitySelector);
      $updateElement.toggleClass(this.config.modifiedQuantityClass, deltaQuantity !== 0);
    });
  }

  private getDeltaQuantity(deltaInput: HTMLElement): number {
    let delta: number = parseInt(<string> $(deltaInput).val(), 10);

    if (Number.isNaN(delta)) {
      delta = 0;
    }

    return delta;
  }

  private getInitialQuantity($container: JQuery): number {
    let initialQuantity = parseInt(<string> $container.data('initialQuantity'), 10);

    if (Number.isNaN(initialQuantity)) {
      initialQuantity = 0;
    }

    return initialQuantity;
  }
}
