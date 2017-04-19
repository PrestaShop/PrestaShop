<template>
  <div class="tags-input search-input" @click="focus()">
    <span v-for="(tag, index) in tags" class="input-tag">
      <span class="tag">{{ tag }}<i class="material-icons" @click="close(index)">close</i></span>
    </span>
    <input
      ref = "tags"
      :placeholder="placeholder"
      type="text"
      v-model="tag"
      class="input"
      @keyup="onKeyUp"
      @keydown.enter="add(tag)"
      @keydown.delete.stop="remove()"
    />
  </div>
</template>

<script>

  export default {
    props:['tags','placeholder'],
    methods: {
      onKeyUp() {
        this.$emit('typing', this.$refs.tags.value);
      },
      add(tag) {
        this.tags.push(tag);
        this.tag = '';
        this.focus();
        this.$emit('tagChange', this.tag);
      },
      close(index) {
        this.$emit('tagChange', this.tags[index]);
        this.tags.splice(index, 1);
      },
      remove() {
        this.tags.pop();
        this.$emit('tagChange', this.tag);
      },
      focus() {
        this.$refs.tags.focus();
      }
    },
    data() {
      return {
        tag: ''
      }
    }
  }
</script>
<style lang="sass?outputStyle=expanded">
  @import "~PrestaKit/scss/custom/_variables.scss";
  .tags-input {
    padding: 4px;
    .tag {
      background: $brand-primary;
      color: white;
      padding: 2px 4px;
      border-radius: 0;
      font-weight: lighter;
      display: inline-block;
      margin: 2px;
      min-height: 25px;
      .material-icons {
        vertical-align: middle;
        color: $gray;
        margin: 0 2px 0 5px;
        cursor: pointer;
      }
    }
    input.input {
      font-family: Open Sans, sans-serif;
      cursor: text;
      padding-left: 2px;
      border: none;
      outline: none;
    }
  }
</style>