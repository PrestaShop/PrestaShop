<template>
  <form class="qty text-xs-right" :class="classObject" v-on:mouseover="focusIn" v-on:mouseleave="focusOut($event)">
    <input @keyup="onKeyup($event.target.value)" v-on:focus="focusIn" v-on:blur="focusOut($event)" :id="id" class="edit-qty" name="qty" v-model="qty" placeholder="0" >
    <transition name="fade">
      <button v-if="isActive" class="check-button" v-on:click="sendQty($event)"><i class="material-icons">check</i></button>
    </transition>
  </form>
</template>

<script>
  export default {
    props: ['product'],
    mounted() {
      let self = this;
      $(`#${this.id}`).spinner({
        max: 999999999,
        min: -999999999,
        spin(event, ui) {
          self.value = ui.value;
          self.isEnabled = !!self.value;
        }
      });
    },
    computed: {
      qty () {
        if(!this.product.qty) {
          this.isActive = this.isEnabled = false;
          this.value = this.product.qty = null;
        }
        return this.product.qty;
      },
      id () {
        return `qty-${this.product.product_id}-${this.product.combination_id}`;
      },
      classObject() {
        return {
          active: this.isActive,
          disabled: !this.isEnabled
        }
      }
    },
    data() {
      return {
        value: null,
        isActive: false,
        isEnabled: false
      }
    },
    watch: {
      value(val) {
        console.log(this.product.product_id,this.product.combination_id)
        this.$store.commit('UPDATE_PRODUCT_QTY', {
          product_id: this.product.product_id,
          combination_id: this.product.combination_id,
          delta: val
        });
      }
    },
    methods: {
      onKeyup(val) {
        this.value = val;
        if(this.value) {
          this.isActive = this.isEnabled = true;
        }
      },
      focusIn() {
        this.isActive = true;
      },
      focusOut(event) {
        if(!$(event.relatedTarget).hasClass('check-button') && !this.value) {
          this.isActive = false;
        }
        this.isEnabled = !!this.value;
      },
      sendQty(event) {
        let apiRootUrl = data.apiRootUrl.replace(/\?.*/,'');
        let apiEditProductsUrl = `${apiRootUrl}/product/${this.product.product_id}`
        let apiEditCombinationsUrl = `${apiRootUrl}/product/${this.product.product_id}/combination/${this.product.combination_id}`;
        let postUrl = this.product.combination_id ? apiEditCombinationsUrl : apiEditProductsUrl;

        event.preventDefault();

        // POST when qty !=0
        if(this.isEnabled) {
          this.$store.dispatch('updateQtyByProductId', {
            url: postUrl,
            delta: this.value
          });
          this.isActive = this.isEnabled = false;
          this.value = null;
        }
      }
    }
  }
</script>

<style lang="sass?outputStyle=expanded" scoped>
  @import "~jquery-ui/themes/base/minified/jquery.ui.spinner.min.css";
  @import "~PrestaKit/scss/custom/_variables.scss";
  *{
    outline: none;
  }
  .qty {
      position: relative;
      width: 120px;
      .check-button {
        outline:none;
        opacity: 0;
        position: absolute;
        top: 4px;
        right: 0;
        border: none;
        height: 29px;
        width: 40px;
        background: $brand-primary;
        z-index: 2;
        border-left: 10px solid white;
        .material-icons {
          color: white;
          vertical-align: middle;
        }
        &:hover {
          background: $primary-hover;
        }
      }
  }
  .qty.active {
    .check-button {
      opacity: 1;
    }
  }
  .qty.disabled {
    .check-button {
      background: $gray-light;
      cursor: default;
    }
  }
  .fade-enter-active, .fade-leave-active {
    transition: opacity 0.2s ease;
  }
  .fade-enter, .fade-leave-to {
    opacity: 0
  }
  .edit-qty {
    text-indent: 5px;
    height: 31px;
    width: 70px;
    border: 1px solid $gray-light;
    margin-right: 0;
  }
</style>
