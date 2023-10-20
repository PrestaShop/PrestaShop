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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *-->
<template>
  <div id="product-combinations-generate">
    <modal
      v-if="isModalShown"
      :modal-title="$t('modal.title')"
      :confirmation="true"
      :close-on-click-outside="false"
      @close="closeModal"
    >
      <template #body>
        <attributes-selector
          :attribute-groups="attributeGroups"
          :selected-attribute-groups="selectedAttributeGroups"
          @changeSelected="changeSelected"
          @removeSelected="removeSelected"
          @addSelected="addSelected"
          @toggleAll="toggleAll"
          v-if="attributeGroups"
        />
      </template>

      <template #footer-confirmation>
        <div
          v-if="isMultiStoreActive"
          class="md-checkbox md-checkbox-inline"
        >
          <label>
            <input
              v-model="applyToAllShops"
              type="checkbox"
              id="generate_combinations_all_shop"
              name="generate_combinations_all_shop"
              class="form-check-input"
            >
            <i class="md-checkbox-control"/>
            {{ $t('label.apply-to-all-shops') }}
          </label>
        </div>

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
          :disabled="!generatedCombinationsNb || loading"
        >
          <span v-if="!loading">
            {{
              $tc('generator.action', generatedCombinationsNb, {
                'combinationsNb': generatedCombinationsNb,
              })
            }}
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

<script lang="ts">
  import {generateCombinations} from '@pages/product/service/combination';
  import AttributesSelector from '@pages/product/combination/generator/AttributesSelector.vue';
  import isSelected from '@pages/product/combination/mixins/is-attribute-selected';
  import {getAllAttributeGroups} from '@pages/product/service/attribute-group';
  import Modal from '@PSVue/components/Modal.vue';
  import {defineComponent} from 'vue';
  import ProductEventMap from '@pages/product/product-event-map';
  import {Attribute, AttributeGroup} from '@pages/product/combination/types';

  const {$} = window;

  const CombinationEvents = ProductEventMap.combinations;

  interface CombinationGeneratorStates {
    attributeGroups: Array<AttributeGroup>,
    selectedAttributeGroups: Record<string, AttributeGroup>,
    isModalShown: boolean,
    preLoading: boolean,
    loading: boolean,
    hasGeneratedCombinations: boolean,
    applyToAllShops: boolean,
  }

  export default defineComponent({
    name: 'CombinationGenerator',
    data(): CombinationGeneratorStates {
      return {
        attributeGroups: [],
        selectedAttributeGroups: {},
        isModalShown: false,
        preLoading: true,
        loading: false,
        hasGeneratedCombinations: false,
        applyToAllShops: false,
      };
    },
    props: {
      productId: {
        type: Number,
        required: true,
      },
      shopId: {
        type: Number,
        required: true,
      },
      isMultiStoreActive: {
        type: Boolean,
        required: true,
      },
      eventEmitter: {
        type: Object,
        required: true,
      },
    },
    mixins: [isSelected],
    components: {
      Modal,
      AttributesSelector,
    },
    computed: {
      generatedCombinationsNb(): number {
        const groupIds = Object.keys(this.selectedAttributeGroups);
        let combinationsNumber = 0;

        groupIds.forEach((attributeGroupId) => {
          const {attributes} = this.selectedAttributeGroups[attributeGroupId];

          if (!attributes.length) {
            return;
          }

          // Only start counting when at least one attribute is selected
          if (combinationsNumber === 0) {
            combinationsNumber = 1;
          }
          combinationsNumber *= this.selectedAttributeGroups[attributeGroupId].attributes.length;
        });

        return combinationsNumber;
      },
    },
    mounted() {
      this.initAttributeGroups();
      this.eventEmitter.on(CombinationEvents.openCombinationsGenerator, () => this.showModal());
    },
    methods: {
      /**
       * This methods is used to initialize combinations definitions
       */
      async initAttributeGroups(): Promise<void> {
        try {
          this.attributeGroups = await getAllAttributeGroups(this.shopId);
          window.prestaShopUiKit.init();
          this.preLoading = false;
          this.eventEmitter.emit(CombinationEvents.combinationGeneratorReady);
        } catch (error) {
          window.$.growl.error({message: error});
        }
      },
      /**
       * Show the modal, and execute PerfectScrollBar and Typehead
       */
      showModal(): void {
        if (this.preLoading) {
          return;
        }
        document.querySelector('body')?.classList.add('overflow-hidden');
        this.hasGeneratedCombinations = false;
        this.selectedAttributeGroups = {};
        this.isModalShown = true;
      },
      /**
       * Handle modal closing
       */
      closeModal(): void {
        this.isModalShown = false;
        document.querySelector('body')?.classList.remove('overflow-hidden');
        if (this.hasGeneratedCombinations) {
          this.eventEmitter.emit(CombinationEvents.refreshCombinationList);
        }
      },
      /**
       * Used when the user clicks on the Generate button of the modal
       */
      async generateCombinations(): Promise<void> {
        this.loading = true;
        const data: Record<string, any> = {attributes: {}};

        Object.keys(this.selectedAttributeGroups).forEach((attributeGroupId) => {
          data.attributes[attributeGroupId] = [];
          this.selectedAttributeGroups[attributeGroupId].attributes.forEach(
            (attribute: Attribute) => {
              data.attributes[attributeGroupId].push(attribute.id);
            },
          );
        });

        try {
          const response = await generateCombinations(
            this.productId,
            this.applyToAllShops ? null : this.shopId,
            data,
          );
          $.growl({
            message: this.$t('generator.success', {
              combinationsNb: response.combination_ids.length,
            }),
          });
          this.selectedAttributeGroups = {};
          this.hasGeneratedCombinations = true;
          this.closeModal();
        } catch (error: any) {
          if (error.responseJSON && error.responseJSON?.error) {
            $.growl.error({message: error.responseJSON.error});
          } else {
            $.growl.error({message: error});
          }
        }

        this.loading = false;
      },
      /**
       * Remove the attribute if it's selected or add it
       *
       * @param {Object} selectedAttribute
       * @param {{id: int, name: string}} attributeGroup
       */
      changeSelected(
        {selectedAttribute, attributeGroup}: {
          selectedAttribute: Attribute,
          attributeGroup: AttributeGroup
        },
      ): void {
        if (
          !this.isSelected(
            selectedAttribute,
            attributeGroup,
            this.selectedAttributeGroups,
          )
        ) {
          this.addSelected({selectedAttribute, attributeGroup});
        } else {
          this.removeSelected({
            selectedAttribute,
            selectedAttributeGroup: attributeGroup,
          });
        }
      },
      /**
       * @param {Object} selectedAttribute
       * @param {{id: int, name: string}} attributeGroup
       */
      addSelected({selectedAttribute, attributeGroup}: {
        selectedAttribute: Attribute,
        attributeGroup: AttributeGroup
      }) {
        // Extra check to avoid adding same attribute twice which would cause a duplicate key error
        if (
          this.isSelected(
            selectedAttribute,
            attributeGroup,
            this.selectedAttributeGroups,
          )
        ) {
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

        this.selectedAttributeGroups[attributeGroup.id].attributes.push(
          selectedAttribute,
        );
      },
      /**
       * @param {Object} selectedAttribute
       * @param {Object} selectedAttributeGroup
       */
      removeSelected({selectedAttribute, selectedAttributeGroup}: {
        selectedAttribute: Attribute,
        selectedAttributeGroup: AttributeGroup
      }) {
        if (
          !Object.prototype.hasOwnProperty.call(
            this.selectedAttributeGroups,
            selectedAttributeGroup.id,
          )
        ) {
          return;
        }

        const group = this.selectedAttributeGroups[selectedAttributeGroup.id];
        group.attributes = group.attributes.filter(
          (attribute: Record<string, any>) => attribute.id !== selectedAttribute.id,
        );
      },
      /**
       * Remove the attribute if it's selected or add it
       *
       * @param {Object} selectedAttribute
       * @param {{id: int, name: string}} attributeGroup
       */
      toggleAll({attributeGroup, select}: {attributeGroup: AttributeGroup, select: Record<string, any>}) {
        if (select) {
          attributeGroup.attributes.forEach((attribute: Attribute) => {
            this.addSelected({selectedAttribute: attribute, attributeGroup});
          });
        } else {
          attributeGroup.attributes.forEach((attribute: Attribute) => {
            this.removeSelected({
              selectedAttribute: attribute,
              selectedAttributeGroup: attributeGroup,
            });
          });
        }
      },
    },
  });
</script>
