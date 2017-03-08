<template>
  <div>
    <Breadcrumb />
    <h1>Stock Management</h1>
    <Tabs />
    <Search />
  </div>
</template>

<script>
  import Breadcrumb from './breadcrumb';
  import Tabs from './tabs';
  import Search from './search';

  export default {
    components: {
      Breadcrumb,
      Tabs,
      Search
    }
  }
</script>
