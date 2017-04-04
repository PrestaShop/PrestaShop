<template>
  <div>
    <input type="text" class="form-control search search-input"  autocomplete="off" :placeholder="placeholder">
  </div>
</template>

<script>
  export default {
    props: ['placeholder']
  }
</script>

<style lang="sass?outputStyle=expanded" scoped>
  ::placeholder {
    font-size: 12px;
    font-style: italic;
  }
</style>
