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

import ProductMap from '@pages/product/product-map';
import ProductEventMap from '@pages/product/product-event-map';
import {EventEmitter} from 'events';

const ProductCategoryMap = ProductMap.categories;

export default class TagsRenderer {
  eventEmitter: EventEmitter;

  container: HTMLElement;

  constructor(
    eventEmitter: EventEmitter,
    containerSelector: string,
  ) {
    this.eventEmitter = eventEmitter;
    this.container = document.querySelector(containerSelector) as HTMLElement;
  }

  public render(categories: Array<Category>): void {
    this.container.innerHTML = '';

    const tagTemplate = this.container.dataset.prototype;
    const {prototypeName} = this.container.dataset;

    if (!tagTemplate || !prototypeName) {
      console.error('Tags prototype template or name is undefined or invalid');

      return;
    }

    categories.forEach((category) => {
      const template = tagTemplate.replace(RegExp(prototypeName, 'g'), String(category.id));
      const tplFragment = document.createRange().createContextualFragment(template.trim());

      if (tplFragment && tplFragment.firstChild && tplFragment.firstChild.parentNode) {
        const frag = tplFragment.firstChild.parentNode;
        const defaultCategoryCheckbox = frag.querySelector(ProductCategoryMap.defaultCategoryCheckbox) as HTMLInputElement;
        defaultCategoryCheckbox.checked = category.isDefault;

        // don't render the tag removal element for main category
        if (category.isDefault) {
          const tagRemoveBtn = frag.querySelector(ProductCategoryMap.tagRemoveBtn);

          if (tagRemoveBtn) {
            tagRemoveBtn.remove();
          }
        }

        const namePreviewElement = frag.querySelector(ProductCategoryMap.categoryNamePreview);

        if (namePreviewElement) {
          namePreviewElement.innerHTML = category.name;
        }

        this.container.append(frag);
      }
    });

    this.listenTagRemoval();
    this.toggleContainerVisibility();
    this.eventEmitter.emit(ProductEventMap.categories.categoriesUpdated);
  }

  private toggleContainerVisibility(): void {
    this.container.querySelector(ProductCategoryMap.tagsContainer);
    this.container.classList.toggle(
      'd-block',
      this.container.querySelector(ProductCategoryMap.tagItem) !== null,
    );
  }

  private listenTagRemoval(): void {
    this.container.querySelectorAll(ProductCategoryMap.tagRemoveBtn).forEach((element) => {
      element.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopImmediatePropagation();
        const clickedBtn = event.currentTarget as HTMLElement;
        const tagItem = clickedBtn.closest(ProductCategoryMap.tagItem) as HTMLElement;

        if (tagItem) {
          const categoryId = Number(tagItem.dataset.id);

          tagItem.remove();
          this.eventEmitter.emit(ProductEventMap.categories.categoryRemoved, categoryId);
        }
      });
    });
  }
}
