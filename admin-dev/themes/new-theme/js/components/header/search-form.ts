/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

$(() => {
  const searchComponent = document.querySelector('.component-search');
  let windowWidth = window.innerWidth;
  let eventsAdded = false;
  const searchInput = searchComponent?.querySelector('.js-form-search');
  const cancelButton = searchComponent?.querySelector('.component-search-cancel');
  const quickAccess = searchComponent?.querySelector('.component-search-quickaccess');
  const background = searchComponent?.querySelector('.component-search-background');

  const closeQuickaccess = () => {
    searchComponent?.classList.remove('active');
    quickAccess?.classList.add('d-none');
    cancelButton?.classList.add('d-none');
    background?.classList.add('d-none');
  };

  const openQuickaccess = () => {
    if (windowWidth <= 768) {
      searchComponent?.classList.add('active');
      quickAccess?.classList.remove('d-none');
      cancelButton?.classList.remove('d-none');
      background?.classList.remove('d-none');
    }
  };

  const addQuickaccessEvent = () => {
    if (searchComponent) {
      searchInput?.addEventListener('focus', openQuickaccess);

      cancelButton?.addEventListener('click', closeQuickaccess);

      background?.addEventListener('click', closeQuickaccess);

      eventsAdded = true;
    }
  };

  window.addEventListener('resize', (e: Record<string, any>) => {
    windowWidth = e.target.outerWidth;

    if (windowWidth > 768) {
      closeQuickaccess();

      return;
    }

    if (eventsAdded) {
      return;
    }

    addQuickaccessEvent();
  });

  addQuickaccessEvent();
});
