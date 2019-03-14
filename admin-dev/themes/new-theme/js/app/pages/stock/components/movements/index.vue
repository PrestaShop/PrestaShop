<!--**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *-->
<template>
  <section class="stock-movements">
    <PSTable class="mt-1">
      <thead>
        <tr>
          <th width="30%">
            <PSSort order="product" @sort="sort" :current-sort="currentSort">
              {{trans('title_product')}}
            </PSSort>
          </th>
          <th>
            <PSSort order="reference" @sort="sort" :current-sort="currentSort">
              {{trans('title_reference')}}
            </PSSort>
          </th>
          <th>
            {{trans('title_movements_type')}}
          </th>
          <th class="text-center">
            {{trans('title_quantity')}}
          </th>
          <th class="text-center">
            <PSSort order="date_add" @sort="sort" :current-sort="currentSort">
              {{trans('title_date')}}
            </PSSort>
          </th>
          <th>
            {{trans('title_employee')}}
          </th>
        </tr>
      </thead>
      <tbody>
        <tr v-if="this.isLoading">
          <td colspan="6">
            <PSLoader v-for="(n, index) in 3" class="mt-1" :key="index">
              <div class="background-masker header-top"></div>
              <div class="background-masker header-left"></div>
              <div class="background-masker header-bottom"></div>
              <div class="background-masker subheader-left"></div>
              <div class="background-masker subheader-bottom"></div>
            </PSLoader>
          </td>
        </tr>
        <tr v-else-if="emptyMovements">
          <td colspan="6">
            <PSAlert alertType="ALERT_TYPE_WARNING" :hasClose="false">
              {{trans('no_product')}}
            </PSAlert>
          </td>
        </tr>
        <MovementLine v-else v-for="(product, index) in movements" key=${index} :product="product" />
      </tbody>
    </PSTable>
  </section>
</template>

<script>
  import PSTable from 'app/widgets/ps-table/ps-table';
  import PSSort from 'app/widgets/ps-table/ps-sort';
  import PSAlert from 'app/widgets/ps-alert';
  import PSLoader from 'app/widgets/ps-loader';
  import MovementLine from './movement-line';

  const DEFAULT_SORT = 'desc';

  export default {
    computed: {
      isLoading() {
        return this.$store.state.isLoading;
      },
      movements() {
        return this.$store.state.movements;
      },
      emptyMovements() {
        return !this.$store.state.movements.length;
      },
      currentSort() {
        return this.$store.state.order;
      },
    },
    methods: {
      sort(order, sortDirection) {
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
  };
</script>
