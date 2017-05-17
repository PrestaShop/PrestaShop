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
        <tr v-if="this.isLoading">
          <td colspan="7">
            <PSLoader v-for="(n, index) in 3" class="m-t-1" :key="index">
              <div class="background-masker header-top"></div>
              <div class="background-masker header-left"></div>
              <div class="background-masker header-bottom"></div>
              <div class="background-masker subheader-left"></div>
              <div class="background-masker subheader-bottom"></div>
            </PSLoader>
          </td>
        </tr>
        <tr v-else-if="emptyMovements">
          <td colspan="7">
            <PSAlert alertType="info" :hasClose="false">
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

  const DEFAULT_SORT = ' desc';

  export default {
    computed: {
      isLoading() {
        return this.$store.getters.isLoading;
      },
      movements() {
        return this.$store.getters.movements;
      },
      emptyMovements() {
        if (this.$store.getters.movements) {
          return !this.$store.getters.movements.length;
        }
        return null;
      },
    },
    methods: {
      toggleSort(order, desc) {
        this.isSorted = !this.isSorted;
        this.$store.dispatch('updateOrder', order);
        this.$emit('fetch', desc);
      },
    },
    mounted() {
      this.$store.dispatch('updatePageIndex', 1);
      this.$store.dispatch('updateKeywords', []);
      this.$store.dispatch('getEmployees');
      this.$store.dispatch('getMovementsTypes');
      this.$store.dispatch('updateOrder', 'date_add');
      this.$emit('fetch', DEFAULT_SORT);
    },
    components: {
      PSTable,
      PSSort,
      PSAlert,
      PSLoader,
      MovementLine,
    },
    data: () => ({ isSorted: true }),
  };
</script>
