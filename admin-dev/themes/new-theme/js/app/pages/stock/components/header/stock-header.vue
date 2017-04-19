<template>
  <div class="stock-header">
    <Breadcrumb />
    <h1>Stock Management</h1>
    <Tabs />
  </div>
</template>

<script>
  import Breadcrumb from './breadcrumb';
  import Tabs from './tabs';

  export default {
    components: {
      Breadcrumb,
      Tabs
    }
  }
</script>

<style lang="sass?outputStyle=expanded" scoped>
  .stock-header {
    position: fixed;
    top: 40px;
    background: white;
    width: 100%;
    z-index: 2;
  }
</style>
