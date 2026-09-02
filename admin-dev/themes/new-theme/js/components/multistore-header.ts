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
};

$(() => {
  initMultistoreHeader();
  initContextualNotification('header-color');
});
