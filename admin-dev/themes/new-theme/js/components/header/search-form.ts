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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
