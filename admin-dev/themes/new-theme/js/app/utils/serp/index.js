/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
  constructor($selector, url) {
    // If the selector cannot be found, we do not load the Vue app
    if ($($selector).length === 0) {
      return;
    }

    this.data = {
      url,
      title: '',
      description: '',
    };

    this.vm = new Vue({
      el: $selector,
      template: '<serp ref="serp" :url="url" :title="title" :description="description" />',
      components: { serp },
      data: this.data,
    });
  }

  setTitle(title) {
    this.data.title = title;
  }

  setDescription(description) {
    this.data.description = description;
  }

  setUrl(url) {
    this.data.url = this.data.url.replace(
      this.originalUrl,
      url,
    );

    this.originalUrl = url;
  }
}

export default SerpApp;
