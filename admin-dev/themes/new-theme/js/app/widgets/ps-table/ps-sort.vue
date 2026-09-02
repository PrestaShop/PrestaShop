<!--*
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *-->
<template>
  <div
    class="ps-sortable-column"
    :data-sort-col-name="this.order"
    :data-sort-is-current="isCurrent"
    :data-sort-direction="sortDirection"
    @click="sortToggle"
  >
    <span role="columnheader"><slot /></span>
    <span
      role="button"
      class="ps-sort"
      aria-label="Tri"
    />
  </div>
</template>

<script lang="ts">
  import {defineComponent} from 'vue';

  export default defineComponent({
    props: {
      // column name
      order: {
        type: String,
        required: true,
      },
      // indicates the currently sorted column in the table
      currentSort: {
        type: String,
        required: true,
      },
    },
    methods: {
      sortToggle(): void {
        // toggle direction
        this.sortDirection = (this.sortDirection === 'asc') ? 'desc' : 'asc';
        this.$emit('sort', this.order, this.sortDirection);
      },
    },
    data() {
      return {
        sortDirection: 'desc',
      };
    },
    computed: {
      isCurrent(): boolean {
        return this.currentSort === this.order;
      },
    },
  });
</script>
