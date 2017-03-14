<template>
  <input :id="id" class="edit-qty" name="qty" v-model="value" >
</template>

<script>
  import Spinner from './spinner';

  export default {
    props: ['productId'],
    mounted() {
      let self = this;
      $(`#${this.id}`).spinner({
        max: 999999999,
        min: -999999999,
        spin(event, ui) {
          self.$store.commit('updateQty', {
            value: ui.value,
            productId: self.productId
          });
        }
      });
    },
    computed: {
      id () {
        return `qty-${this.productId}`;
      }
    },
    data() {
      return {
        value: 0
      }
    }
  }
</script>

<style lang="sass?outputStyle=expanded" scoped>
  @import "~jquery-ui/themes/base/minified/jquery.ui.spinner.min.css";
  .edit-qty {
    text-indent: 5px;
  }
</style>
