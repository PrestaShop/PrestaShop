/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
