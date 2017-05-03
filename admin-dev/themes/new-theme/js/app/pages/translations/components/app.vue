<template>
  <div id="app" class="translations-app">
    <TranslationsHeader />
    <div class="container-fluid">
      <div class="row">
        <Search @search="onSearch" />
      </div>

      <div class="row">
        <Sidebar />
        <Principal />
      </div>
    </div>
  </div>
</template>

<script>
  import TranslationsHeader from './header/translations-header';
  import Search from './header/search';
  import Sidebar from './sidebar';
  import Principal from './principal';

  export default {
    name: 'app',
    methods: {
      onSearch(keywords) {
        let desc = this.$route.name === 'overview' ? '' : ' desc';
        this.$store.dispatch('updateKeywords', keywords);
        this.fetch(desc);
      },
    },
    components: {
      TranslationsHeader,
      Search,
      Sidebar,
      Principal
    },
  }
</script>

<style lang="sass?outputStyle=expanded">
  @import "~PrestaKit/scss/custom/_variables.scss";
  .header-toolbar {
    z-index: 0;
    height: 128px;
  }
  .translations-app {
    padding-top: 3em;
  }
</style>
