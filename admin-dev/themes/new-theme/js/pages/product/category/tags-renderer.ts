/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import ProductMap from '@pages/product/product-map';
import {EventEmitter} from 'events';
import {Category} from '@pages/product/category/types';

const ProductCategoryMap = ProductMap.categories;

export default class TagsRenderer {
  eventEmitter: EventEmitter;

  container: HTMLElement;

  tagRemovedEventName: string;

  constructor(
    eventEmitter: EventEmitter,
    containerSelector: string,
    tagRemovedEventName: string,
  ) {
    this.eventEmitter = eventEmitter;
    this.container = document.querySelector(containerSelector) as HTMLElement;
    this.tagRemovedEventName = tagRemovedEventName;
    this.listenTagRemoval();
  }

  public render(categories: Array<Category>): void {
    this.container.innerHTML = '';
    const tagTemplate = this.container.dataset.prototype;
    const {prototypeName} = this.container.dataset;

    if (!tagTemplate || !prototypeName) {
      console.error('Tags prototype template or name is undefined or invalid');

      return;
    }

    let index = 0;
    categories.forEach((category) => {
      const template = tagTemplate.replace(RegExp(prototypeName, 'g'), String(index));
      const tplFragment = document.createRange().createContextualFragment(template.trim());

      if (tplFragment && tplFragment.firstChild && tplFragment.firstChild.parentNode) {
        const frag = tplFragment.firstChild.parentNode;
        const idInput = frag.querySelector(ProductCategoryMap.tagCategoryIdInput) as HTMLInputElement;
        idInput.value = String(category.id);

        const tagRemoveBtn = frag.querySelector(ProductCategoryMap.tagRemoveBtn) as HTMLElement;

        // don't show the tag removal element when it is the last category
        if (categories.length === 1) {
          tagRemoveBtn.classList.add('d-none');
        } else {
          tagRemoveBtn.classList.remove('d-none');
        }

        const namePreviewElement = frag.querySelector(ProductCategoryMap.categoryNamePreview);

        if (namePreviewElement) {
          namePreviewElement.innerHTML = category.displayName;

          const nameInput = frag.querySelector<HTMLInputElement>(ProductCategoryMap.categoryNameInput)!;
          const namePreviewInput = frag.querySelector<HTMLInputElement>(ProductCategoryMap.namePreviewInput)!;

          if (!nameInput || !namePreviewInput) {
            // eslint-disable-next-line max-len
            console.error(`Missing ${ProductCategoryMap.categoryNameInput} or ${ProductCategoryMap.namePreviewInput} preview input`);
            return;
          }

          nameInput.value = category.name;
          namePreviewInput.value = category.displayName;
        }

        this.container.append(frag);
      }

      index += 1;
    });

    this.listenTagRemoval();
  }

  private listenTagRemoval(): void {
    this.container.querySelectorAll(ProductCategoryMap.tagRemoveBtn).forEach((element) => {
      element.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopImmediatePropagation();
        const clickedBtn = event.currentTarget as HTMLElement;
        const tagItem = clickedBtn.closest(ProductCategoryMap.tagItem) as HTMLElement;

        if (tagItem) {
          const idInput = tagItem.querySelector(ProductCategoryMap.tagCategoryIdInput) as HTMLInputElement;
          tagItem.remove();
          this.eventEmitter.emit(this.tagRemovedEventName, Number(idInput.value));
        }
      });
    });
  }
}
