<template>
  <section>
    <PSTable class="m-t-1">
      <thead>
        <tr>
          <th width="40%">
            Product
            <PSSort order="product" :isDesc="isSorted" @sort="toggleSort" />
          </th>
          <th>
            Reference
            <PSSort order="reference" :isDesc="isSorted" @sort="toggleSort" />
          </th>
          <th>
            Movements type
          </th>
          <th class="text-xs-center">
            Quantity
          </th>
          <th class="text-xs-center">
            Date & time
            <PSSort order="date_add" :isDesc="isSorted" @sort="toggleSort" />
          </th>
          <th>
            Employee
          </th>
        </tr>
      </thead>
      <tbody>
        <PSAlert v-if="emptyMovements">
          No product matches your search. Try changing search terms.
        </PSAlert>
        <MovementLine v-for="(movement, index) in movements" key=${index} :movement="movement" />
      </tbody>
    </PSTable>
  </section>
</template>

<script>
  import PSTable from 'app/widgets/ps-table/ps-table';
  import PSSort from 'app/widgets/ps-table/ps-sort';
  import PSAlert from 'app/widgets/ps-alert';
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
      fetch(desc) {
        this.$store.dispatch('getMovements', {
          order: `${this.$store.getters.order}${desc}`,
          page_size: this.$store.state.productsPerPage,
          page_index: this.$store.getters.pageIndex
        });
      },
      toggleSort(order, desc) {
        this.isSorted = !this.isSorted;
        this.$store.dispatch('updateOrder', order);
        this.fetch(desc);
      }
    },
    mounted() {
      this.$store.dispatch('updateOrder', 'date_add');
      this.fetch(DEFAULT_SORT);
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
      MovementLine
    }
  }
</script>