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
  <div class="generate-modal-content">
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
              @click.prevent.stop="
                sendRemoveEvent(selectedAttribute, selectedGroup)
              "
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
        id="attributes-list-selector"
        class="attributes-list-overflow"
      >
        <div class="attributes-content">
          <div
            class="attribute-group"
            v-for="attributeGroup of attributeGroups"
            :key="attributeGroup.id"
          >
            <div class="attribute-group-header">
              <a
                class="attribute-group-name collapsed"
                data-toggle="collapse"
                :href="`#attribute-group-${attributeGroup.id}`"
              >
                <label>{{ attributeGroup.name }}</label>
              </a>
              <div class="md-checkbox attribute-group-checkbox">
                <label>
                  <input
                    class="attribute-group-checkbox"
                    type="checkbox"
                    :name="`checkbox_${attributeGroup.id}`"
                    @change.prevent.stop="toggleAll(attributeGroup)"
                    :checked="checkboxList.includes(attributeGroup)"
                  >
                  <i class="md-checkbox-control" />
                  {{
                    $tc('generator.select-all', attributeGroup.attributes.length, {
                      'valuesNb': attributeGroup.attributes.length,
                    })
                  }}
                </label>
              </div>
            </div>
            <div
              class="attribute-group-content attributes collapse"
              :id="`attribute-group-${attributeGroup.id}`"
            >
              <label
                v-for="attribute of attributeGroup.attributes"
                :class="[
                  'attribute-item',
                  getSelectedClass(attribute, attributeGroup),
                ]"
                :for="`attribute_${attribute.id}`"
                :key="attribute.id"
              >
                <input
                  type="checkbox"
                  :name="`attribute_${attribute.id}`"
                  :id="`attribute_${attribute.id}`"
                  @change="sendChangeEvent(attribute, attributeGroup)"
                >
                <div class="attribute-item-content">
                  <span
                    class="attribute-item-texture"
                    v-if="attribute.texture"
                    :style="`background: transparent url(${attribute.texture}) no-repeat; background-size: 100% auto;`"
                  />
                  <span
                    class="attribute-item-color"
                    v-else-if="attribute.color"
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
  </div>
</template>

<script lang="ts">
  import {defineComponent, PropType} from 'vue';
  import isSelected from '@pages/product/combination/mixins/is-attribute-selected';
  import ProductMap from '@pages/product/product-map';
  import PerfectScrollbar from 'perfect-scrollbar';
  // @ts-ignore
  import Bloodhound from 'typeahead.js';
  import AutoCompleteSearch, {AutoCompleteSearchConfig} from '@components/auto-complete-search';
  import Tokenizers from '@components/bloodhound/tokenizers';
  import {Attribute, AttributeGroup, AttributesSelectorStates} from '@pages/product/combination/types';

  const {$} = window;

  const CombinationsMap = ProductMap.combinations;

  export default defineComponent({
    name: 'AttributesSelector',
    props: {
      attributeGroups: {
        type: Array as PropType<Array<AttributeGroup>>,
        default: () => [],
      },
      selectedAttributeGroups: {
        type: Object as PropType<Record<string, AttributeGroup>>,
        default: () => ({}),
      },
    },
    mixins: [isSelected],
    data(): AttributesSelectorStates {
      return {
        dataSetConfig: {},
        searchSource: {},
        scrollbar: null,
        hasGeneratedCombinations: false,
        checkboxList: [],
      };
    },
    mounted() {
      this.initDataSetConfig();
      this.scrollbar = new PerfectScrollbar(CombinationsMap.scrollBar);
      const $searchInput = $(CombinationsMap.searchInput);
      new AutoCompleteSearch($searchInput, <Partial<AutoCompleteSearchConfig>> this.dataSetConfig);
    },
    watch: {
      selectedAttributeGroups(value: any): void {
        const attributes = Object.keys(value);

        if (attributes.length <= 0) {
          this.checkboxList = [];
        }
      },
    },
    methods: {
      initDataSetConfig(): void {
        const searchItems = this.getSearchableAttributes();

        const letters = [
          'name',
          'value',
          'group_name',
        ];

        this.searchSource = new Bloodhound({
          datumTokenizer: Tokenizers.obj.letters(letters),
          queryTokenizer: Bloodhound.tokenizers.nonword,
          local: searchItems,
        });

        this.dataSetConfig = {
          source: this.searchSource,
          display: (item: Attribute) => `${item.group_name}: ${item.name}`,
          value: 'name',
          minLength: 1,
          onSelect: (attribute: Attribute, e: JQueryEventObject, $searchInput: JQuery) => {
            const attributeGroup: AttributeGroup = {
              id: attribute.group_id,
              name: attribute.group_name,
              attributes: [],
              publicName: attribute.group_name,
            };
            this.sendAddEvent(attribute, attributeGroup);

            // This resets the search input or else previous search is cached and can be added again
            $searchInput.typeahead('val', '');

            return true;
          },
        };
      },
      /**
       * @returns {Array}
       */
      getSearchableAttributes(): Array<Attribute> {
        const searchableAttributes: Array<Attribute> = [];
        this.attributeGroups.forEach((attributeGroup: AttributeGroup) => {
          attributeGroup.attributes.forEach((attribute: Attribute) => {
            if (
              this.isSelected(
                attribute,
                attributeGroup,
                this.selectedAttributeGroups,
              )
            ) {
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
       * @param {Object} attribute
       * @param {Object} attributeGroup
       *
       * @returns {string}
       */
      getSelectedClass(attribute: Attribute, attributeGroup: AttributeGroup): string {
        return this.isSelected(
          attribute,
          attributeGroup,
          this.selectedAttributeGroups,
        )
          ? 'selected'
          : 'unselected';
      },
      sendRemoveEvent(selectedAttribute: Attribute, selectedAttributeGroup: AttributeGroup): void {
        this.$emit('removeSelected', {
          selectedAttribute,
          selectedAttributeGroup,
        });
        this.updateSearchableAttributes();
        this.updateCheckboxes(selectedAttributeGroup);
      },
      sendChangeEvent(selectedAttribute: Attribute, attributeGroup: AttributeGroup): void {
        this.$emit('changeSelected', {selectedAttribute, attributeGroup});
        this.updateSearchableAttributes();
        this.updateCheckboxes(attributeGroup);
      },
      sendAddEvent(selectedAttribute: Attribute, attributeGroup: AttributeGroup): void {
        this.$emit('addSelected', {selectedAttribute, attributeGroup});
        this.updateSearchableAttributes();
        this.updateCheckboxes(attributeGroup);
      },
      /**
       * Update Bloodhound engine so that it does not include already selected attributes
       */
      updateSearchableAttributes(): void {
        const searchableAttributes = this.getSearchableAttributes();
        this.searchSource.clear();
        this.searchSource.add(searchableAttributes);
      },
      toggleAll(attributeGroup: AttributeGroup): void {
        if (this.checkboxList.includes(attributeGroup)) {
          this.checkboxList = this.checkboxList.filter(
            (e) => e.id !== attributeGroup.id,
          );
        } else {
          this.checkboxList.push(attributeGroup);
        }

        this.$emit('toggleAll', {
          attributeGroup,
          select: this.checkboxList.includes(attributeGroup),
        });
      },
      updateCheckboxes(attributeGroup: AttributeGroup): void {
        if (
          this.selectedAttributeGroups[attributeGroup.id]
          && !this.checkboxList.includes(attributeGroup)
          && this.selectedAttributeGroups[attributeGroup.id].attributes.length
            === attributeGroup.attributes.length
        ) {
          this.checkboxList.push(attributeGroup);
        } else {
          this.checkboxList = this.checkboxList.filter(
            (group) => group.id !== attributeGroup.id,
          );
        }
      },
    },
  });
</script>

<style lang="scss" type="text/scss">
@import '~@scss/config/_settings.scss';

#product-combinations-generate {
  .modal {
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

    #attributes-list-selector {
      max-height: 50vh;

      .attribute-group {
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

        .attribute-item {
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

      .attributes {
        height: auto;
        padding: var(--#{$cdk}size-8);
      }
    }

    .product-combinations-modal-content {
      position: relative;
      padding-bottom: var(--#{$cdk}size-8);
    }
  }
}
</style>
