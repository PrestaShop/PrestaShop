/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
