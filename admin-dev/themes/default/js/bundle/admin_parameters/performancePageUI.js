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
const PerformancePageUI = {
  displaySmartyCache() {
    const CACHE_ENABLED = '1';
    const smartyCacheSelected = document.querySelector('input[name="smarty[cache]"]:checked');
    const smartyCacheOptions = document.querySelectorAll('.smarty-cache-option');

    if (smartyCacheSelected && smartyCacheSelected.value === CACHE_ENABLED) {
      for (var i = 0; i < smartyCacheOptions.length; i++) {
        smartyCacheOptions[i].classList.remove('d-none');
      }

      return;
    }

    for (var i = 0; i < smartyCacheOptions.length; i++) {
      smartyCacheOptions[i].classList.add('d-none');
    }
  },
  displayCacheSystems() {
    const CACHE_ENABLED = '1';
    const cacheEnabledInput = document.querySelector('input[name="caching[use_cache]"]:checked');
    const cachingElements = document.getElementsByClassName('memcache');

    if (cacheEnabledInput.value === CACHE_ENABLED) {
      for (var i = 0; i < cachingElements.length; i++) {
        cachingElements[i].style.display = '';
      }

      return;
    }

    for (var i = 0; i < cachingElements.length; i++) {
      cachingElements[i].style.display = 'none';
    }
  },
  displayMemcacheServers() {
    const CACHE_ENABLED = '1';
    const cacheEnabledInput = document.querySelector('input[name="caching[use_cache]"]:checked');
    const cacheSelected = document.querySelector('input[name="caching[caching_system]"]:checked');
    const memcacheServersListBlock = document.getElementById('servers-list');
    const newServerBtn = document.getElementById('new-server-btn');
    const isMemcache = cacheSelected && (cacheSelected.value === 'CacheMemcache' || cacheSelected.value === 'CacheMemcached');

    if (isMemcache && cacheEnabledInput.value === CACHE_ENABLED) {
      memcacheServersListBlock.style.display = 'block';
      newServerBtn.style.display = 'block';

      return;
    }

    memcacheServersListBlock.style.display = 'none';
    newServerBtn.style.display = 'none';
  },
};

/**
 * Animations on form values.
 */
window.addEventListener('load', () => {
  PerformancePageUI.displaySmartyCache();
  PerformancePageUI.displayCacheSystems();
  PerformancePageUI.displayMemcacheServers();
});

const cacheSystemInputs = document.querySelectorAll('input[type=radio]');
let {length} = cacheSystemInputs;

while (length--) {
  cacheSystemInputs[length].addEventListener('change', (e) => {
    const name = e.target.getAttribute('name');

    if (name === 'caching[use_cache]') {
      return PerformancePageUI.displayCacheSystems();
    }
    if (name === 'smarty[cache]') {
      return PerformancePageUI.displaySmartyCache();
    }
    if (name === 'caching[caching_system]') {
      return PerformancePageUI.displayMemcacheServers();
    }
  });
}
