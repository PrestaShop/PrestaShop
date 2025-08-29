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
import {createApp, App} from 'vue';
import {createI18n} from 'vue-i18n';
import ReplaceFormatter from '@PSVue/plugins/vue-i18n/replace-formatter';
import GroupedItemCollectionModal from '@PSVue/components/grouped-item-collection/GroupedItemCollectionModal.vue';
import GroupedItemCollectionMap from '@PSVue/components/grouped-item-collection/GroupedItemCollectionMap';
import {Item, ItemGroup} from '@PSVue/components/grouped-item-collection/types';

/**
 * Initialize a GroupedItemCollection, the provided selector should be an ID targeting the root widget.
 * This is the only function exported and the only endpoint you need to initialize the whole component.
 *
 * @param groupedItemRootSelector {String} CSS selector of the root div
 * @param fetchItemGroups {Function} Asynchronous function to fetch the list of groups, the returned data must respect the ItemGroup format
 * @param onItemsSelected {Function|null} Optional callback called when the items have been selected in the modal
 */
export default function initGroupedItemCollection(
  groupedItemRootSelector: string,
  fetchItemGroups: () => Promise<ItemGroup[]>,
  onItemsSelected: ((selectedItems: Item[]) => void)|null = null,
): App {
  initTagInputs(groupedItemRootSelector);

  // We always update the input tags automatically, but you can specify an additional callback
  // if you need to handle something more with selected items
  const onSelectCallback = function (selectedItems: Item[]): void {
    updateTagInputs(groupedItemRootSelector, selectedItems);
    if (onItemsSelected) {
      onItemsSelected(selectedItems);
    }
  };
  // We build the getSelectedCallback based on the group selector
  const getSelectedItemsCallback = function (): Item[] {
    return getSelectedItemsFromInputs(groupedItemRootSelector);
  };

  return initVueApp(groupedItemRootSelector, fetchItemGroups, getSelectedItemsCallback, onSelectCallback);
}

/**
 * @internal
 * Initialize the modal VueApp that allows selecting the attributes organized by groups.
 *
 * @param groupedItemRootSelector {String} CSS selector of the root div
 * @param fetchItemGroups {Function} Asynchronous function to fetch the list of groups, the returned data must respect the ItemGroup format
 * @param getSelectedItems {Function} Callback function that provides the list of selected items, so the modal Vue app can preselect them when it opens
 * @param onItemsSelected {Function|null} Optional callback called when the items have been selected in the modal * @param groupedItemRootSelector
 */
function initVueApp(
  groupedItemRootSelector: string,
  fetchItemGroups: () => Promise<ItemGroup[]>,
  getSelectedItems: () => Item[],
  onItemsSelected: (selectedItems: Item[]) => void,
): App {
  const groupItemRoot = document.querySelector<HTMLElement>(groupedItemRootSelector);
  const groupedItemCollection = groupItemRoot?.querySelector<HTMLElement>(GroupedItemCollectionMap.groupedItemCollection);
  const modalControl = groupItemRoot?.querySelector(GroupedItemCollectionMap.modalControl);
  const vueAppContainer = document.createElement('div');
  vueAppContainer.classList.add(GroupedItemCollectionMap.vueAppContainerClass);
  groupItemRoot?.appendChild(vueAppContainer);

  const translations = JSON.parse(<string>groupedItemCollection?.dataset?.translations);
  const i18n = createI18n({
    locale: 'en',
    formatter: new ReplaceFormatter(),
    messages: {en: translations},
  });

  const vueApp = createApp(GroupedItemCollectionModal, {
    i18n,
    modalControl,
    fetchItemGroups,
    getSelectedItems,
    onItemsSelected,
  }).use(i18n);
  vueApp.mount(vueAppContainer);

  return vueApp;
}

/**
 * @internal
 * Parses the form inputs to generate a list a selected items (the most important is the item ID, the group
 * details are not relevant).
 */
function getSelectedItemsFromInputs(groupedItemRootSelector: string): Item[] {
  const selectedItems: Item[] = [];

  const groupedItemRoot = document.querySelector<HTMLElement>(groupedItemRootSelector);

  if (groupedItemRoot) {
    const groupedItemCollection = groupedItemRoot.querySelectorAll<HTMLElement>(GroupedItemCollectionMap.groupedItemCollection);
    groupedItemCollection.forEach((groupCollection: HTMLElement) => {
      const groupIdValue = groupCollection.querySelector<HTMLInputElement>(GroupedItemCollectionMap.groupIdValue);
      const groupNameValue = groupCollection.querySelector<HTMLInputElement>(GroupedItemCollectionMap.groupNameValue);

      if (groupIdValue && groupNameValue) {
        const tagItems = groupCollection.querySelectorAll<HTMLElement>(GroupedItemCollectionMap.tagItem);
        tagItems.forEach((tagContainer: HTMLElement) => {
          const tagIdValue = tagContainer.querySelector<HTMLInputElement>(GroupedItemCollectionMap.tagIdValue);
          const tagNameValue = tagContainer.querySelector<HTMLInputElement>(GroupedItemCollectionMap.tagNameValue);

          if (tagNameValue && tagIdValue) {
            selectedItems.push({
              id: parseInt(tagIdValue.value, 10),
              name: tagNameValue.value,
              selected: true,
              groupId: parseInt(groupIdValue.value, 10),
              groupName: groupNameValue.value,
              color: '',
              texture: '',
            });
          }
        });
      }
    });
  }

  return selectedItems;
}

/**
 * @internal
 * Handles click events on the tags removal buttons. When all tags have been removed from a tags container input,
 * the whole related group form group is removed automatically.
 */
function initTagInputs(groupedItemRootSelector: string): void {
  const groupedItemRoot = document.querySelector<HTMLElement>(groupedItemRootSelector);

  if (groupedItemRoot) {
    groupedItemRoot.querySelectorAll(GroupedItemCollectionMap.tagRemoveButtons).forEach((tagRemoveButton) => {
      tagRemoveButton.addEventListener('click', () => {
        const tagItem = tagRemoveButton.closest<HTMLElement>(GroupedItemCollectionMap.tagItem);

        if (tagItem) {
          const collection = tagItem.closest<HTMLElement>(GroupedItemCollectionMap.tagCollection);
          tagItem.parentNode?.removeChild(tagItem);

          if (collection) {
            const remainingTagItems = collection.querySelectorAll(GroupedItemCollectionMap.tagItem);

            // If all tags have been removed the collection form row is removed (meaning the whole group is removed)
            if (!remainingTagItems.length) {
              const groupRow = collection.closest<HTMLElement>(GroupedItemCollectionMap.groupedItemRow);

              if (groupRow) {
                groupRow.parentNode?.removeChild(groupRow);
              }
            }
          }
        }
      });
    });
  }
}

/**
 * @internal
 * Rebuilds the grouped item collection based on the selected items returned by the Vue app modal.
 * The building of the element uses the Symfony collection prototype feature.
 */
function updateTagInputs(
  groupedItemRootSelector: string,
  selectedItems: Item[],
): void {
  const groupedItemRoot = document.querySelector<HTMLElement>(groupedItemRootSelector);

  if (groupedItemRoot) {
    const groupedItemCollection = groupedItemRoot.querySelector<HTMLElement>(GroupedItemCollectionMap.groupedItemCollection);

    if (groupedItemCollection) {
      // First create all the group nodes, they are index by their group name
      const groupPrototype = groupedItemCollection.dataset.prototype;
      const groupPrototypeName = groupedItemCollection.dataset.prototypeName;

      if (groupPrototype && groupPrototypeName) {
        const groupHTMLElements: HTMLElement[] = [];
        let groupHTMLElement: HTMLElement;
        selectedItems.forEach((item: Item) => {
          // Get the group node or create it if not existent yet
          if (groupHTMLElements[item.groupId]) {
            groupHTMLElement = groupHTMLElements[item.groupId];
          } else {
            const groupTemplate = groupPrototype.replace(new RegExp(groupPrototypeName, 'g'), item.groupId.toString());
            // Trim is important here or the first child could be some text (whitespace, or \n)
            const fragment = document.createRange().createContextualFragment(groupTemplate.trim());
            groupHTMLElement = fragment.firstChild as HTMLElement;

            // Fill the new group withs values
            const groupIdValue = groupHTMLElement.querySelector<HTMLInputElement>(GroupedItemCollectionMap.groupIdValue);
            const groupNameValue = groupHTMLElement.querySelector<HTMLInputElement>(GroupedItemCollectionMap.groupNameValue);
            const groupSpanPreview = groupHTMLElement.querySelector<HTMLElement>(GroupedItemCollectionMap.groupPreview);

            if (groupIdValue && groupNameValue && groupSpanPreview) {
              groupIdValue.value = item.groupId.toString();
              groupNameValue.value = item.groupName;
              groupSpanPreview.innerText = item.groupName;

              // Finally add the group and store it for following selected items
              groupedItemRoot.appendChild(groupHTMLElement);
              groupHTMLElements[item.groupId] = groupHTMLElement;
            }
          }
        });

        // Remove all existing groups and replace them with new one
        while (groupedItemCollection.firstChild) {
          groupedItemCollection.removeChild(groupedItemCollection.firstChild);
        }
        groupHTMLElements.forEach((child: HTMLElement) => {
          groupedItemCollection.appendChild(child);
        });

        // Now add all the tags in their respective group container
        const groupIndexes: number[] = [];
        selectedItems.forEach((item: Item) => {
          groupHTMLElement = groupHTMLElements[item.groupId];
          const tagCollection = groupHTMLElement.querySelector<HTMLElement>(GroupedItemCollectionMap.tagCollection);

          if (tagCollection) {
            const tagPrototype = tagCollection.dataset.prototype;
            const tagPrototypeName = tagCollection.dataset.prototypeName;

            if (tagPrototype && tagPrototypeName) {
              // The array index is built for each group starting from 0
              if (typeof groupIndexes[item.groupId] === 'undefined') {
                groupIndexes[item.groupId] = 0;
              } else {
                groupIndexes[item.groupId] += 1;
              }
              const groupIndex = groupIndexes[item.groupId];
              const tagTemplate = tagPrototype
                .replace(new RegExp(tagPrototypeName, 'g'), groupIndex.toString())
                .replace(new RegExp(groupPrototypeName, 'g'), item.groupId.toString());
              // Trim is important here or the first child could be some text (whitespace, or \n)
              const fragment = document.createRange().createContextualFragment(tagTemplate.trim());
              const tagHTMLElement = fragment.firstChild as HTMLElement;

              // The tag label must be integrated in the internal preview span
              const tagSpanPreview = tagHTMLElement.querySelector<HTMLElement>(GroupedItemCollectionMap.tagPreview);
              const tagIdValue = tagHTMLElement.querySelector<HTMLInputElement>(GroupedItemCollectionMap.tagIdValue);
              const tagNameValue = tagHTMLElement.querySelector<HTMLInputElement>(GroupedItemCollectionMap.tagNameValue);

              if (tagSpanPreview && tagIdValue && tagNameValue) {
                tagIdValue.value = item.id.toString();
                tagNameValue.value = item.name;
                tagSpanPreview.innerText = item.name;
                tagCollection.appendChild(tagHTMLElement);
              }
            }
          }
        });

        initTagInputs(groupedItemRootSelector);
      }
    }
  }
}
