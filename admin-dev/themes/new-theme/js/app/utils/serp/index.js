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
import Vue from 'vue';
import serp from './serp.vue';

const $ = window.$;

/**
 * Vue component displaying a search page result, Google style.
 * Requires a tag with the id "#serp-app" to be present in the DOM to run it.
 * The component is automatically updated by watching several inputs.
 * Set the proper class to link a input to a part of the panel.
 */
class SerpApp {
  constructor(selectors, url) {
    // If the selector cannot be found, we do not load the Vue app
    if ($(selectors.container).length === 0) {
      return;
    }

    this.originalUrl = url;
    this.useMultiLang = selectors.multiLanguageInput !== undefined &&
                        selectors.multiLanguageItem !== undefined;

    if (this.useMultiLang) {
      this.multiLangInputSelector = selectors.multiLanguageInput;
      this.attachMultiLangEvents(selectors.multiLanguageItem);
    }

    this.data = {
      url,
      title: '',
      description: '',
    };

    this.vm = new Vue({
      el: selectors.container,
      template: '<serp ref="serp" :url="url" :title="title" :description="description" />',
      components: { serp },
      data: this.data,
    });

    this.initializeSelectors(selectors);
    this.attachInputEvents();
  }

  attachMultiLangEvents(itemSelector) {
    $('body').on(
      'click',
      itemSelector,
      () => {
        this.checkTitle();
        this.checkDesc();
        this.checkUrl();
      },
    );
  }

  initializeSelectors(selectors) {
    this.defaultTitle = $(selectors.defaultTitle);
    this.watchedTitle = $(selectors.watchedTitle);
    this.defaultDescription = $(selectors.defaultDescription);
    this.watchedDescription = $(selectors.watchedDescription);
    this.watchedMetaUrl = $(selectors.watchedMetaUrl);
  }

  attachInputEvents() {
    $(this.defaultTitle).on('keyup change', () => this.checkTitle());
    $(this.watchedTitle).on('keyup change', () => this.checkTitle());
    $(this.defaultDescription).on('keyup change', () => this.checkDesc());
    $(this.watchedDescription).on('keyup change', () => this.checkDesc());
    this.watchedMetaUrl.on('keyup change', () => this.checkUrl());

    this.checkTitle();
    this.checkDesc();
    this.checkUrl();
  }

  setTitle(title) {
    this.data.title = title;
  }

  setDescription(description) {
    this.data.description = description;
  }

  setUrl(rewrite) {
    this.data.url = this.originalUrl.replace(
      '{friendy-url}',
      rewrite,
    );
  }

  checkTitle() {
    let defaultTitle = this.defaultTitle;
    let watchedTitle = this.watchedTitle;

    if (this.useMultiLang) {
      watchedTitle = watchedTitle.closest(this.multiLangInputSelector).find('input');
      defaultTitle = defaultTitle.closest(this.multiLangInputSelector).find('input');
    }

    const title1 = watchedTitle.length ? watchedTitle.val() : '';
    const title2 = defaultTitle.length ? defaultTitle.val() : '';

    this.setTitle(title1 === '' ? title2 : title1);
    // Always check for url if title change
    this.checkUrl();
  }

  checkDesc() {
    let watchedDescription = this.watchedDescription;
    let defaultDescription = this.defaultDescription;

    if (this.useMultiLang) {
      watchedDescription = watchedDescription
        .closest(this.multiLangInputSelector)
        .find(this.watchedDescription.is('input') ? 'input' : 'textarea');
      defaultDescription = defaultDescription
        .closest(this.multiLangInputSelector)
        .find(this.defaultDescription.is('input') ? 'input' : 'textarea');
    }

    const desc1 = watchedDescription.length ? watchedDescription.val().innerText || watchedDescription.val() : '';
    const desc2 = defaultDescription.length ? $(defaultDescription.val()).text() || defaultDescription.val() : '';

    this.setDescription(desc1 === '' ? desc2 : desc1);
  }

  checkUrl() {
    let watchedMetaUrl = this.watchedMetaUrl;
    if (this.useMultiLang) {
      watchedMetaUrl = watchedMetaUrl.closest(this.multiLangInputSelector).find('input');
    }

    this.setUrl(watchedMetaUrl.val());
  }
}

export default SerpApp;
