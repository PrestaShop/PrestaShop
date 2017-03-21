<template>
  <nav class="pull-xs-right m-t-1">
    <ul class="multi pagination">
      <li v-for="n in pagesCount" class="page-item">
        <PageIndex :index="n" :current="currentIndex" v-on:pageChanged="onPageChanged" />
      </li>
    </ul>
  </nav>
</template>

<script>
import PageIndex from './page-index';
  const DEFAULT_LINE_NUMBER = 10;
  export default {
    computed: {
      pagesCount() {
        //TODO GET NUMBER OF PAGES
        return 10;
      }
    },
    methods: {
      onPageChanged(pageIndex) {
        this.currentIndex = pageIndex;
        this.$store.dispatch('getStock', {
          url: window.data.apiRootUrl.replace(/\?.*/,''),
          order: this.$store.state.order,
          page_size: DEFAULT_LINE_NUMBER,
          page_index: pageIndex
        });
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
    &.current {
      color: $brand-primary;
    }
  }
</style>
