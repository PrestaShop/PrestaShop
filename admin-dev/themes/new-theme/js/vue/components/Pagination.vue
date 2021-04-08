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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *-->
<template>
  <div class="pagination">
    <ul class="pagination-list">
      <li
        class="pagination-item pagination-previous"
        @click="goToPage(currentPage - 1)"
      >
        <i class="material-icons">chevron_left</i>
      </li>
      <li
        :class="['pagination-item', currentPage === key + 1 ? 'active' : null]"
        v-for="(page, key) of paginatedDatas"
        :key="key"
        @click="goToPage(key + 1)"
      >
        {{ key + 1 }}
      </li>
      <li
        class="pagination-item pagination-next"
        @click="goToPage(currentPage + 1)"
      >
        <i class="material-icons">chevron_right</i>
      </li>
    </ul>
  </div>
</template>

<script>
  export default {
    name: 'Pagination',
    data() {
      return {
        paginatedDatas: [],
        currentPage: 1,
      };
    },
    props: {
      datas: {
        type: Array,
        default: () => [],
      },
      paginationLength: {
        type: Number,
        default: 14,
      },
    },
    methods: {
      goToPage(pageNumber) {
        if (this.paginatedDatas[pageNumber - 1]) {
          this.currentPage = pageNumber;
          this.$emit('paginated', {paginatedDatas: this.paginatedDatas, currentPage: this.currentPage});
        }
      },
      constructDatas(newDatas) {
        this.paginatedDatas = [];

        for (let i = 0; i < newDatas.length; i += this.paginationLength) {
          this.paginatedDatas.push(newDatas.slice(i, i + this.paginationLength));
        }

        this.$emit('paginated', {paginatedDatas: this.paginatedDatas, currentPage: this.currentPage});
      },
    },
    mounted() {
      this.constructDatas(this.datas);
    },
    watch: {
      datas(newDatas) {
        this.constructDatas(newDatas);
      },
    },
  };
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
    font-size: 1rem;
    padding: .5rem;
    transition: .25s ease-out;
    cursor: pointer;
    color: #6C868E;

    &:hover {
      color: $primary;
    }

    &.active {
      color: $primary;
    }
  }

  &-previous, &-next {
    font-size: 1.25rem;
  }
}
</style>
