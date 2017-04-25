<template>
  <section>
    <PSTable class="m-t-1">
      <thead>
        <tr>
          <th width="30%">
            {{trans('title_product')}}
            <PSSort order="product" :isDesc="isSorted" @sort="toggleSort" />
          </th>
          <th>
            {{trans('title_reference')}}
            <PSSort order="reference" :isDesc="isSorted" @sort="toggleSort" />
          </th>
          <th>
            {{trans('title_movements_type')}}
          </th>
          <th class="text-xs-center">
            {{trans('title_quantity')}}
          </th>
          <th class="text-xs-center">
            {{trans('title_date')}}
            <PSSort order="date_add" :isDesc="isSorted" @sort="toggleSort" />
          </th>
          <th>
            {{trans('title_employee')}}
          </th>
        </tr>
      </thead>
      <tbody>
        <PSAlert v-if="emptyMovements">
           {{trans('no_product')}}
        </PSAlert>
        <MovementLine v-for="(product, index) in movements" key=${index} :product="product" />
      </tbody>
    </PSTable>
  </section>
</template>

<script>
  import PSTable from 'app/widgets/ps-table/ps-table';
  import PSSort from 'app/widgets/ps-table/ps-sort';
  import PSAlert from 'app/widgets/ps-alert';
  import PSPagination from 'app/widgets/ps-pagination/ps-pagination';
  import MovementLine from './movement-line';

  const DEFAULT_SORT = ' desc';

  export default {
    computed: {
      movements() {
        return this.$store.getters.movements;
      },
      emptyMovements() {
        return !this.$store.getters.movements.length;
      }
    },
    methods: {
      toggleSort(order, desc) {
        this.isSorted = !this.isSorted;
        this.$store.dispatch('updateOrder', order);
        this.$emit('fetch', desc);
      }
    },
    mounted() {
      this.$store.dispatch('updateOrder', 'date_add');
      this.$emit('fetch', DEFAULT_SORT);
    },
    data() {
      return {
        isSorted: true
      }
    },
    components: {
      PSTable,
      PSSort,
      PSAlert,
      PSPagination,
      MovementLine
    }
  }
</script>