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
  <div id="filters-container">
    <button
      class="search-input collapse-button"
      type="button"
      data-toggle="collapse"
      data-target="#filters"
    >
      <i class="material-icons mr-1">filter_list</i>
      <i class="material-icons float-right ">keyboard_arrow_down</i>
      {{ trans('button_advanced_filter') }}
    </button>
    <div
      id="filters"
      class="container-fluid collapse"
    >
      <div class="row">
        <div class="col-lg-4">
          <div
            v-if="isOverview"
            class="py-3"
          >
            <h2>{{ trans('filter_suppliers') }}</h2>
            <FilterComponent
              :placeholder="trans('filter_search_suppliers')"
              :list="this.$store.getters.suppliers"
              class="filter-suppliers"
              item-id="supplier_id"
              label="name"
              @active="onFilterActive"
            />
          </div>
          <div
            v-else
            class="py-3"
          >
            <h2>{{ trans('filter_movements_type') }}</h2>
            <PSSelect
              :items="movementsTypes"
              item-id="id_stock_mvt_reason"
              item-name="name"
              @change="onChange"
            >
              {{ trans('none') }}
            </PSSelect>
            <h2 class="mt-4">
              {{ trans('filter_movements_employee') }}
            </h2>
            <PSSelect
              :items="employees"
              item-id="id_employee"
              item-name="name"
              @change="onChange"
            >
              {{ trans('none') }}
            </PSSelect>
            <h2 class="mt-4">
              {{ trans('filter_movements_period') }}
            </h2>
            <form class="row">
              <div class="col-md-6">
                <label>{{ trans('filter_datepicker_from') }}</label>
                <PSDatePicker
                  :locale="locale"
                  @dpChange="onDpChange"
                  @reset="onClear"
                  type="sup"
                />
              </div>
              <div class="col-md-6">
                <label>{{ trans('filter_datepicker_to') }}</label>
                <PSDatePicker
                  :locale="locale"
                  @dpChange="onDpChange"
                  @reset="onClear"
                  type="inf"
                />
              </div>
            </form>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="py-3">
            <h2>{{ trans('filter_categories') }}</h2>
            <FilterComponent
              :placeholder="trans('filter_search_category')"
              :list="categoriesList"
              class="filter-categories"
              item-id="id_category"
              label="name"
              @active="onFilterActive"
            />
          </div>
        </div>
        <div class="col-lg-4">
          <div class="py-3">
            <h2>{{ trans('filter_status') }}</h2>
            <PSRadio
              id="enable"
              :label="trans('filter_status_enable')"
              :checked="false"
              value="1"
              @change="onRadioChange"
            />
            <PSRadio
              id="disable"
              :label="trans('filter_status_disable')"
              :checked="false"
              value="0"
              @change="onRadioChange"
            />
            <PSRadio
              id="all"
              :label="trans('filter_status_all')"
              :checked="true"
              value="null"
              @change="onRadioChange"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
  import Vue from 'vue';
  import PSSelect from '@app/widgets/ps-select.vue';
  import PSDatePicker from '@app/widgets/ps-datepicker.vue';
  import PSRadio from '@app/widgets/ps-radio.vue';
  import FilterComponent from './filters/filter-component.vue';

  export default Vue.extend({
    computed: {
      locale(): string {
        return window.data.locale;
      },
      isOverview(): boolean {
        return this.$route.name === 'overview';
      },
      employees(): Record<string, any> {
        return this.$store.state.employees;
      },
      movementsTypes(): Record<string, any> {
        return this.$store.state.movementsTypes;
      },
      categoriesList(): Record<string, any> {
        return this.$store.getters.categories;
      },
    },
    methods: {
      onClear(event: any): void {
        delete this.date_add[<number>event.dateType];
        this.applyFilter();
      },
      onClick(): void {
        this.applyFilter();
      },
      onFilterActive(list: Array<any>, type: string): void {
        if (type === 'supplier') {
          this.suppliers = list;
        } else {
          this.categories = list;
        }
        this.disabled = !this.suppliers.length && !this.categories.length;
        this.applyFilter();
      },
      applyFilter(): void {
        this.$store.dispatch('isLoading');
        this.$emit('applyFilter', {
          suppliers: this.suppliers,
          categories: this.categories,
          id_stock_mvt_reason: this.id_stock_mvt_reason,
          id_employee: this.id_employee,
          date_add: this.date_add,
          active: this.active,
        });
      },
      onChange(item: any): void {
        if (item.itemId === 'id_stock_mvt_reason') {
          this.id_stock_mvt_reason = item.value === 'default' ? [] : item.value;
        } else {
          this.id_employee = item.value === 'default' ? [] : item.value;
        }
        this.applyFilter();
      },
      onDpChange(event: any) {
        this.date_add[<number>event.dateType] = event.date.unix();
        if (event.oldDate) {
          this.applyFilter();
        }
      },
      onRadioChange(value: any): void {
        this.active = value;
        this.applyFilter();
      },
    },
    components: {
      FilterComponent,
      PSSelect,
      PSDatePicker,
      PSRadio,
    },
    mounted() {
      this.date_add = [];
      this.$store.dispatch('getSuppliers');
      this.$store.dispatch('getCategories');
    },
    data() {
      return {
        disabled: true,
        suppliers: [] as Array<any>,
        categories: [] as Array<any>,
        id_stock_mvt_reason: [] as Array<any>,
        id_employee: [] as Array<any>,
        date_add: [] as Array<any>,
        active: null,
      };
    },
  });
</script>
