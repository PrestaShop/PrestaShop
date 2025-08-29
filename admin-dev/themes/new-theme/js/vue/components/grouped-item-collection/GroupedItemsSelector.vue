<!--**
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
 *-->
<template>
  <div class="grouped-items-selector-component">
    <div class="tags-input d-flex flex-wrap">
      <div class="tags-wrapper">
        <span
          class="tag"
          :key="selectedItem.id"
          v-for="selectedItem in selectedItems"
        >
          {{ selectedItem.groupName }}: {{ selectedItem.name }}
          <i
            class="material-icons"
            @click.prevent.stop="unselectItem(selectedItem)"
          >close</i>
        </span>
      </div>
      <input
        type="text"
        :disabled="!searchableItemsNb"
        :placeholder="$t('search.placeholder')"
        class="form-control input items-search"
      >
    </div>

    <div class="item-groups-list-container">
      <div class="item-groups-list-overflow">
        <div class="item-groups-list">
          <div
            class="item-group"
            v-for="itemGroup of itemGroups"
            :key="itemGroup.id"
          >
            <div class="item-group-header">
              <a
                class="item-group-name collapsed"
                data-toggle="collapse"
                :href="`#item-group-${itemGroup.id}`"
              >
                <label>{{ itemGroup.name }}</label>
              </a>
              <div class="md-checkbox item-group-checkbox">
                <label>
                  <input
                    class="item-group-checkbox"
                    type="checkbox"
                    :name="`checkbox_${itemGroup.id}`"
                    @change.prevent.stop="toggleAllItems($event, itemGroup)"
                    :checked="allItemsSelected(itemGroup)"
                  >
                  <i class="md-checkbox-control" />
                  {{
                    $t('group.select-all', {
                      'valuesNb': itemGroup.items.length,
                    })
                  }}
                </label>
              </div>
            </div>
            <div
              class="item-group-content collapse"
              :id="`item-group-${itemGroup.id}`"
            >
              <label
                v-for="item of itemGroup.items"
                :class="[
                  'item',
                  getSelectedClass(item),
                ]"
                :for="`item_${item.id}`"
                :key="item.id"
              >
                <input
                  type="checkbox"
                  :name="`item_${item.id}`"
                  :id="`item_${item.id}`"
                  @change="toggleItemSelected(item)"
                >
                <div class="item-content">
                  <span
                    class="item-texture"
                    v-if="item.texture"
                    :style="`background: transparent url(${item.texture}) no-repeat; background-size: 100% auto;`"
                  />
                  <span
                    class="item-color"
                    v-else-if="item.color"
                    :style="`background-color: ${item.color}`"
                  />
                  <span class="item-name">{{ item.name }}</span>
                </div>
              </label>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
  import {defineComponent, PropType} from 'vue';
  import GroupedItemCollectionMap from '@PSVue/components/grouped-item-collection/GroupedItemCollectionMap';
  import PerfectScrollbar from 'perfect-scrollbar';
  // @ts-ignore
  import Bloodhound from 'typeahead.js';
  import AutoCompleteSearch, {AutoCompleteSearchConfig} from '@components/auto-complete-search';
  import Tokenizers from '@components/bloodhound/tokenizers';
  import {ItemGroup, Item, GroupedItemsSelectorStates} from '@PSVue/components/grouped-item-collection/types';

  const {$} = window;

  export default defineComponent({
    name: 'GroupedItemsSelector',
    props: {
      itemGroups: {
        type: Array as PropType<ItemGroup[]>,
        default: () => [],
      },
      selectedItemGroups: {
        type: Object as PropType<Record<string, ItemGroup>>,
        default: () => ({}),
      },
    },
    data(): GroupedItemsSelectorStates {
      return {
        dataSetConfig: {},
        searchSource: {},
        scrollbar: null,
        searchableItems: [],
      };
    },
    mounted() {
      this.initDataSetConfig();
      // Init scrollbar for the selection container (when everything is selected and open it all
      // needs to fit in the modal).
      this.scrollbar = new PerfectScrollbar(GroupedItemCollectionMap.scrollBarContainer);
      const $searchInput = $(GroupedItemCollectionMap.searchInput);
      new AutoCompleteSearch($searchInput, <Partial<AutoCompleteSearchConfig>> this.dataSetConfig);
    },
    computed: {
      selectedItems(): Item[] {
        const selectedItems: Item[] = [];
        this.itemGroups.forEach((itemGroup: ItemGroup) => {
          itemGroup.items.forEach((item: Item) => {
            if (item.selected) {
              selectedItems.push(item);
            }
          });
        });

        return selectedItems;
      },
      searchableItemsNb(): number {
        return this.searchableItems.length;
      },
    },
    watch: {
      /**
       * We use a watcher so that this list is always updated, when the itemGroups are modified,
       * including when the parent updates the selected status.
       */
      itemGroups() {
        this.updateSearchableItems();
      },
    },
    methods: {
      /**
       * Prepare the configuration for the AutoCompleteSearch component.
       */
      initDataSetConfig(): void {
        const letters = [
          'name',
          'value',
          'groupName',
        ];

        this.searchSource = new Bloodhound({
          datumTokenizer: Tokenizers.obj.letters(letters),
          queryTokenizer: Bloodhound.tokenizers.nonword,
          local: this.searchableItems,
        });

        this.dataSetConfig = {
          source: this.searchSource,
          display: (item: Item) => `${item.groupName}: ${item.name}`,
          value: 'name',
          minLength: 1,
          onSelect: (item: Item, e: JQueryEventObject, $searchInput: JQuery) => {
            this.selectItem(item);

            // This resets the search input or else previous search is cached and can be added again
            $searchInput.typeahead('val', '');

            return true;
          },
        };
      },
      getSelectedClass(item: Item): string {
        return item.selected ? 'selected' : 'unselected';
      },
      toggleItemSelected(selectedItem: Item): void {
        selectedItem.selected = !selectedItem.selected;
        this.updateSearchableItems();
      },
      selectItem(selectedItem: Item): void {
        selectedItem.selected = true;
        this.updateSearchableItems();
      },
      unselectItem(item: Item): void {
        item.selected = false;
        this.updateSearchableItems();
      },
      toggleAllItems(event: any, itemGroup: ItemGroup): void {
        const allSelected = event.target.checked;
        itemGroup.items.forEach((item: Item) => {
          item.selected = allSelected;
        });
        this.updateSearchableItems();
      },
      allItemsSelected(itemGroup: ItemGroup): boolean {
        let allSelected = true;
        itemGroup.items.forEach((item: Item) => {
          if (!item.selected) {
            allSelected = false;
          }
        });

        return allSelected;
      },
      /**
       * Update Bloodhound engine so that it does not include already selected items
       */
      updateSearchableItems(): void {
        // Create a list of searchable items, by filtering from the whole list the ones
        // that have been selected already, the Item are filled with their parent group
        // data which makes it more convenient to use the parent data
        const searchableItems: Item[] = [];
        this.itemGroups.forEach((itemGroup: ItemGroup) => {
          itemGroup.items.forEach((item: Item) => {
            if (this.selectedItems.includes(item)) {
              return;
            }

            searchableItems.push(item);
          });
        });
        this.searchableItems = searchableItems;

        // Update search input source
        this.searchSource.clear();
        this.searchSource.add(this.searchableItems);
      },
    },
  });
</script>

<style lang="scss" type="text/scss">
@import '~@scss/config/_settings.scss';

.grouped-items-selector-component {
  @import '~@scss/components/twitter_typeahead';

  .tags-input {
    margin-bottom: var(--#{$cdk}size-16);

    .tag {
      margin-bottom: var(--#{$cdk}size-4);
    }

    .tags-wrapper {
      max-height: var(--#{$cdk}size-208);
      overflow-y: auto;
    }
  }

  .item-groups-list-overflow {
    max-height: 50vh;

    .item-group {
      position: relative;
      margin-bottom: var(--#{$cdk}size-12);
      overflow: hidden;
      border: 1px solid var(--#{$cdk}primary-400);

      &-header {
        display: flex;
        background-color: var(--#{$cdk}primary-200);
      }

      &-content {
        border-top: 1px solid var(--#{$cdk}primary-300);
      }

      &-checkbox {
        width: fit-content;
        font-weight: 400;
        position: absolute;
        right: var(--#{$cdk}size-48);
        top: 9px;
      }

      label {
        margin-bottom: 0;
      }

      &-name {
        width: 100%;
        padding: var(--#{$cdk}size-8) var(--#{$cdk}size-40) var(--#{$cdk}size-8) var(--#{$cdk}size-16);
        font-weight: 600;
        color: var(--#{$cdk}primary-800);

        &:hover {
          text-decoration: none;
        }

        &::after {
          font-family: var(--#{$cdk}font-family-material-icons);
          font-size: var(--#{$cdk}size-24);
          content: 'expand_more';
          line-height: var(--#{$cdk}size-24);
          height: var(--#{$cdk}size-24);
          position: absolute;
          top: var(--#{$cdk}size-8);
          right: var(--#{$cdk}size-8);
        }

        &[aria-expanded="true"] {
          &::after {
            content: 'expand_less';
          }
        }
      }

      .item {
        margin: var(--#{$cdk}size-4);
        cursor: pointer;
        border-radius: var(--#{$cdk}size-4);

        &-content {
          display: flex;
          align-items: center;
          padding: var(--#{$cdk}size-8);
        }

        &.unselected {
          &:hover {
            background-color: var(--#{$cdk}primary-200);
          }
        }

        &.selected {
          background-color: var(--#{$cdk}primary-300);
        }

        input {
          display: none;
        }

        &-color {
          display: block;
          width: var(--#{$cdk}size-16);
          height: var(--#{$cdk}size-16);
          margin-right: var(--#{$cdk}size-8);
          border-radius: var(--#{$cdk}size-4);
          border: 1px solid var(--#{$cdk}primary-400);
        }

        &-texture {
          display: block;
          width: var(--#{$cdk}size-16);
          height: var(--#{$cdk}size-16);
          margin-right: var(--#{$cdk}size-8);
          border-radius: var(--#{$cdk}size-4);
          border: 1px solid var(--#{$cdk}primary-400);
        }
      }
    }

    .item-groups-list {
      height: auto;
      padding: var(--#{$cdk}size-8);
    }
  }

  .item-groups-list-container {
    position: relative;
    padding-bottom: var(--#{$cdk}size-8);
  }
}
</style>
