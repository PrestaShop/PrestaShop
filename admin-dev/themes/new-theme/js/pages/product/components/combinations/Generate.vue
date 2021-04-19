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
      {{ $t('generator.open') }}
    </button>
    <modal
      v-if="isModalShown"
      :modal-title="$t('modal.title')"
      :confirmation="true"
      @close="closeModal"
    >
      <template #body>
        <div class="tags-input d-flex flex-wrap">
          <div class="tags-wrapper">
            <template v-for="selectedGroup in selectedAttributeGroups">
              <span
                class="tag"
                :key="selectedAttribute.id"
                v-for="selectedAttribute in selectedGroup.attributes"
              >
                {{ selectedGroup.name }}: {{ selectedAttribute.name }}
                <i
                  class="material-icons"
                  @click.prevent.stop="removeSelected(selectedAttribute, selectedGroup)"
                >close</i>
              </span>
            </template>
          </div>
          <input
            type="text"
            :placeholder="$t('search.placeholder')"
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
                    :class="['attribute-item', getSelectedClass(attribute, attributeGroup)]"
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
          :aria-label="$t('modal.close')"
        >
          {{ $t('modal.close') }}
        </button>

        <button
          type="button"
          class="btn btn-primary"
          @click.prevent.stop="generateCombinations"
        >
          <span v-if="!loading">
            {{ $t('modal.save') }}
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
        selectedAttributeGroups: {},
        combinationsService: new CombinationsService(this.productId),
        isModalShown: false,
        loading: false,
        scrollbar: null,
        dataSetConfig: {},
        searchSource: {},
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
          this.initDataSetConfig();
        } catch (error) {
          window.$.growl.error({message: error});
        }
      },
      initDataSetConfig() {
        const searchItems = this.getSearchableAttributes();
        this.searchSource = new Bloodhound({
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
          source: this.searchSource,
          display: 'name',
          value: 'name',
          minLength: 1,
          onSelect: (attribute, e, $searchInput) => {
            const attributeGroup = {
              id: attribute.group_id,
              name: attribute.group_name,
            };
            this.addSelected(attribute, attributeGroup);

            // This resets the search input or else previous search is cached and can be added again
            $searchInput.typeahead('val', '');
          },
          onClose(event, $searchInput) {
            $searchInput.typeahead('val', '');
            return true;
          },
        };

        dataSetConfig.templates = {
          suggestion: (item) => `<div class="px-2">${item.group_name}: ${item.name}</div>`,
        };

        this.dataSetConfig = dataSetConfig;
      },
      /**
       * Show the modal, and execute PerfectScrollBar and Typehead
       */
      showModal() {
        document.querySelector('body').classList.add('overflow-hidden');
        this.isModalShown = true;
        // We need to use a setTimeout to add it at the end
        // of the callstack so the modal is already displayed
        // when this is getting executed
        setTimeout(() => {
          this.scrollbar = new PerfectScrollbar(CombinationsMap.scrollBar);
          const $searchInput = $(CombinationsMap.searchInput);
          new AutoCompleteSearch($searchInput, this.dataSetConfig);
        }, 0);
      },
      /**
       * @returns {Array}
       */
      getSearchableAttributes() {
        const searchableAttributes = [];
        this.attributeGroups.forEach((attributeGroup) => {
          attributeGroup.attributes.forEach((attribute) => {
            if (this.isSelected(attribute, attributeGroup, this.selectedAttributeGroups)) {
              return;
            }

            attribute.group_name = attributeGroup.name;
            attribute.group_id = attributeGroup.id;
            searchableAttributes.push(attribute);
          });
        });

        return searchableAttributes;
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
        if (!this.isSelected(selectedAttribute, attributeGroup, this.selectedAttributeGroups)) {
          this.addSelected(selectedAttribute, attributeGroup);
        } else {
          this.removeSelected(selectedAttribute, attributeGroup);
        }
      },
      /**
       * @param {Object} attribute
       * @param {Object} attributeGroup
       */
      addSelected(attribute, attributeGroup) {
        // Extra check to avoid adding same attribute twice which would cause a duplicate key error
        if (this.isSelected(attribute, attributeGroup, this.selectedAttributeGroups)) {
          return;
        }

        // Add copy of attribute group in selected groups
        if (!this.selectedAttributeGroups[attributeGroup.id]) {
          const newAttributeGroup = {
            [attributeGroup.id]: {
              id: attributeGroup.id,
              name: attributeGroup.name,
              attributes: [],
            },
          };

          // This is needed to correctly handle observation
          this.selectedAttributeGroups = {
            ...this.selectedAttributeGroups,
            ...newAttributeGroup,
          };
        }

        this.selectedAttributeGroups[attributeGroup.id].attributes.push(attribute);
        this.updateSearchableAttributes();
      },
      /**
       * @param {Object} selectedAttribute
       * @param {Object} selectedAttributeGroup
       */
      removeSelected(selectedAttribute, selectedAttributeGroup) {
        if (!Object.prototype.hasOwnProperty.call(this.selectedAttributeGroups, selectedAttributeGroup.id)) {
          return;
        }

        const group = this.selectedAttributeGroups[selectedAttributeGroup.id];
        group.attributes = group.attributes.filter((attribute) => attribute.id !== selectedAttribute.id);

        this.updateSearchableAttributes();
      },
      /**
       * The selected attribute is provided as a parameter instead od using this reference because it helps the
       * observer work better whe this.selectedAttributeGroups is explicitly used as an argument.
       *
       * @param {Object} attribute
       * @param {Object} attributeGroup
       * @param {Object} attributeGroups
       *
       * @returns {boolean}
       */
      isSelected(attribute, attributeGroup, attributeGroups) {
        if (!Object.prototype.hasOwnProperty.call(attributeGroups, attributeGroup.id)) {
          return false;
        }

        return attributeGroups[attributeGroup.id].attributes.includes(attribute);
      },
      /**
       * @param {Object} attribute
       * @param {Object} attributeGroup
       *
       * @returns {string}
       */
      getSelectedClass(attribute, attributeGroup) {
        return this.isSelected(attribute, attributeGroup, this.selectedAttributeGroups) ? 'selected' : 'unselected';
      },
      /**
       * Update Bloodhound engine so that it does not include already selected attributes
       */
      updateSearchableAttributes() {
        const searchableAttributes = this.getSearchableAttributes();
        this.searchSource.clear();
        this.searchSource.add(searchableAttributes);
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
