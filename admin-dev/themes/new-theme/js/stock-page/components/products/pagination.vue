<template>
  <nav class="pull-xs-right m-t-1">
    <ul class="multi pagination">
      <li v-for="n in pagesCount" class="page-item"><a v-on:click="changePage($event, n)" class="page-link" href="#">{{ n }}</a></li>
    </ul>
  </nav>
</template>

<script>
  const DEFAULT_LINE_NUMBER = 10;
  export default {
    computed: {
      pagesCount() {
        //TODO GET NUMBER OF PAGES
        return 10;
      }
    },
    methods: {
      changePage(event, pageIndex) {
        event.preventDefault();
        this.$store.dispatch('getStock', {
          url: window.data.apiRootUrl.replace(/\?.*/,''),
          order: this.$store.state.order,
          page_size: DEFAULT_LINE_NUMBER,
          page_index: pageIndex
        });
      }
    }
  }
</script>

<style lang="sass?outputStyle=expanded" scoped>
  .page-link, .page-item.active .page-link {
    background-color: transparent;
  }
</style>
