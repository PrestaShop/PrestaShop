<template>
    <Tags :placeholder="placeholder" :tags="tags" @tags-change="handleChange" :klass="this.customClass" />
</template>

<script>
  import Tags from 'vue-tagsinput';

  export default {
    props:['tags','placeholder'],
    components: {
      Tags
    },
    data() {
      return {
        customClass: {
          container: 'tags-input search-input',
          input: 'input',
          gap: 'gap',
          tag: 'tag'
        }
      }
    },
    methods: {
      handleChange(index, text) {
        if (text) {
          this.tags.splice(index, 0, text);
        } else {
          this.tags.splice(index, 1);
          this.$emit('tagChange');
        }
      }
    }
  }
</script>
<style lang="sass?outputStyle=expanded">
  @import "~PrestaKit/scss/custom/_variables.scss";
  .tags-input {
    .tag {
      background: $brand-primary;
      color: white;
      padding: 2px 4px;
      border-radius: 0;
      font-weight: lighter;
      .hl-click {
          height: 100%;
          width: 15px;
      }
    }
    .gap:first-of-type .input {
      margin-left: -6px;
    }
    input.input {
      font-family: Open Sans, sans-serif;
      cursor: text;
      padding-left: 2px;
    }
  }
</style>