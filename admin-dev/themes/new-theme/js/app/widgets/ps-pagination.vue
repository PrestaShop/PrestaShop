<template>
  <nav class="pull-xs-right m-t-1">
    <ul class="pagination" :class="{'multi':isMultiPagination}">
      <li v-if="isMultiPagination" class="page-item">
        <button v-show="activeLeftArrow" class="pull-left page-link" @click="prev($event)">
          <i class="material-icons">keyboard_arrow_left</i>
          <span class="sr-only">Previous</span>
        </button>
      </li>
      <li class="page-item" v-for="index in pagesCount">
        <button
          v-if="showIndex(index)"
          class="page-link"
          :class="{
            'current' : checkCurrentIndex(index),
            'p-l-0' : showFirstDots(index),
            'p-r-0' : showLastDots(index)
          }"
          @click.prevent="changePage(index)"
          >
          <span v-if="isMultiPagination" v-show="showFirstDots(index)">...</span>
          {{ index }}
          <span v-if="isMultiPagination" v-show="showLastDots(index)">...</span>
        </button>
      </li>
      <li v-if="isMultiPagination" class="page-item">
        <button v-show="activeRightArrow" class="pull-left page-link" @click="next($event)">
          <i class="material-icons">keyboard_arrow_right</i>
          <span class="sr-only">Next</span>
        </button>
      </li>
    </ul>
  </nav>
</template>

<script>
  export default {
    props: ['pagesCount', 'current'],
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
        this.currentIndex = pageIndex;
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
      currentIndex: 1,
      multiPagesToDisplay: 2,
      multiPagesActivationLimit: 5,
    }),
  };
</script>

<style lang="sass" scoped>
  @import "~PrestaKit/scss/custom/_variables.scss";
  .page-link, .page-item.active .page-link {
    background-color: transparent;
    text-decoration: none;
    &.current {
      color: $brand-primary;
    }
  }
  .page-link {
    outline: none;
    & .material-icons {
      background-color: transparent;
      vertical-align: middle;
    }
  }
</style>
