<template>
  <div class="col-xs-9">
    <div class="card p-a-1">

    </div>
  </div>
</template>

<script>
  export default {
      mounted () {
          this.$store.dispatch('getCatalog');
      }
  }
</script>
