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

// Mimic Symfony debug toolbar getPreference function to get the toolbar state
const profilerStorageKey = 'symfony/profiler/';
const getPreference = (name: string): string | null => {
  if (!window.localStorage) {
    return null;
  }

  return localStorage.getItem(profilerStorageKey + name);
};

const refreshDelay = 100;
const waitForDebugContent = (debugToken: string): void => {
  // Wait until the toolbar content is present on page
  const debugBarContentId = `sfToolbarMainContent-${debugToken}`;

  const toolbar = document.getElementById(debugBarContentId);

  if (toolbar) {
    initToggleWatching(debugToken);
  } else {
    setTimeout(() => waitForDebugContent(debugToken), refreshDelay);
  }
};

const initToggleWatching = (debugToken: string): void => {
  document.getElementById(`sfToolbarMiniToggler-${debugToken}`)?.addEventListener('click', toggleDebugMode);
  document.getElementById(`sfToolbarHideButton-${debugToken}`)?.addEventListener('click', toggleDebugMode);
  toggleDebugMode();
};

const toggleDebugMode = (): void => {
  if (getPreference('toolbar/displayState') === 'none') {
    document.body.classList.add('debug-toolbar-hidden');
    document.body.classList.remove('debug-toolbar-shown');
  } else {
    // Alternative is block (set as shown) or null (default setting is shown)
    document.body.classList.add('debug-toolbar-shown');
    document.body.classList.remove('debug-toolbar-hidden');
  }
};

const watchSymfonyDebugBar = (): void => {
  const debugToolbar = document.querySelector<HTMLElement>('[id^=sfwdt]');

  if (!debugToolbar) {
    // If initial container is not present the debug toolbar will never be displayed, so nothing to do
    return;
  }

  const debugToken = debugToolbar.id.replace(/^sfwdt/, '');
  waitForDebugContent(debugToken);
};

export default watchSymfonyDebugBar;
