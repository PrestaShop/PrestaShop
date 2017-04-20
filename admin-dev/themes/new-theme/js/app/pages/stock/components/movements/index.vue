<template>
  <section>
    <PSTable class="m-t-1">
      <thead>
        <tr>
          <th>
            Product
          </th>
          <th>
            Reference
          </th>
          <th>
            Movements type
          </th>
          <th>
            Quantity
          </th>
          <th>Date & time</th>
          <th>
            Employee
          </th>
        </tr>
      </thead>
      <tbody>
        <PSAlert v-if="emptyMovements">
          No product matches your search. Try changing search terms.
        </PSAlert>
        <MovementLine v-for="(movement, index) in movements" key=${index} :product="product" />
      </tbody>
    </PSTable>
  </section>
</template>

<script>
  import PSTable from 'app/widgets/ps-table';
  import PSAlert from 'app/widgets/ps-alert';

  export default {
    computed: {
      movements() {
        return this.$store.getters.movements;
      },
      emptyMovements() {
        return !!this.movements.length;
      }
    },
    components: {
      PSTable
    }
  }
</script>