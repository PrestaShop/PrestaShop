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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
      font-size: 1rem;
      padding: 0.5rem;
      transition: 0.25s ease-out;
      cursor: pointer;
      color: #6c868e;
      border: 0;
      background-color: inherit;

      &:disabled {
        cursor: not-allowed;
        opacity: 0.5;
      }

      &:hover:not(:disabled) {
        color: $primary;
      }
    }

    &.active {
      button {
        color: $primary;
      }
    }
  }

  &-previous,
  &-next {
    font-size: 1.25rem;
  }
}
</style>
