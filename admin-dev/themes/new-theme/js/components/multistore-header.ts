/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

// @ts-ignore-next-line
import Bloodhound from 'typeahead.js';
import Router from '@components/router';
import AutoCompleteSearch from '@components/auto-complete-search';
import PerfectScrollbar from 'perfect-scrollbar';
import ComponentsMap from '@components/components-map';
import initContextualNotification from '@components/contextual-notification';
import {ConfirmModal} from '@components/modal';
import 'perfect-scrollbar/css/perfect-scrollbar.css';

const {$} = window;

const initMultistoreHeader = () => {
  const MultistoreHeaderMap = ComponentsMap.multistoreHeader;
  const headerButton = document.querySelector(MultistoreHeaderMap.headerButton);
  const modalMultishop = document.querySelector(MultistoreHeaderMap.modal);
  const modalMultishopDialog = document.querySelector(MultistoreHeaderMap.modalDialog);
  const $searchInput = $(MultistoreHeaderMap.searchInput);
  const router = new Router();
  const route = router.generate('admin_shops_search', {
    searchTerm: '__QUERY__',
  });

  const header = document.querySelector<HTMLElement>(MultistoreHeaderMap.headerMultiShop);

  new PerfectScrollbar(MultistoreHeaderMap.jsScrollbar);

  /**
   * Switching context reloads the page, so anything typed into a form on it is lost. Track whether
   * that has happened and let the merchant confirm first.
   *
   * Any edit counts, including one the merchant undoes by hand: comparing the form's content against
   * its initial state was considered and dropped as overkill in the discussion on this feature, since
   * the only cost of a false positive is one extra confirmation.
   */
  let formModified = false;

  /**
   * Controls that carry no unsaved state, so typing in one is not a page modification: the store
   * picker's own search field, the back office search bar, and the filters of both the migrated and
   * the legacy lists, which are kept in the session and are still there after the context switch.
   */
  const transientControlSelector = [
    MultistoreHeaderMap.headerMultiShop,
    '[role="search"]',
    '.js-grid-panel',
    'tr.filter',
  ].join(', ');

  const markFormModified = (event: Event): void => {
    const target = event.target as HTMLElement | null;

    if (!target || !target.closest('form') || target.closest(transientControlSelector)) {
      return;
    }

    formModified = true;
  };

  document.addEventListener('input', markFormModified, true);
  document.addEventListener('change', markFormModified, true);

  const switchContext = (url: string): void => {
    if (!formModified) {
      window.location.href = url;

      return;
    }

    new ConfirmModal(
      {
        id: 'multistore-switch-context-modal',
        confirmTitle: header?.dataset.switchContextTitle,
        confirmMessage: header?.dataset.switchContextMessage,
        confirmButtonLabel: header?.dataset.switchContextConfirm,
        closeButtonLabel: header?.dataset.switchContextCancel,
        confirmButtonClass: 'btn-primary',
      },
      () => {
        window.location.href = url;
      },
    ).show();
  };

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
      switchContext(setContextUrl);

      return true;
    },
  };

  new AutoCompleteSearch($searchInput, dataSetConfig);

  function toggleModal(): void {
    if (!headerButton || !modalMultishop) {
      return;
    }

    modalMultishop.classList.toggle('multishop-modal-hidden');
    headerButton.classList.toggle('active');
  }

  if (headerButton && modalMultishop && modalMultishopDialog) {
    headerButton.addEventListener('click', () => {
      toggleModal();
    });

    modalMultishop.addEventListener('click', (e: Event) => {
      if (e.target instanceof Node && !modalMultishopDialog.contains(e.target)) {
        toggleModal();
      }
    }, false);
  }

  /**
   * Header multishop links don't handle anchors which might be useful for tab navigation for example
   * so we synchronize them via javascript
   */
  function updateLinksAnchor(): void {
    function updateLinkAnchor(shopLink: HTMLLinkElement) {
      if (!shopLink.hasAttribute('href')) {
        return;
      }
      const updatedLink = shopLink.href.replace(/#(.*)$/, '') + window.location.hash;
      shopLink.setAttribute('href', updatedLink);
    }

    const shopLinks: NodeListOf<HTMLLinkElement> = document.querySelectorAll(MultistoreHeaderMap.shopLinks);
    shopLinks.forEach(updateLinkAnchor);

    const groupShopLinks: NodeListOf<HTMLLinkElement> = document.querySelectorAll(MultistoreHeaderMap.groupShopLinks);
    groupShopLinks.forEach(updateLinkAnchor);
  }

  updateLinksAnchor();
  window.addEventListener('hashchange', updateLinksAnchor);

  /**
   * The store, group and "All stores" entries are plain links, so the confirmation has to happen
   * before the browser follows them.
   */
  const contextLinks = [
    MultistoreHeaderMap.allShopsLink,
    MultistoreHeaderMap.groupShopLinks,
    MultistoreHeaderMap.shopLinks,
  ].join(', ');

  header?.addEventListener('click', (event: Event) => {
    const target = event.target as HTMLElement | null;
    const link = target?.closest<HTMLAnchorElement>(contextLinks);

    // A store with no configured URL is rendered without an href and switches nothing.
    if (!link || !link.getAttribute('href') || !formModified) {
      return;
    }

    event.preventDefault();
    // Close the store picker first, so the confirmation is not stacked on top of its overlay.
    toggleModal();
    switchContext(link.href);
  });
};

$(() => {
  initMultistoreHeader();
  initContextualNotification('header-color');
});
