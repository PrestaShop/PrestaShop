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
  <div class="grouped-item-collection-modal">
    <modal
      v-if="isModalShown"
      :modal-title="$t('modal.title')"
      :confirmation="true"
      :close-on-click-outside="false"
      @close="closeModal"
    >
      <template #body>
        <grouped-items-selector
          :item-groups="itemGroups"
          v-if="itemGroups"
        />
      </template>

      <template #footer-confirmation>
        <button
          type="button"
          class="btn btn-outline-secondary"
          @click.prevent.stop="closeModal"
          :aria-label="$t('modal.close')"
        >
          {{ $t('modal.close') }}
        </button>

        <button
          type="button"
          class="btn btn-primary"
          @click.prevent.stop="selectItems"
          :disabled="!selectedItemsNb || loading"
        >
          <span v-if="!loading">
            {{
              $t('select.action', {
                'selectedItemsNb': selectedItemsNb,
              })
            }}
          </span>
          <span v-if="loading">{{ $t('modal.loading') }}</span>
          <span
            class="spinner-border spinner-border-sm"
            v-if="loading"
            role="status"
            aria-hidden="true"
          />
        </button>
      </template>
    </modal>
  </div>
</template>

<script lang="ts">
  import GroupedItemsSelector from '@PSVue/components/grouped-item-collection/GroupedItemsSelector.vue';
  import Modal from '@PSVue/components/Modal.vue';
  import {defineComponent} from 'vue';
  import {GroupedItemCollectionModalStates, Item, ItemGroup} from '@PSVue/components/grouped-item-collection/types';

  export default defineComponent({
    name: 'GroupedItemCollectionModal',
    data(): GroupedItemCollectionModalStates {
      return {
        itemGroups: [],
        isModalShown: false,
        loading: false,
      };
    },
    props: {
      modalControl: {
        type: HTMLElement,
        required: true,
      },
      fetchItemGroups: {
        type: Function,
        required: true,
      },
      onItemsSelected: {
        type: Function,
        required: true,
      },
      getSelectedItems: {
        type: Function,
        required: true,
      },
    },
    components: {
      Modal,
      GroupedItemsSelector,
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
      selectedItemsNb(): number {
        return this.selectedItems.length;
      },
    },
    mounted() {
      window.prestaShopUiKit.init();
      this.modalControl.addEventListener('click', () => {
        this.showModal();
      });
    },
    methods: {
      /**
       * Show the modal, and execute PerfectScrollBar and Typehead
       */
      async showModal(): Promise<void> {
        document.querySelector('body')?.classList.add('overflow-hidden');
        this.isModalShown = true;

        // First display, we load the items
        if (!this.itemGroups.length && !this.loading) {
          this.loading = true;

          try {
            this.itemGroups = await this.fetchItemGroups();
          } catch (error) {
            window.$.growl.error({message: error});
          }
          this.loading = false;
        }

        // Update selected items
        const selectedItems: Item[] = this.getSelectedItems();

        if (selectedItems && selectedItems.length) {
          selectedItems.forEach((selectedItem: Item) => {
            this.itemGroups.forEach((itemGroup: ItemGroup) => {
              itemGroup.items.forEach((item: Item) => {
                if (selectedItem.id === item.id) {
                  item.selected = true;
                }
              });
            });
          });
        }
      },
      /**
       * Handle modal closing
       */
      closeModal(): void {
        this.isModalShown = false;
        document.querySelector('body')?.classList.remove('overflow-hidden');
      },
      unselectAll(): void {
        this.itemGroups.forEach((itemGroup: ItemGroup) => {
          itemGroup.items.forEach((item: Item) => {
            item.selected = false;
          });
        });
      },
      /**
       * Used when the user clicks on the Generate button of the modal
       */
      async selectItems(): Promise<void> {
        this.onItemsSelected(this.selectedItems);
        this.unselectAll();
        this.closeModal();
      },
    },
  });
</script>
