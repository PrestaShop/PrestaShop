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
  <div id="product-combinations-generate">
    <button
      class="btn btn-primary"
      @click.prevent.stop="showModal"
    >
      Open combinations generator
    </button>
    <modal
      v-if="isModalShown"
      :modal-title="'Generate combinations'"
      :confirmation="true"
      @close="closeModal"
    >
      <template #body>
        <div class="tags-input d-flex flex-wrap">
          <div class="tags-wrapper">
            <template v-for="(group, groupName) in selectedAttributes">
              <span
                class="tag"
                :key="selectedAttribute.id_combination"
                v-for="selectedAttribute in group"
              >{{ groupName }}: {{ selectedAttribute.name }}<i
                class="material-icons"
                @click.prevent.stop="changeSelected(selectedAttribute, {name: groupName})"
              >close</i></span>
            </template>
          </div>
          <input
            type="text"
            placeholder="Search some attributes..."
            class="form-control input attributes-search"
          >
        </div>

        <div class="product-combinations-modal-content">
          <div
            id="attributes-list"
            class="attributes-list-overflow"
          >
            <div class="attributes-content">
              <div
                class="combination attribute-group"
                v-for="combination of combinations"
                :key="combination.id_combination"
              >
                <a
                  class="combination-name attribute-group-name collapsed"
                  data-toggle="collapse"
                  :href="`#attribute-group-${combination.id_combination}`"
                >{{ combination.name }}</a>
                <div
                  class="combination-content attributes collapse"
                  :id="`attribute-group-${combination.id_combination}`"
                >
                  <label
                    v-for="item of combination.childs"
                    :class="['combination-item', isSelected(combination, item)]"
                    :for="`combination_${item.id_combination}`"
                    :key="item.id"
                  >
                    <input
                      type="checkbox"
                      :name="`combination_${item.id_combination}`"
                      :id="`combination_${item.id_combination}`"
                      @change="changeSelected(item, combination)"
                    >
                    <div class="combination-item-content">
                      <span
                        class="combination-item-color"
                        v-if="item.color"
                        :style="`background-color: ${item.color}`"
                      />
                      <span class="combination-item-name">{{ item.name }}</span>
                    </div>
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>
      </template>

      <template #footer-confirmation>
        <button
          type="button"
          class="btn btn-outline-secondary"
          @click.prevent.stop="closeModal"
          aria-label="Close modal"
        >
          Cancel
        </button>

        <button
          type="button"
          class="btn btn-primary"
          @click.prevent.stop="generateCombinations"
        >
          <span v-if="!loading">
            Generate
          </span>
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

<script>
  import CombinationsService from '@pages/product/services/combinations-service';
  import ProductMap from '@pages/product/product-map';
  import Modal from '@vue/components/Modal';
  import PerfectScrollbar from 'perfect-scrollbar';
  import Bloodhound from 'typeahead.js';
  import AutoCompleteSearch from '@components/auto-complete-search';

  const {$} = window;

  const CombinationsMap = ProductMap.combinations;

  export default {
    name: 'Generate',
    data() {
      return {
        combinations: [],
        selectedAttributes: {},
        service: new CombinationsService(this.productId),
        isModalShown: false,
        loading: false,
        scrollbar: null,
      };
    },
    props: {
      productId: {
        type: Number,
        required: true,
      },
    },
    components: {
      Modal,
    },
    computed: {},
    mounted() {
      this.initCombinations();
    },
    methods: {
      /**
       * This methods is used to initialize combinations definitions
       */
      async initCombinations() {
        try {
          this.combinations = await this.service.fetchAll();
          window.prestaShopUiKit.init();
        } catch (error) {
          window.$.growl.error({message: error});
        }
      },
      /**
       * Show the modal, and execute PerfectScrollBar and Typehead
       */
      showModal() {
        this.isModalShown = true;
        const that = this;

        // We need to use a setTimeout to add it at the end
        // of the callstack so the modal is already displayed
        // when this is getting executed
        setTimeout(() => {
          this.scrollbar = new PerfectScrollbar(CombinationsMap.scrollBar);
          const searchItems = [];

          this.combinations.forEach((combination) => {
            combination.childs.forEach((attribute) => {
              attribute.group_name = combination.name;
              searchItems.push(attribute);
            });
          });

          const $searchInput = $(CombinationsMap.searchInput);
          const source = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name', 'value', 'color', 'group_name'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            local: searchItems,
          });

          const dataSetConfig = {
            source,
            display: 'name',
            value: 'name',
            onSelect(selectedItem) {
              const groupName = {
                name: selectedItem.group_name,
              };

              delete selectedItem.color;

              that.changeSelected(selectedItem, groupName);
            },
            onClose() {
              $searchInput.val('');
              return true;
            },
          };

          dataSetConfig.templates = {
            suggestion: (item) => {
              let displaySuggestion = item;

              if (typeof dataSetConfig.display === 'function') {
                dataSetConfig.display(item);
              } else if (Object.prototype.hasOwnProperty.call(item, dataSetConfig.display)) {
                displaySuggestion = item[dataSetConfig.display];
              }

              return `<div class="px-2">${item.group_name}: ${displaySuggestion}</div>`;
            },
          };

          new AutoCompleteSearch($searchInput, dataSetConfig);
        }, 0);
      },
      /**
       * Handle modal closing
       */
      closeModal() {
        this.isModalShown = false;
      },
      /**
       * Used when the user clicks on the Generate button of the modal
       */
      async generateCombinations() {
        this.loading = true;
        this.loading = false;
      },
      /**
       * Remove the attribute if it's selected or add it
       *
       * @param {Object} Combination
       * @param {{name: string}} Combination
       */
      changeSelected(combination, group) {
        if (!this.selectedAttributes[group.name] || !this.selectedAttributes[group.name].includes(combination)) {
          if (!this.selectedAttributes[group.name]) {
            const newAttributeGroup = {};

            newAttributeGroup[group.name] = [];
            newAttributeGroup[group.name].push(combination);
            this.selectedAttributes = {...this.selectedAttributes, ...newAttributeGroup};
          } else {
            this.selectedAttributes[group.name].push(combination);
          }
        } else {
          // eslint-disable-next-line
          this.selectedAttributes[group.name] = this.selectedAttributes[group.name].filter((e) => e.id_combination !== combination.id_combination);
        }
      },
      isSelected(combination, attribut) {
        return this.selectedAttributes[combination.name]
          && this.selectedAttributes[combination.name].includes(attribut)
          ? 'selected'
          : 'unselected';
      },
    },
  };
</script>

<style lang="scss" type="text/scss">
@import "~@scss/config/_settings.scss";

.product-page #product-combinations-generate {
  .modal {
    .tags-input {
      margin-bottom: 1rem;
    }

    #attributes-list {
     max-height: 50vh;

     .combination {
       &:last-of-type {
        border-bottom: 1px solid $gray-300;
        margin-bottom: .1rem;
       }
     }

     .attributes {
      height: auto;
     }
    }

    .product-combinations-modal-content {
      position: relative;
      padding-bottom: .5rem;
    }
  }

  .combination {
    &-item {
      cursor: pointer;
      border-radius: 3px;
      margin-right: .5rem;

      &-content {
        display: flex;
        align-items: center;
        padding: .5rem;
      }

      &-color {
        margin-right: .5rem;
      }

      &.selected {
        border: 2px solid $primary;
      }

      input {
        display: none;
      }

      &-color {
        height: 15px;
        width: 15px;
        display: block;
        border-radius: 3px;
      }
    }
  }
}
</style>
