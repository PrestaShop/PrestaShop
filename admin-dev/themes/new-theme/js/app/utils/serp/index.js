/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
import Vue from 'vue';
import serp from './serp';
const $ = window.$;

/**
 * Vue component displaying a search page result, Google style.
 * Requires a tag with the id "#serp-app" to be present in the DOM to run it.
 * The component is automatically updated by watching several inputs.
 * Set the proper class to link a input to a part of the panel.
 */
class SerpApp {
  constructor() {
    // If the selector cannot be found, we do not load the Vue app
    if (0 === $('#serp-app').length) {
      return;
    }

    this.defaultTitle = $('.serp-default-title:input');
    this.watchedTitle = $('.serp-watched-title:input');
    this.defaultDescription = $('.serp-default-description');
    this.watchedDescription = $('.serp-watched-description');
    this.defaultUrl = $('.serp-default-url:input');

    this.vm = new Vue({
      el: '#serp-app',
      template: '<serp ref="serp" />',
      components: { serp },
    });

    this.attachEvents(this.vm.$refs.serp);
  }
    
  attachEvents(app) {
    // Specific rules for updating the search result preview
    const updateSerpTitle = () => {
      const title1 = this.watchedTitle.length ? this.watchedTitle.val() : '';
      const title2 = this.defaultTitle.length ? this.defaultTitle.val() : '';
      app.setTitle(title1 || title2);
    };
    const updateSerpUrl = () => {
      if (this.defaultUrl.length) {
        app.setUrl(this.defaultUrl.val());
      }
    };
    const updateSerpDescription = () => {
      const desc1 = this.watchedDescription.length ? this.watchedDescription.val().innerText || this.watchedDescription.val() : '';
      const desc2 = this.defaultDescription.length ? this.defaultDescription.val().innerText || this.defaultDescription.val() : '';
      app.setDescription(desc1 || desc2);
    };
    this.watchedTitle.on('keyup change', updateSerpTitle);
    this.defaultTitle.on('keyup change', updateSerpTitle);

    this.watchedDescription.on('keyup change', updateSerpDescription);
    this.defaultDescription.on('keyup change', updateSerpDescription);

    updateSerpTitle();
    updateSerpUrl();
    updateSerpDescription();
  }

  /**
   * @returns {boolean}
   */
  isActive() {
    return (undefined !== this.vm);
  }
}

export default SerpApp;
