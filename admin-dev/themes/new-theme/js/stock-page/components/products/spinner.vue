<template>
  <input :id="id" type="number" class="edit-qty" name="qty" value="0">
</template>

<script>
  import Spinner from './spinner';

  export default {
    props: ['value'],
    mounted() {
      $(`#${this.id}`).spinner();
    },
    computed: {
      id: function () {
        return `qty-${this.value}`;
      }
    }
  }
</script>

<style lang="sass?outputStyle=expanded" scoped>
  @import "~jquery-ui/themes/base/minified/jquery-ui.min.css";

</style>
