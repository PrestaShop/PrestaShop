<template>
  <nav class="pull-xs-right m-t-1">
    <ul class="pagination" :class="{'multi':isMulti}">
      <li v-if="isMulti" v-show="activeLeftArrow" class="page-item">
        <a class="pull-left arrow js-arrow" href="#" aria-label="Previous" v-on:click="prev($event)">
          <i class="material-icons">keyboard_arrow_left</i>
          <span class="sr-only">Previous</span>
        </a>
      </li>
      <li class="page-item" v-for="n in pagesCount">
        <PageIndex :pagesToDisplay="pagesToDisplay" :total="pagesCount" :isMulti="isMulti" :index="n" :current="currentIndex" v-on:pageChanged="onPageChanged" />
      </li>
      <li v-if="isMulti" v-show="activeRightArrow" class="page-item">
        <a class="pull-left arrow js-arrow" href="#" aria-label="Next" v-on:click="next($event)">
          <i class="material-icons">keyboard_arrow_right</i>
          <span class="sr-only">Next</span>
        </a>
      </li>
    </ul>
  </nav>
</template>

<script>
import PageIndex from './page-index';
  const DEFAULT_PAGES_NUMBER = 3;
  const ACTIVE_MULTI_PAGINATION_NUMBER = 5;
  export default {
    computed: {
      pagesCount() {
        return this.$store.getters.totalPages;
      },
      isMulti() {
        return this.pagesCount > ACTIVE_MULTI_PAGINATION_NUMBER;
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
        return DEFAULT_PAGES_NUMBER;
      }
    },
    methods: {
      onPageChanged(pageIndex) {
        this.currentIndex = pageIndex;
        this.$store.dispatch('getStock', {
          url: window.data.apiRootUrl.replace(/\?.*/,''),
          order: this.$store.state.order,
          page_size: this.$store.state.productsPerPage,
          page_index: pageIndex
        });
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
      PageIndex
    },
    data() {
      return {
        currentIndex: 1
      }
    }
  }
</script>

<style lang="sass?outputStyle=expanded" scoped>
  @import "~PrestaKit/scss/custom/_variables.scss";
  .page-link, .page-item.active .page-link {
    background-color: transparent;
    text-decoration: none;
    &.current {
      color: $brand-primary;
    }
  }
</style>
