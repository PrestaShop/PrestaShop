<template>
    <Tags ref="tags" :class="{ 'has-tags' : hasTags }" id="tags" :placeholder="placeholder" :tags="tags" @tags-change="handleChange" :klass="this.customClass" />
</template>

<script>
  import Tags from 'vue-tagsinput';

  export default {
    props:['tags','placeholder'],
    computed: {
      hasTags() {
        return !!this.tags.length;
      }
    },
    methods: {
      handleChange(index, text) {
        let tag = this.tags[index];
        let splice = true;
        if (text) {
          this.tags.splice(index, 0, text);
        } else {
          splice = false
          this.tags.splice(index, 1);
        }
        this.$emit('tagChange', this.tags, index, tag, splice);
      }
    },
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
    }
  }
</script>
<style lang="sass?outputStyle=expanded">
  @import "~PrestaKit/scss/custom/_variables.scss";
  #tags {
    &.tags-input {
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
      .gap:last-of-type .input {
        min-width:20px !important;
      }
      &.has-tags .gap span {
        display: none;
      }
      input.input {
        font-family: Open Sans, sans-serif;
        cursor: text;
        padding-left: 2px;
        background-color: white;
      }
    }
  }
</style>