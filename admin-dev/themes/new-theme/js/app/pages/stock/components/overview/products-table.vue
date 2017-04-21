<template>
  <PSTable class="m-t-1">
    <thead>
      <tr>
        <th width="40%" class="thead-title">
          Product
          <PSSort order="product" :isDesc="isSorted" @sort="toggleSort" />
        </th>
        <th>
          Reference
          <PSSort order="reference" :isDesc="isSorted" @sort="toggleSort" />
        </th>
        <th>
          Supplier
          <PSSort order="supplier" :isDesc="isSorted" @sort="toggleSort" />
        </th>
        <th class="text-xs-center">
          Physical
          <PSSort order="physical_quantity" :isDesc="isSorted" @sort="toggleSort" />
        </th>
        <th class="text-xs-center">
          Reserved
        </th>
        <th class="text-xs-center">
          Available
          <PSSort order="available_quantity" :isDesc="isSorted" @sort="toggleSort" />
        </th>
        <th class="text-xs-right">
          <i class="material-icons">edit</i>
          Edit Quantity
        </th>
      </tr>
    </thead>
    <tbody>
      <PSAlert v-if="emptyProducts">
        No product matches your search. Try changing search terms.
      </PSAlert>
      <ProductLine v-for="(product, index) in products" key=${index} :product="product" />
    </tbody>
  </PSTable>
</template>

<script>
  import ProductLine from './product-line';
  import PSAlert from 'app/widgets/ps-alert';
  import PSTable from 'app/widgets/ps-table/ps-table';
  import PSSort from 'app/widgets/ps-table/ps-sort';

  export default {
    components: {
      ProductLine,
      PSSort,
      PSAlert,
      PSTable
    },
    methods: {
      toggleSort(order, desc) {
        this.isSorted = !this.isSorted;
        this.$store.dispatch('updateOrder', order);
        this.$emit('sort', desc);
      }
    },
    computed: {
      products() {
       return this.$store.getters.products;
      },
      emptyProducts() {
        return !this.$store.getters.products.length;
      }
    },
    data() {
      return {
        isSorted: true
      }
    }
  }
</script>

<style lang="sass?outputStyle=expanded">
  @import "~PrestaKit/scss/custom/_variables.scss";
  .table {
    font-size: .9em;
    table-layout: fixed;
    width: 100%;
    white-space: nowrap;
    thead {
      border:none;
      th {
        border:none;
        border-bottom: 2px solid $brand-primary;
        color: $gray-dark;
        padding: 10px 0;
        .material-icons {
          margin-left: 5px;
          vertical-align: middle;
        }
        &.thead-title {
          padding-left: 98px;
        }
        &:last-child {
          .material-icons {
            color: $gray-medium;
            margin-right: 5px;
          }
        }
      }
    }
    tbody {
      border: none;
      tr {
        border-bottom: $gray-light 1px solid;
      }
    }
  }
</style>
