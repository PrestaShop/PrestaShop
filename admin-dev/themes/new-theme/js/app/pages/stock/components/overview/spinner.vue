<template>
  <form
    class="qty text-xs-right"
    :class="classObject"
    @mouseover="focusIn"
    @mouseleave="focusOut($event)"
    @submit.prevent="sendQty($event)"
  >
    <input
      name="qty"
      class="edit-qty"
      placeholder="0"
      :id="id"
      :model="qty"
      @keyup="onKeyup($event.target.value)"
      @focus="focusIn"
      @blur="focusOut($event)"
    >
    <transition name="fade">
      <button v-if="isActive" class="check-button"><i class="material-icons">check</i></button>
    </transition>
  </form>
</template>

<script>
  export default {
    props: ['product'],
    mounted() {
      let self = this;
      $(`#${this.id}`).spinner({
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
        this.$store.commit('UPDATE_PRODUCT_QTY', {
          product_id: this.product.product_id,
          combination_id: this.product.combination_id,
          delta: val
        });
      }
    },
    methods: {
      onKeyup(val) {
        let validChars = /^[-]?\d*$/g;
        let invalidChars = /[^-0-9]/g;
        let invalidChars2 = /[^0-9]/g;

        if(!validChars.test(val)) {
          let firstLetter = val.charAt(0).replace(invalidChars,'');
          let lastChars = val.substring(1,val.length).replace(invalidChars2,'');
          return  $(`#${this.id}`).val(firstLetter.concat(lastChars));
        }
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

        // POST when qty !=0

        if(this.product.qty && !isNaN(this.product.qty)) {
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
        top: 3.5px;
        right: 0;
        border: none;
        height: 31px;
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
    height: 33px;
    width: 70px;
    border: 1px solid $gray-light;
    margin-right: 0;
  }
</style>
