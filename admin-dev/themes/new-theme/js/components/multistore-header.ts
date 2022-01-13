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

// @ts-ignore-next-line
import Bloodhound from 'typeahead.js';
import Router from '@components/router';
import AutoCompleteSearch from '@components/auto-complete-search';
import PerfectScrollbar from 'perfect-scrollbar';
import ComponentsMap from '@components/components-map';
import ContextualNotification from '@components/contextual-notification';
import 'perfect-scrollbar/css/perfect-scrollbar.css';

const {$} = window;

const initMultistoreHeader = () => {
  const MultistoreHeaderMap = ComponentsMap.multistoreHeader;
  const headerButton = document.querySelector(MultistoreHeaderMap.headerButton);
  const modalMultishop = document.querySelector(MultistoreHeaderMap.modal);
  const $searchInput = $(MultistoreHeaderMap.searchInput);
  const router = new Router();
  const route = router.generate('admin_shops_search', {
    searchTerm: '__QUERY__',
  });

  new PerfectScrollbar(MultistoreHeaderMap.jsScrollbar);

  const source = new Bloodhound({
    datumTokenizer: Bloodhound.tokenizers.obj.whitespace,
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    remote: {
      url: route,
      wildcard: '__QUERY__',
    },
  });

  const dataSetConfig = {
    source,
    onSelect(selectedItem: any) {
      const contextUrlLetter = typeof selectedItem.groupName !== 'undefined' ? 's' : 'g';
      const setContextUrl = MultistoreHeaderMap.setContextUrl(
        window.location.href,
        contextUrlLetter,
        selectedItem.id,
      );
      window.location.href = setContextUrl;

      return true;
    },
  };

  new AutoCompleteSearch($searchInput, dataSetConfig);

  if (headerButton && modalMultishop) {
    headerButton.addEventListener('click', () => {
      modalMultishop.classList.toggle('multishop-modal-hidden');
      headerButton.classList.toggle('active');
    });
  }
};

const initContextualNotification = () => {
  const configKeyShopPrefix = 'missing-color-shop-';
  const configKeyGroupPrefix = 'missing-color-group-';
  const multistoreHeader = document.querySelector('.header-multishop');

  if (multistoreHeader === null
    || !(multistoreHeader instanceof HTMLElement)
    || !multistoreHeader.dataset.headerColorNotification) {
    return;
  }

  const notificationMessage = multistoreHeader.dataset.headerColorNotification;

  if (notificationMessage.length <= 0) {
    return;
  }

  // make localstorage key for this context
  const contextualNotification = new ContextualNotification();
  let notificationKey = configKeyGroupPrefix + multistoreHeader.dataset.groupId;

  if (multistoreHeader.hasAttribute('data-shop-id')) {
    notificationKey = configKeyShopPrefix + multistoreHeader.dataset.shopId;
  }

  // check if key exists, if yes: display or not depending on given value
  const configValue = contextualNotification.getItem(notificationKey);

  if (configValue === true || configValue === null) {
    contextualNotification.displayNotification(multistoreHeader.dataset.headerColorNotification, notificationKey);
  }

  // if the config doesn't exist, we set it to true
  if (configValue === null) {
    contextualNotification.setItem(notificationKey, true);
  }
};

$(() => {
  initMultistoreHeader();
  initContextualNotification();
});
