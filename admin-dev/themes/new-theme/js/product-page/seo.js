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

import serpComponent from '../components/serp-component.js';

/**
 * SEO Tab
 */
export default function() {
  var productName = $('#form_step1_name_1');
  var productMetaTitle = $('#form_step5_meta_title_1');
  var productDescription = $('#form_step1_description_1');
  var productMetaDescription = $('#form_step5_meta_description_1');
  var productUrl = $('#form_step5_meta_title_1');
  
  return {
    'init': function() {
        
        const serp = new serpComponent();
  
        // Specific rules for updating the search result preview
        function updateSerpTitle() {
            serp.app.setTitle(productMetaTitle.val() || productName.val());
        }
        function updateSerpUrl() {
            serp.app.setUrl(productUrl.val());
        }
        function updateSerpDescription() {
            serp.app.setDescription(productMetaDescription.val() || $(productDescription.val()).text());
        }
        productMetaTitle.on("keyup", updateSerpTitle);
        productName.on("keyup", updateSerpTitle);
        
        productUrl.on("keyup", updateSerpUrl);
        
        productMetaDescription.on("keyup", updateSerpDescription);
        productDescription.on("keyup", updateSerpDescription);
        
        updateSerpTitle();
        updateSerpUrl();
        updateSerpDescription();
    }
  };
}
