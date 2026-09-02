<!--*
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
    color: var(--#{$cdk}primary-800);
    margin-bottom: var(--#{$cdk}size-16);
  }

  &-line {
    display: flex;
    align-items: flex-start;
    flex-wrap: wrap;
    margin: 0 calc(-1 * var(--#{$cdk}size-6));
  }
}
</style>
