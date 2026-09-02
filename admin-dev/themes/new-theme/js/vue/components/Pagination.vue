<!--*
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *-->
<template>
  <div class="pagination">
    <ul class="pagination-list">
      <li class="pagination-item pagination-previous">
        <button
          @click="goToPage(currentPage - 1)"
          :disabled="currentPage === 1"
        >
          <i class="material-icons rtl-flip">chevron_left</i>
        </button>
      </li>
      <li
        :class="['pagination-item', isActive(key)]"
        v-for="(page, key) of paginatedDatas"
        :key="key"
      >
        <button @click="goToPage(key + 1)">
          {{ key + 1 }}
        </button>
      </li>
      <li class="pagination-item pagination-next">
        <button
          @click="goToPage(currentPage + 1)"
          :disabled="currentPage === paginatedDatas.length"
        >
          <i class="material-icons rtl-flip">chevron_right</i>
        </button>
      </li>
    </ul>
  </div>
</template>

<script lang="ts">
  import {defineComponent} from 'vue';

  export default defineComponent({
    name: 'Pagination',
    data(): {paginatedDatas: Array<Record<string, any>>, currentPage: number} {
      return {
        paginatedDatas: [],
        currentPage: 1,
      };
    },
    props: {
      datas: {
        type: Array as () => Array<Record<string, any>>,
        default: () => [],
      },
      paginationLength: {
        type: Number,
        default: 14,
      },
    },
    methods: {
      /**
       * Used to switch page state of the pagination
       *
       * @param {int} pageNumber
       */
      goToPage(pageNumber: number): void {
        if (this.paginatedDatas[pageNumber - 1]) {
          this.currentPage = pageNumber;
          this.$emit('paginated', {
            paginatedDatas: this.paginatedDatas,
            currentPage: this.currentPage,
          });
        }
      },
      /**
       * Split items into chunks based on paginationLength
       *
       * @param {array} newDatas
       */
      constructDatas(newDatas: Array<Record<string, any>>): void {
        this.paginatedDatas = [];

        for (let i = 0; i < newDatas.length; i += this.paginationLength) {
          this.paginatedDatas.push(newDatas.slice(i, i + this.paginationLength));
        }

        this.$emit('paginated', {
          paginatedDatas: this.paginatedDatas,
          currentPage: this.currentPage,
        });
      },
      /**
       * Avoid too much logics in the template
       *
       * @param {int} key
       */
      isActive(key: number): string | null {
        return this.currentPage === key + 1 ? 'active' : null;
      },
    },
    /**
     * On mount, split datas into chunks
     */
    mounted() {
      this.constructDatas(this.datas);
    },
    watch: {
      /**
       * On datas change, split into chunks again.
       *
       * @param {array} newDatas
       */
      datas(newDatas: Array<Record<string, any>>): void {
        this.constructDatas(newDatas);
      },
    },
  });
</script>

<style lang="scss" type="text/scss">
@import "~@scss/config/_settings.scss";

.pagination {
  &-list {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    margin: 0;
    width: 100%;
  }

  &-item {
    list-style-type: none;

    button {
      font-size: var(--#{$cdk}size-16);
      padding: var(--#{$cdk}size-8);
      transition: 0.25s ease-out;
      cursor: pointer;
      color: var(--#{$cdk}primary-600);
      border: 0;
      background-color: inherit;

      &:disabled {
        cursor: not-allowed;
        opacity: 0.5;
      }

      &:hover:not(:disabled) {
        color: var(--#{$cdk}primary-800);
      }
    }

    &.active {
      button {
        color: var(--#{$cdk}primary-800);
      }
    }
  }

  &-previous,
  &-next {
    font-size: var(--#{$cdk}size-20);
  }
}
</style>
