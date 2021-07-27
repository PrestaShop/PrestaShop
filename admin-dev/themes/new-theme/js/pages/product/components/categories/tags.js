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

const ProductCategoryMap = ProductMap.categories;

export default class Tags {
  constructor(
    containerSelector,
    initialCategories,
    defaultCategoryId,
    onDeleteCallback,
  ) {
    this.container = document.querySelector(containerSelector);
    this.defaultCategoryId = defaultCategoryId;
    this.onDeleteCallback = (categoryId) => onDeleteCallback(categoryId);
    this.refresh(initialCategories, defaultCategoryId);
  }

  refresh(categories) {
    this.toggle();
    this.container.innerHTML = '';

    const tagTemplate = this.container.dataset.prototype;

    categories.forEach((category) => {
      const template = tagTemplate.replace(RegExp(this.container.dataset.prototypeName, 'g'), category.id);
      const frag = document.createRange().createContextualFragment(template.trim());

      // do not allow removing default category (thus don't render the tag removal cross)
      if (this.defaultCategoryId === category.id) {
        frag.firstChild.querySelector(ProductCategoryMap.tagRemoveBtn).remove();
      }

      frag.firstChild.querySelector(ProductCategoryMap.categoryNamePreview).innerHTML = category.name;
      this.container.append(frag);
    }, this);
    this.listenDelete();
  }

  toggle() {
    this.container.querySelector(ProductCategoryMap.tagsContainer);
    this.container.classList.toggle(
      'd-block',
      this.container.querySelector(ProductCategoryMap.tagItem),
    );
  }

  listenDelete() {
    this.container.querySelectorAll(ProductCategoryMap.tagRemoveBtn).forEach((element) => {
      element.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopImmediatePropagation();
        const tagItem = event.currentTarget.closest(ProductCategoryMap.tagItem);
        const categoryId = Number(tagItem.dataset.id);

        tagItem.remove();
        this.onDeleteCallback(categoryId);
      });
    }, this);
  }
}
