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

export default function() {
  const defaultTitle = $('.serp-default-title');
  const watchedTitle = $('.serp-watched-title');
  const defaultDescription = $('.serp-default-description');
  const watchedDescription = $('.serp-watched-description');
  const defaultUrl = $('.serp-default-url');

  return {
    'init': function() {  
        const vm = new Vue({
          el: '#serp-app',
          template: '<serp ref="serp" />',
          components: { serp },
        });
        
        this.attachEvents(vm.$refs.serp);
        return vm;
    },
    
    'attachEvents': function(serp) {
        // Specific rules for updating the search result preview
        function updateSerpTitle() {
            serp.setTitle(watchedTitle.val() || defaultTitle.val());
        }
        function updateSerpUrl() {
            serp.setUrl(defaultUrl.val());
        }
        function updateSerpDescription() {
            serp.setDescription(watchedDescription.val() || $(defaultDescription.val()).text());
        }
        watchedTitle.on("keyup change", updateSerpTitle);
        defaultTitle.on("keyup change", updateSerpTitle);
        
        watchedDescription.on("keyup change", updateSerpDescription);
        defaultDescription.on("keyup change", updateSerpDescription);
        
        updateSerpTitle();
        updateSerpUrl();
        updateSerpDescription();
    }
  };
}
