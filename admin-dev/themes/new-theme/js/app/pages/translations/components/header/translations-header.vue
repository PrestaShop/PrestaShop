<template>
  <div class="translations-header">
    <Breadcrumb />
    <h1>{{trans('head_title')}}</h1>
  </div>
</template>

<script>
  import Breadcrumb from './breadcrumb';

  export default {
    components: {
      Breadcrumb
    }
  }
</script>

<style lang="sass?outputStyle=expanded" scoped>
  @import "~PrestaKit/scss/custom/_variables.scss";
  .translations-header {
    position: fixed;
    top: 40px;
    background: white;
    width: 100%;
    z-index: 2;
    border-bottom: 1px solid $gray-light;
    height: 128px;
  }
</style>
