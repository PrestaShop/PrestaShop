<!--**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *-->
<template>
  <nav class="float-sm-right mt-1 mr-2" v-if="displayPagination">
    <ul class="pagination" :class="{'multi':isMultiPagination}">
      <li v-if="isMultiPagination" class="page-item previous">
        <a v-show="activeLeftArrow" class="float-left page-link" @click="prev($event)" href="#">
          <span class="sr-only">Previous</span>
        </a>
      </li>
      <li class="page-item" :class="{'active' : checkCurrentIndex(index)}" v-for="index in pagesCount">
        <a
          v-if="showIndex(index)"
          class="page-link"
          :class="{
            'pl-0' : showFirstDots(index),
            'pr-0' : showLastDots(index)
          }"
          @click.prevent="changePage(index)"
          href="#"
          >
          <span v-if="isMultiPagination" v-show="showFirstDots(index)">...</span>
          {{ index }}
          <span v-if="isMultiPagination" v-show="showLastDots(index)">...</span>
        </a>
      </li>
      <li v-if="isMultiPagination" class="page-item next">
        <a v-show="activeRightArrow" class="float-left page-link" @click="next($event)" href="#">
          <span class="sr-only">Next</span>
        </a>
      </li>
    </ul>
  </nav>
</template>

<script>
  export default {
    props: ['pagesCount', 'currentIndex'],
    computed: {
      isMultiPagination() {
        return this.pagesCount > this.multiPagesActivationLimit;
      },
      activeLeftArrow() {
        return this.currentIndex !== 1;
      },
      activeRightArrow() {
        return this.currentIndex !== this.pagesCount;
      },
      pagesToDisplay() {
        return this.multiPagesToDisplay;
      },
      displayPagination() {
        return this.pagesCount > 1;
      },
    },
    methods: {
      checkCurrentIndex(index) {
        return this.currentIndex === index;
      },
      showIndex(index) {
        const startPaginationIndex = index < this.currentIndex + this.multiPagesToDisplay;
        const lastPaginationIndex = index > this.currentIndex - this.multiPagesToDisplay;
        const indexToDisplay = startPaginationIndex && lastPaginationIndex;
        const lastIndex = index === this.pagesCount;
        const firstIndex = index === 1;
        if (!this.isMultiPagination) {
          return !this.isMultiPagination;
        }
        return indexToDisplay || firstIndex || lastIndex;
      },
      changePage(pageIndex) {
        this.$emit('pageChanged', pageIndex);
      },
      showFirstDots(index) {
        const pagesToDisplay = this.pagesCount - this.multiPagesToDisplay;
        if (!this.isMultiPagination) {
          return this.isMultiPagination;
        }
        return index === this.pagesCount && this.currentIndex <= pagesToDisplay;
      },
      showLastDots(index) {
        if (!this.isMultiPagination) {
          return this.isMultiPagination;
        }
        return index === 1 && this.currentIndex > this.multiPagesToDisplay;
      },
      prev() {
        if (this.currentIndex > 1) {
          this.changePage(this.currentIndex - 1);
        }
      },
      next() {
        if (this.currentIndex < this.pagesCount) {
          this.changePage(this.currentIndex + 1);
        }
      },
    },
    data: () => ({
      multiPagesToDisplay: 2,
      multiPagesActivationLimit: 5,
    }),
  };
</script>
