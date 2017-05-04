<template>
  <nav class="pull-xs-right m-t-1">
    <ul class="pagination" :class="{'multi':isMulti}">
      <li v-if="isMulti" v-show="activeLeftArrow" class="page-item">
        <a class="pull-left arrow js-arrow" href="#" aria-label="Previous" @click="prev($event)">
          <i class="material-icons">keyboard_arrow_left</i>
          <span class="sr-only">Previous</span>
        </a>
      </li>
      <li class="page-item" v-for="n in pagesCount">
        <PSPageIndex
            :pagesToDisplay="pagesToDisplay"
            :total="pagesCount"
            :isMulti="isMulti"
            :index="n"
            :current="current"
            @pageChanged="onPageChanged"
        />
      </li>
      <li v-if="isMulti" v-show="activeRightArrow" class="page-item">
        <a class="pull-left arrow js-arrow" href="#" aria-label="Next" @click="next($event)">
          <i class="material-icons">keyboard_arrow_right</i>
          <span class="sr-only">Next</span>
        </a>
      </li>
    </ul>
  </nav>
</template>

<script>
  import PSPageIndex from './ps-page-index';

  export default {
    props: ['pageNumber', 'activeMultiPagination', 'pagesCount', 'current'],
    computed: {
      isMulti() {
        return this.pagesCount > this.activeMultiPagination;
      },
      activeLeftArrow() {
        if(this.currentIndex === 1) {
          return false;
        }
        return true;
      },
      activeRightArrow() {
        if(this.currentIndex === this.pagesCount) {
          return false;
        }
        return true;
      },
      pagesToDisplay() {
        return this.pageNumber;
      }
    },
    methods: {
      onPageChanged(pageIndex) {
        this.currentIndex = pageIndex;
        this.$emit('pageChanged', pageIndex);
      },
      prev(event) {
        event.preventDefault();
        if(this.currentIndex > 1) {
          this.onPageChanged(this.currentIndex - 1);
        }
      },
      next(event) {
        event.preventDefault();
        if(this.currentIndex < this.pagesCount) {
          this.onPageChanged(this.currentIndex + 1);
        }
      }
    },
    components: {
      PSPageIndex
    },
    data() {
      return {
        currentIndex: this.current
      }
    }
  }
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
</style>
