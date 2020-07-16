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
  <PSTable class="mt-1">
    <thead>
      <tr class="column-headers">
        <th scope="col" width="27%" class="product-title">
          <PSSort order="product" @sort="sort" :current-sort="currentSort">
            {{trans('title_product')}}
          </PSSort>
        </th>
        <th scope="col">
          <PSSort order="reference" @sort="sort" :current-sort="currentSort">
            {{trans('title_reference')}}
          </PSSort>
        </th>
        <th>
          <PSSort order="supplier" @sort="sort" :current-sort="currentSort">
            {{trans('title_supplier')}}
          </PSSort>
        </th>
        <th class="text-center">
          {{trans('title_status')}}
        </th>
        <th class="text-center">
          <PSSort order="physical_quantity" @sort="sort" :current-sort="currentSort">
            {{trans('title_physical')}}
          </PSSort>
        </th>
        <th class="text-center">
          {{trans('title_reserved')}}
        </th>
        <th class="text-center">
          <PSSort order="available_quantity" @sort="sort" :current-sort="currentSort">
            {{trans('title_available')}}
          </PSSort>
        </th>
        <th :title="trans('title_edit_quantity')">
          <i class="material-icons">edit</i>
          {{trans('title_edit_quantity')}}
        </th>
      </tr>
    </thead>
    <tbody>
      <tr v-if="this.isLoading">
        <td colspan="8">
          <PSLoader v-for="(n, index) in 3" class="mt-1" :key="index">
            <div class="background-masker header-top"></div>
            <div class="background-masker header-left"></div>
            <div class="background-masker header-bottom"></div>
            <div class="background-masker subheader-left"></div>
            <div class="background-masker subheader-bottom"></div>
          </PSLoader>
        </td>
      </tr>
      <tr v-else-if="emptyProducts">
        <td colspan="8">
          <PSAlert alertType="ALERT_TYPE_WARNING" :hasClose="false" >
            {{trans('no_product')}}
          </PSAlert>
        </td>
      </tr>
      <ProductLine
        v-else
        v-for="(product, index) in products"
        :key=index
        :product="product"
      />
    </tbody>
  </PSTable>
</template>

<script>
  import ProductLine from './product-line';
  import PSAlert from '@app/widgets/ps-alert';
  import PSTable from '@app/widgets/ps-table/ps-table';
  import PSSort from '@app/widgets/ps-table/ps-sort';
  import PSLoader from '@app/widgets/ps-loader';

  export default {
    props: ['isLoading'],
    components: {
      ProductLine,
      PSSort,
      PSAlert,
      PSTable,
      PSLoader,
    },
    methods: {
      sort(order, sortDirection) {
        this.$store.dispatch('updateOrder', order);
        this.$emit('sort', sortDirection === 'desc' ? 'desc' : 'asc');
      },
    },
    computed: {
      products() {
        return this.$store.state.products;
      },
      emptyProducts() {
        return !this.$store.state.products.length;
      },
      currentSort() {
        return this.$store.state.order;
      },
    },
  };
</script>
