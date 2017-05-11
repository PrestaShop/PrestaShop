<template>
  <div class="m-b-1">
    <small>
      <a :href="catalogLink">{{trans('link_catalog')}}</a> /
      <a :href="stockLink">{{trans('link_stock')}}</a> /
      <span v-if="isOverview">{{trans('link_overview')}}</span>
      <span v-else>{{trans('link_movements')}}</span>
    </small>
  </div>
</template>

<script>
  export default {
    computed: {
      isOverview() {
        return this.$route.name === 'overview';
      },
      catalogLink() {
        return data.catalogUrl;
      },
      stockLink() {
        return data.stockUrl;
      }
    }
  }
</script>