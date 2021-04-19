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
            <template v-for="(attributes, groupName) in selectedAttributes">
              <span
                class="tag"
                :key="selectedAttribute.id"
                v-for="selectedAttribute in attributes"
              >{{ groupName }}: {{ selectedAttribute.name
              }}<i
                class="material-icons"
                @click.prevent.stop="
                  changeSelected(selectedAttribute, { name: groupName })
                "
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
                class="attribute-group"
                v-for="attributeGroup of attributeGroups"
                :key="attributeGroup.id"
              >
                <a
                  class="attribute-group-name collapsed"
                  data-toggle="collapse"
                  :href="`#attribute-group-${attributeGroup.id}`"
                >{{ attributeGroup.name }}</a>
                <div
                  class="attribute-group-content attributes collapse"
                  :id="`attribute-group-${attributeGroup.id}`"
                >
                  <label
                    v-for="attribute of attributeGroup.attributes"
                    :class="['attribute-item', isSelected(attributeGroup, attribute)]"
                    :for="`attribute_${attribute.id}`"
                    :key="attribute.id"
                  >
                    <input
                      type="checkbox"
                      :name="`attribute_${attribute.id}`"
                      :id="`attribute_${attribute.id}`"
                      @change="changeSelected(attribute, attributeGroup)"
                    >
                    <div class="attribute-item-content">
                      <span
                        class="attribute-item-color"
                        v-if="attribute.color"
                        :style="`background-color: ${attribute.color}`"
                      />
                      <span class="attribute-item-name">{{ attribute.name }}</span>
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
  import {getAllAttributeGroups} from '@pages/product/services/attribute-groups';
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
        attributeGroups: [],
        selectedAttributes: {},
        combinationsService: new CombinationsService(this.productId),
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
      this.initAttributeGroups();
    },
    methods: {
      /**
       * This methods is used to initialize combinations definitions
       */
      async initAttributeGroups() {
        try {
          this.attributeGroups = await getAllAttributeGroups();
          window.prestaShopUiKit.init();
        } catch (error) {
          window.$.growl.error({message: error});
        }
      },
      /**
       * Show the modal, and execute PerfectScrollBar and Typehead
       */
      showModal() {
        document.querySelector('body').classList.add('overflow-hidden');
        this.isModalShown = true;
        const that = this;

        // We need to use a setTimeout to add it at the end
        // of the callstack so the modal is already displayed
        // when this is getting executed
        setTimeout(() => {
          this.scrollbar = new PerfectScrollbar(CombinationsMap.scrollBar);
          const searchItems = [];

          this.attributeGroups.forEach((attributeGroup) => {
            attributeGroup.attributes.forEach((attribute) => {
              attribute.group_name = attributeGroup.name;
              searchItems.push(attribute);
            });
          });

          const $searchInput = $(CombinationsMap.searchInput);
          const source = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace(
              'name',
              'value',
              'color',
              'group_name',
            ),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            local: searchItems,
          });

          const dataSetConfig = {
            source,
            display: 'name',
            value: 'name',
            minLength: 1,
            onSelect(attribute) {
              const attributeGroup = {
                name: attribute.group_name,
              };

              that.changeSelected(attribute, attributeGroup);
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
              } else if (
                Object.prototype.hasOwnProperty.call(item, dataSetConfig.display)
              ) {
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
        document.querySelector('body').classList.remove('overflow-hidden');
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
       * @param {Object} selectedAttribute
       * @param {{name: string}} attributeGroup
       */
      changeSelected(selectedAttribute, attributeGroup) {
        const groupName = attributeGroup.name;

        if (
          !this.selectedAttributes[groupName]
          || !this.selectedAttributes[groupName].includes(selectedAttribute)
        ) {
          if (!this.selectedAttributes[groupName]) {
            const newAttributeGroup = {};

            newAttributeGroup[groupName] = [];
            newAttributeGroup[groupName].push(selectedAttribute);
            this.selectedAttributes = {
              ...this.selectedAttributes,
              ...newAttributeGroup,
            };
          } else {
            this.selectedAttributes[groupName].push(selectedAttribute);
          }
        } else {
          // eslint-disable-next-line
        this.selectedAttributes[groupName] = this.selectedAttributes[
            groupName
          ].filter((attribute) => attribute.id_attribute !== selectedAttribute.id_attribute);
        }
      },
      isSelected(attributeGroup, attribute) {
        return this.selectedAttributes[attributeGroup.name]
          && this.selectedAttributes[attributeGroup.name].includes(attribute)
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

      .attribute-group {
        border-bottom: 1px solid $gray-300;
        margin-bottom: 0.75rem;
        border-radius: 4px;
        overflow: hidden;

        &-name {
          background-color: $gray-250;
        }
      }

      .attributes {
        height: auto;
      }
    }

    .product-combinations-modal-content {
      position: relative;
      padding-bottom: 0.5rem;
    }
  }

  .attribute-group {
    &-content {
      border-top: 1px solid $gray-300;
    }

    .attribute-item {
      cursor: pointer;
      border-radius: 3px;
      margin: 0.25rem 0;
      margin-right: 0.5rem;

      &-content {
        display: flex;
        align-items: center;
        padding: 0.5rem;
      }

      &-color {
        margin-right: 0.5rem;
      }

      &.selected {
        background-color: $gray-disabled;
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
