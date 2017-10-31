/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
var PerformancePageUI = {
    displaySmartyCache: function() {
        var CACHE_ENABLED = '1';
        var smartyCacheSelected = document.getElementById('form_smarty_cache');
        var smartyCacheOptions = document.querySelectorAll('.smarty-cache-option');
        if (smartyCacheSelected && smartyCacheSelected.value === CACHE_ENABLED) {
          for(var i = 0; i < smartyCacheOptions.length; i++) {
            smartyCacheOptions[i].style.display = 'block';
          }

          return;
        }

      for(var i = 0; i < smartyCacheOptions.length; i++) {
        smartyCacheOptions[i].style.display = 'none';
      }
    },
    displayCacheSystems: function() {
        var CACHE_ENABLED = '1';
        var cacheEnabledInput = document.getElementById('form_caching_use_cache');
        var cachingElements = document.getElementsByClassName('memcache');

        if(cacheEnabledInput.value === CACHE_ENABLED) {
            for (var i = 0; i < cachingElements.length; i++) {
                cachingElements[i].style.display = "block";
            }
            return;
        }

        for (var i = 0; i < cachingElements.length; i++) {
            cachingElements[i].style.display = "none";
        }
    },
    displayMemcacheServers: function() {
        var CACHE_ENABLED = '1';
        var cacheEnabledInput = document.getElementById('form_caching_use_cache');
        var cacheSelected = document.querySelector('input[name="form[caching][caching_system]"]:checked');
        var memcacheServersListBlock = document.getElementById('servers-list');
        var newServerBtn = document.getElementById('new-server-btn');
        var isMemcache = cacheSelected && (cacheSelected.value === "CacheMemcache" || cacheSelected.value === "CacheMemcached");

        if (isMemcache && cacheEnabledInput.value === CACHE_ENABLED) {
            memcacheServersListBlock.style.display = "block";
            newServerBtn.style.display = "block";

            return;
        }

        memcacheServersListBlock.style.display = "none";
        newServerBtn.style.display = "none";
    }
};

/**
 * Animations on form values.
 */
document.getElementById('form_caching_use_cache').addEventListener('change', function() {
    PerformancePageUI.displayCacheSystems();
});

window.addEventListener('load', function() {
    PerformancePageUI.displaySmartyCache();
    PerformancePageUI.displayCacheSystems();
    PerformancePageUI.displayMemcacheServers();
});

var cacheSystemInputs = document.querySelectorAll('input[type=radio]');
var length = cacheSystemInputs.length;

while(length--) {
    cacheSystemInputs[length].addEventListener('change', function() {
        PerformancePageUI.displayMemcacheServers();
    });
}

var smartyCacheOption = document.getElementById('form_smarty_cache');

smartyCacheOption.addEventListener('change', function() {
  PerformancePageUI.displaySmartyCache();
});
