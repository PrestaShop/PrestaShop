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
  <div class="combinations-filters">
    <label
      class="control-label"
      v-if="filters.length"
    >{{ $t('filters.label') }}</label>

    <div
      class="combinations-filters-line"
      v-if="filters.length"
    >
      <checkboxes-dropdown
        v-for="filter in filters"
        :key="filter.id"
        :parent-id="filter.id"
        :label="filter.name"
        :choices="filter.attributes"
        :selected-choice-ids="selectedFilterIds[filter.id]"
        @selectChoice="addFilter"
        @unselectChoice="removeFilter"
      />
      <button
        type="button"
        v-if="selectedFiltersNumber > 0"
        class="btn btn-outline-secondary combinations-filters-clear"
        @click="clearAll"
      >
        <i class="material-icons">close</i>
        {{ $tc('filters.clear', selectedFiltersNumber, { 'filtersNb': selectedFiltersNumber }) }}
      </button>
    </div>
  </div>
</template>

<script lang="ts">
  import {defineComponent, PropType} from 'vue';
  import CheckboxesDropdown from '@app/components/checkboxes-dropdown/CheckboxesDropdown.vue';
  import EventEmitter from '@components/event-emitter';
  import ProductEventMap from '@pages/product/product-event-map';
  import ProductMap from '@pages/product/product-map';
  import {Attribute, AttributeGroup} from '@pages/product/combination/types';
  import {Choice} from '@app/components/checkboxes-dropdown/types';

  const CombinationEvents = ProductEventMap.combinations;

  interface Filter {
    id: number;
    name: string;
    attributes: Choice[];
  }

  export default defineComponent({
    name: 'Filters',
    data(): {
      selectedFilterIds: Array<number[]>,
      filters: Filter[],
    } {
      return {
        selectedFilterIds: [],
        filters: [],
      };
    },
    props: {
      attributeGroups: {
        type: Array as PropType<AttributeGroup[]>,
        required: true,
      },
      eventEmitter: {
        type: Object as PropType<typeof EventEmitter>,
        required: true,
      },
    },
    components: {
      CheckboxesDropdown,
    },
    computed: {
      selectedFiltersNumber(): number {
        if (!this.selectedFilterIds) {
          return 0;
        }

        return this.selectedFilterIds.reduce<number>((total: number, ids: number[]) => total + ids.length, 0);
      },
    },
    mounted() {
      this.eventEmitter.on(CombinationEvents.clearFilters, () => this.clearAll());
      // remap attribute groups to fit format for checkboxes-dropdown component
      this.filters = this.attributeGroups.map((attributeGroup: AttributeGroup): Filter => ({
        id: attributeGroup.id,
        name: attributeGroup.name,
        attributes: attributeGroup.attributes.map((attribute: Attribute): Choice => ({
          id: attribute.id,
          label: attribute.name,
          // this name will be inserted as <input> name,
          // so we can use it to exclude these checkboxes from listening onchange event when updating save button state
          name: ProductMap.combinations.list.attributeFilterInputName,
        })),
      }));
    },
    methods: {
      getSelectedIds(parentId: number): number[] {
        if (this.selectedFilterIds[parentId]) {
          return this.selectedFilterIds[parentId];
        }

        return [];
      },
      addFilter(filter: Choice, parentId: number): void {
        // If absent set new field with set method so that it's reactive
        if (!this.selectedFilterIds[parentId]) {
          this.selectedFilterIds[parentId] = [];
        }

        this.selectedFilterIds[parentId].push(filter.id);
        this.updateFilters();
      },
      removeFilter(filter: Choice, parentId: number): void {
        if (!this.selectedFilterIds[parentId]) {
          return;
        }

        this.selectedFilterIds[parentId] = this.selectedFilterIds[parentId].filter(
          (id: number) => filter.id !== id,
        );

        if (this.selectedFilterIds[parentId].length === 0) {
          // remove parent array if it became empty after filters removal
          delete this.selectedFilterIds[parentId];
        }

        this.updateFilters();
      },
      clearAll(): void {
        this.selectedFilterIds = [];
        this.eventEmitter.emit(CombinationEvents.updateAttributeFilters, []);
      },
      updateFilters(): void {
        this.eventEmitter.emit(CombinationEvents.updateAttributeFilters, this.selectedFilterIds);
      },
    },
  });
</script>

<style lang="scss" type="text/scss">
@import "~@scss/config/_settings.scss";

.combinations-filters {
  .control-label {
    font-weight: 600;
    color: #000;
    margin-bottom: 1rem;
  }

  &-line {
    display: flex;
    align-items: flex-start;
    flex-wrap: wrap;
    margin: 0 -0.35rem;
  }
}
</style>
