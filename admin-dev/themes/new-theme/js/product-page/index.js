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
import productHeader from './product-header';
import productSearchAutocomplete from './product-search-autocomplete';
import categoryTree from './category-tree';
import attributes from './attributes';
import bulkCombination from './product-bulk-combinations';
import nestedCategory from './nested-categories';
import combination from './combination';
import Serp from '../app/utils/serp/index';

const $ = window.$;

$(() => {
  productHeader();
  productSearchAutocomplete();
  categoryTree();
  attributes();
  combination();
  bulkCombination().init();
  nestedCategory().init();

  const serpApp = new Serp(
    '#serp-app',
    $('#product_form_preview_btn').data('redirect')
  );

  const defaultTitle = $('.serp-default-title:input');
  const watchedTitle = $('.serp-watched-title:input');
  const defaultDescription = $('.serp-default-description');
  const watchedDescription = $('.serp-watched-description');
  const watchedMetaUrl = $('.serp-watched-url:input');

  const checkTitle = () => {
    const title1 = watchedTitle.length ? watchedTitle.val() : '';
    const title2 = defaultTitle.length ? defaultTitle.val() : '';

    serpApp.setTitle(title1 === '' ? title2 : title1);
  };
  const checkDesc = () => {
    const desc1 = watchedDescription.length ? watchedDescription.val().innerText || watchedDescription.val() : '';
    const desc2 = defaultDescription.length ? $(defaultDescription.val()).text() || defaultDescription.val() : '';
    serpApp.setDescription(desc1 === '' ? desc2 : desc1);
  };
  const checkUrl = () => {
    serpApp.setUrl(watchedMetaUrl.val());
  };

  $(watchedTitle, defaultTitle).on('keyup change', checkTitle);
  $(watchedDescription, defaultDescription).on('keyup change', checkDesc);
  watchedMetaUrl.on('keyup change', checkUrl);

  checkTitle();
  checkDesc();
  checkUrl();

  // This is the only script for the module page so there is no specific file for it.
  $('.modules-list-select').on('change', (e) => {
    $('.module-render-container').hide();
    $(`.${e.target.value}`).show();
  });

  $('.modules-list-button').on('click', (e) => {
    const target = $(e.target).data('target');
    $('.module-selection').show();
    $('.modules-list-select').val(target).trigger('change');
    return false;
  });
});
