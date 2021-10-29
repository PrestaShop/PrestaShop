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
  <section class="stock-movements">
    <PSTable class="mt-1">
      <thead>
        <tr>
          <th width="30%">
            <PSSort
              order="product"
              @sort="sort"
              :current-sort="currentSort"
            >
              {{ trans('title_product') }}
            </PSSort>
          </th>
          <th>
            <PSSort
              order="reference"
              @sort="sort"
              :current-sort="currentSort"
            >
              {{ trans('title_reference') }}
            </PSSort>
          </th>
          <th>
            {{ trans('title_movements_type') }}
          </th>
          <th class="text-center">
            {{ trans('title_quantity') }}
          </th>
          <th class="text-center">
            <PSSort
              order="date_add"
              @sort="sort"
              :current-sort="currentSort"
            >
              {{ trans('title_date') }}
            </PSSort>
          </th>
          <th>
            {{ trans('title_employee') }}
          </th>
        </tr>
      </thead>
      <tbody>
        <tr v-if="this.isLoading">
          <td colspan="6">
            <PSLoader
              v-for="(n, index) in 3"
              class="mt-1"
              :key="index"
            >
              <div class="background-masker header-top" />
              <div class="background-masker header-left" />
              <div class="background-masker header-bottom" />
              <div class="background-masker subheader-left" />
              <div class="background-masker subheader-bottom" />
            </PSLoader>
          </td>
        </tr>
        <tr v-else-if="emptyMovements">
          <td colspan="6">
            <PSAlert
              alert-type="ALERT_TYPE_WARNING"
              :has-close="false"
            >
              {{ trans('no_product') }}
            </PSAlert>
          </td>
        </tr>
        <MovementLine
          v-else
          v-for="(product, index) in movements"
          :key="index"
          :product="product"
        />
      </tbody>
    </PSTable>
  </section>
</template>

<script lang="ts">
  import Vue from 'vue';
  import PSTable from '@app/widgets/ps-table/ps-table.vue';
  import PSSort from '@app/widgets/ps-table/ps-sort.vue';
  import PSAlert from '@app/widgets/ps-alert.vue';
  import PSLoader from '@app/widgets/ps-loader.vue';
  import MovementLine from './movement-line.vue';

  const DEFAULT_SORT = 'desc';

  export default Vue.extend({
    computed: {
      isLoading(): boolean {
        return this.$store.state.isLoading;
      },
      movements(): Record<string, any> {
        return this.$store.state.movements;
      },
      emptyMovements(): boolean {
        return !this.$store.state.movements.length;
      },
      currentSort(): string {
        return this.$store.state.order;
      },
    },
    methods: {
      sort(order: string, sortDirection: string): void {
        this.$store.dispatch('updateOrder', order);
        this.$emit('fetch', sortDirection === 'desc' ? 'desc' : 'asc');
      },
    },
    mounted() {
      this.$store.dispatch('updatePageIndex', 1);
      this.$store.dispatch('updateKeywords', []);
      this.$store.dispatch('getEmployees');
      this.$store.dispatch('getMovementsTypes');
      this.$store.dispatch('updateOrder', 'date_add');
      this.$emit('resetFilters');
      this.$emit('fetch', DEFAULT_SORT);
    },
    components: {
      PSTable,
      PSSort,
      PSAlert,
      PSLoader,
      MovementLine,
    },
  });
</script>
