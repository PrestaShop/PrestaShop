<template>
  <div>
    <Tags class="form-control search search-input" :tags="tags" :placeholder="placeholder" />
  </div>
</template>

<script>
  import Tags from '../../utils/tags';
  export default {
    props: ['placeholder'],
    components: {
      Tags
    },
    data() {
      return {
        tags: []
      }
    }
  }
</script>

<style lang="sass?outputStyle=expanded" scoped>
  ::placeholder {
    font-size: 12px;
    font-style: italic;
  }
</style>
