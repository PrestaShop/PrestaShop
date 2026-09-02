/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import ComponentsMap from '@components/components-map';

import TriggeredEvent = JQuery.TriggeredEvent;

const {$} = window;

export type DeltaQuantityConfig = {
  containerSelector: string;
  deltaInputSelector: string;
  updateQuantitySelector: string;
  modifiedQuantityClass: string;
  newQuantitySelector: string;
  quantityInputSelector: string;
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
      quantityInputSelector: componentMap.quantityInputSelector,
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
    const deltaInputSelector: string = `${this.config.containerSelector} ${this.config.deltaInputSelector}`;
    $(document).on('change keyup', deltaInputSelector, (event: TriggeredEvent) => {
      this.updateDeltaQuantity(event.target);
    });
  }

  private updateDeltaQuantity(deltaInput: HTMLElement): void {
    const $container: JQuery = $(deltaInput).closest(this.config.containerSelector);
    const deltaQuantity = this.getDeltaQuantity(deltaInput);
    const initialQuantity = this.getInitialQuantity($container);
    const updatedQuantity: number = initialQuantity + deltaQuantity;

    const $newQuantity: JQuery = $container.find(this.config.newQuantitySelector);
    $newQuantity.text(updatedQuantity);
    $container.find(this.config.quantityInputSelector).val(updatedQuantity);

    const $updateElement = $container.find(this.config.updateQuantitySelector);
    $updateElement.toggleClass(this.config.modifiedQuantityClass, deltaQuantity !== 0);
  }

  private getDeltaQuantity(deltaInput: HTMLElement): number {
    let delta: number = parseInt(Number($(deltaInput).val()).toString(), 10);

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
