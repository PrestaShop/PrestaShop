<template>
  <ul class="nav nav-tabs" id="tab" role="tablist">
    <li class="nav-item">
      <router-link data-toggle="tab" class="nav-link" :class="{active : isOverview}" to="/" role="tab">{{trans('menu_stock')}}</router-link>
    </li>
    <li class="nav-item">
       <router-link data-toggle="tab" class="nav-link" :class="{active : isMovements}" to="/movements" role="tab">{{trans('menu_movements')}}</router-link>
    </li>
  </ul>
</template>

<script>
  export default {
    computed: {
      isOverview() {
        return this.$route.name === 'overview';
      },
      isMovements() {
        return this.$route.name === 'movements';
      },
    },
  };
</script>

<style lang="sass" scoped>
  @import "~PrestaKit/scss/custom/_variables.scss";
  .nav-item {
    cursor: pointer;
  }
  .nav-link.active, .nav-link.active:hover, .nav-link.active:focus {
    border-top: none;
    border-bottom: 3px solid $brand-primary;
  }
</style>
