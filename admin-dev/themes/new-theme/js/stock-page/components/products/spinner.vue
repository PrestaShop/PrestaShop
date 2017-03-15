<template>
  <form>
    <input v-on:focus="focusIn" v-on:blur="focusOut" :id="id" class="edit-qty" name="qty" v-model="value" >
  </form>
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
          self.value = ui.value;
          self.$emit('enabled', self.value);
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
    },
    methods: {
      focusIn(){
        this.$emit('focusIn');
      },
      focusOut() {
        this.$emit('focusOut');
      }
    }
  }
</script>

<style lang="sass?outputStyle=expanded" scoped>
  @import "~jquery-ui/themes/base/minified/jquery.ui.spinner.min.css";
  @import "~PrestaKit/scss/custom/_variables.scss";
  .edit-qty {
    text-indent: 5px;
    height: 31px;
    width: 70px;
    border: 1px solid $gray-light;
  }
</style>
