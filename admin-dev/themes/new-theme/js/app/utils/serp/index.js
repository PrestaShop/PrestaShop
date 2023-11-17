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
import {createApp} from 'vue';
import {EventEmitter} from '@components/event-emitter';
import serp from './serp.vue';

const {$} = window;

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
    this.selectors = selectors;
    this.useMultiLang = selectors.multiLanguageInput !== undefined || selectors.multiLanguageField !== undefined;

    if (this.useMultiLang) {
      const possibleSelectors = [];

      if (selectors.multiLanguageInput) {
        possibleSelectors.push(selectors.multiLanguageInput);
      }
      if (selectors.multiLanguageField) {
        possibleSelectors.push(selectors.multiLanguageField);
      }
      this.multiLangSelector = possibleSelectors.join(',');
      this.attachMultiLangEvents();
    }

    this.data = {
      url,
      title: '',
      description: '',
    };

    this.initializeSelectors(selectors);
    this.attachInputEvents();
  }

  updateComponent() {
    if (this.vm) {
      this.vm.unmount();
    }

    this.vm = createApp({
      template: '<serp ref="serp" :url="url" :title="title" :description="description" />',
      components: {serp},
      data: () => this.data,
    });

    this.vm.mount(this.selectors.container);
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

    EventEmitter.on('languageSelected', () => {
      this.checkTitle();
      this.checkDesc();
      this.checkUrl();
    });
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
    // We replace two placeholders because there was a typo in the initial one ('friendy' instead of 'friendly')
    this.data.url = this.originalUrl.replace(
      '{friendy-url}',
      rewrite,
    );
    this.data.url = this.data.url.replace(
      '{friendly-url}',
      rewrite,
    );
  }

  checkTitle() {
    let {defaultTitle} = this;
    let {watchedTitle} = this;

    if (this.useMultiLang) {
      watchedTitle = watchedTitle.closest(this.multiLangSelector).find('input');
      defaultTitle = defaultTitle.closest(this.multiLangSelector).find('input');
    }

    const title1 = watchedTitle.length ? watchedTitle.val() : '';
    const title2 = defaultTitle.length ? defaultTitle.val() : '';

    this.setTitle(title1 === '' ? title2 : title1);
    // Always check for url if title change
    this.checkUrl();
    this.updateComponent();
  }

  checkDesc() {
    let {watchedDescription} = this;
    let {defaultDescription} = this;

    if (this.useMultiLang) {
      watchedDescription = watchedDescription
        .closest(this.multiLangSelector)
        .find(this.watchedDescription.is('input') ? 'input' : 'textarea');
      defaultDescription = defaultDescription
        .closest(this.multiLangSelector)
        .find(this.defaultDescription.is('input') ? 'input' : 'textarea');
    }

    const desc1 = watchedDescription.length ? watchedDescription.val().innerText || watchedDescription.val() : '';
    const desc2 = defaultDescription.length ? defaultDescription.text() : '';

    this.setDescription(desc1 === '' ? desc2 : desc1);
    this.updateComponent();
  }

  checkUrl() {
    let {watchedMetaUrl} = this;

    if (this.useMultiLang) {
      watchedMetaUrl = watchedMetaUrl.closest(this.multiLangSelector).find('input');
    }

    this.setUrl(watchedMetaUrl.val());
    this.updateComponent();
  }
}

export default SerpApp;
