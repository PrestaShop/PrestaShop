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
  <PSTable class="mt-1">
    <thead>
      <tr>
        <th width="27%" class="thead-title">
          {{trans('title_product')}}
          <PSSort order="product" @sort="toggleSort" />
        </th>
        <th>
          {{trans('title_reference')}}
          <PSSort order="reference" @sort="toggleSort" />
        </th>
        <th>
          {{trans('title_supplier')}}
          <PSSort order="supplier" @sort="toggleSort" />
        </th>
        <th class="text-sm-center">
          {{trans('title_status')}}
        </th>
        <th class="text-sm-center">
          {{trans('title_physical')}}
          <PSSort order="physical_quantity" @sort="toggleSort" />
        </th>
        <th class="text-sm-center">
          {{trans('title_reserved')}}
        </th>
        <th class="text-sm-left text-md-center">
          {{trans('title_available')}}
          <PSSort order="available_quantity" @sort="toggleSort" />
        </th>
        <th class="text-md-left" :title="trans('title_edit_quantity')">
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
        <td colspan="7">
          <PSAlert alertType="ALERT_TYPE_WARNING" :hasClose="false" >
            {{trans('no_product')}}
          </PSAlert>
        </td>
      </tr>
      <ProductLine
        v-else
        v-for="(product, index) in products"
        key=${index}
        :product="product"
      />
    </tbody>
  </PSTable>
</template>

<script>
  import ProductLine from './product-line';
  import PSAlert from 'app/widgets/ps-alert';
  import PSTable from 'app/widgets/ps-table/ps-table';
  import PSSort from 'app/widgets/ps-table/ps-sort';
  import PSLoader from 'app/widgets/ps-loader';

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
      toggleSort(order, isSorted) {
        const desc = isSorted ? ' desc' : '';
        this.$store.dispatch('updateOrder', order);
        this.$emit('sort', desc);
      },
    },
    computed: {
      products() {
        return this.$store.state.products;
      },
      emptyProducts() {
        return !this.$store.state.products.length;
      },
    },
  };
</script>
