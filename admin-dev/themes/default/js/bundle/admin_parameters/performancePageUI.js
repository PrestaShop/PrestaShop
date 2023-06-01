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
    document.querySelectorAll('.smarty-cache-option').forEach((element) => {
      element.classList.toggle('d-none', smartyCacheSelected.value !== CACHE_ENABLED);
    });
  },
  displayDebugModeOptions() {
    const DEBUG_MODE_ON = '1';
    const debugModeOn = document.querySelector('input[name="debug_mode[debug_mode]"]:checked');
    document.querySelectorAll('.debug-mode-option').forEach((element) => {
      element.classList.toggle('d-none', debugModeOn.value !== DEBUG_MODE_ON);
    });
  },
  displayCacheSystems() {
    const CACHE_ENABLED = '1';
    const cacheEnabledInput = document.querySelector('input[name="caching[use_cache]"]:checked');
    const cachingElements = document.getElementsByClassName('memcache');

    if (cacheEnabledInput.value === CACHE_ENABLED) {
      for (let i = 0; i < cachingElements.length; i += 1) {
        cachingElements[i].style.display = '';
      }

      return;
    }

    for (let i = 0; i < cachingElements.length; i += 1) {
      cachingElements[i].style.display = 'none';
    }
  },
  displayMemcacheServers() {
    const CACHE_ENABLED = '1';
    const cacheEnabledInput = document.querySelector('input[name="caching[use_cache]"]:checked');
    const cacheSelected = document.querySelector('input[name="caching[caching_system]"]:checked');
    const memcacheServersListBlock = document.getElementById('servers-list');
    const newServerBtn = document.getElementById('new-server-btn');
    const isMemcache = cacheSelected
      && (cacheSelected.value === 'CacheMemcache' || cacheSelected.value === 'CacheMemcached');

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
  PerformancePageUI.displayDebugModeOptions();
  PerformancePageUI.displayCacheSystems();
  PerformancePageUI.displayMemcacheServers();
});

const cacheSystemInputs = document.querySelectorAll('input[type=radio]');
let {length} = cacheSystemInputs;

// eslint-disable-next-line
while (length--) {
  // eslint-disable-next-line
  cacheSystemInputs[length].addEventListener('change', (e) => {
    const name = e.target.getAttribute('name');

    if (name === 'caching[use_cache]') {
      return PerformancePageUI.displayCacheSystems();
    }
    if (name === 'smarty[cache]') {
      return PerformancePageUI.displaySmartyCache();
    }
    if (name === 'debug_mode[debug_mode]') {
      return PerformancePageUI.displayDebugModeOptions();
    }
    if (name === 'caching[caching_system]') {
      return PerformancePageUI.displayMemcacheServers();
    }
  });
}
