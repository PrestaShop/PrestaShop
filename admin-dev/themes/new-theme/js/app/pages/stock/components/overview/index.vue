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
  <section class="stock-overview">
    <ProductsActions />
    <ProductsTable
      :isLoading="isLoading"
      @sort="sort"
    />
  </section>
</template>

<script>
  import ProductsActions from './products-actions';
  import ProductsTable from './products-table';

  const DEFAULT_SORT = 'asc';

  export default {
    computed: {
      isLoading() {
        return this.$store.state.isLoading;
      },
    },
    methods: {
      sort(sortDirection) {
        this.$emit('fetch', sortDirection);
      },
    },
    mounted() {
      this.$store.dispatch('updatePageIndex', 1);
      this.$store.dispatch('updateKeywords', []);
      this.$store.dispatch('updateOrder', 'product');
      this.$store.dispatch('isLoading');
      this.$emit('resetFilters');
      this.$emit('fetch', DEFAULT_SORT);
    },
    components: {
      ProductsActions,
      ProductsTable,
    },
  };
</script>
