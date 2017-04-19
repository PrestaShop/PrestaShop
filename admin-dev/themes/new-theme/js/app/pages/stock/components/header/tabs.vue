<template>
  <ul class="nav nav-tabs" id="tab" role="tablist">
    <li class="nav-item">
      <a class="nav-link active" data-toggle="tab" href="#stock" role="tab">Stock</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-toggle="tab" href="#movement" role="tab">Movements</a>
    </li>
  </ul>
</template>

<style lang="sass?outputStyle=expanded" scoped>
  @import "~PrestaKit/scss/custom/_variables.scss";
  .nav-link.active, .nav-link.active:hover, .nav-link.active:focus {
    border-top: none;
    border-bottom: 3px solid $brand-primary;
  }
</style>
