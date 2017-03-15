<template>
  <tr>
    <td>
      <input type="checkbox" :value="product.product_id" class="m-r-1">
      <img :src="imagePath" class="thumbnail" />
      <span class="m-l-1">{{ product.product_name }}</span>
    </td>
    <td class="text-xs-center p-r-1">
      {{ product.product_reference }}
    </td>
    <td class="p-r-1">
      {{ product.supplier_name }}
    </td>
    <td class="text-xs-center p-r-1">
      {{ product.product_available_quantity + product.product_reserved_quantity }}
    </td>
    <td class="text-xs-center">
      {{ product.product_reserved_quantity }}
    </td>
    <td class="text-xs-center">
      {{ product.product_available_quantity }}
    </td>
    <td class="text-xs-center">
      <div class="qty" :class="classObject" >
        <Spinner :productId="product.product_id" v-on:focusIn="toggleCheck" v-on:focusOut="toggleCheck" v-on:enabled="toggleEnabled"/>
        <button class="check-button" v-on:click="sendQty(product)"><i class="material-icons">check</i></button>
      </div>
    </td>
  </tr>
</template>

<script>
  import Spinner from './spinner';
  import { mapActions } from 'vuex';

  export default {
    props: ['product'],
    computed: {
      imagePath() {
        return `${data.baseUrl}/${this.product.image_thumbnail_path}`;
      },
      classObject() {
        return {
          active: this.isActive,
          disabled: !this.isEnabled
        }
      }
    },
    components: {
      Spinner
    },
    data() {
      return {
        isActive: false,
        isEnabled: false
      }
    },
    methods: {
      ...mapActions([
        'updateQtyByProductId'
      ]),
      sendQty(product) {
        let apiRootUrl = data.apiRootUrl.replace(/\?.*/,'');
        let apiEditProductsUrl = `${apiRootUrl}/product/${product.product_id}`
        let apiEditCombinationsUrl = `${apiRootUrl}/product/${product.product_id}/combination/${product.product_attribute_id}`;
        let postUrl = product.product_attribute_id ? apiEditCombinationsUrl : apiEditProductsUrl;

        // POST when qty !=0

        if(this.isEnabled) {
          this.$store.dispatch('updateQtyByProductId', {
            http: this.$http,
            url: postUrl,
            qty: product.qty
          });
        }
      },
      toggleCheck(val) {
        this.isActive = !this.isActive
      },
      toggleEnabled(val) {
        this.isEnabled = (val !==0);
      }
    }
  }
</script>

<style lang="sass?outputStyle=expanded" scoped>
  @import "~PrestaKit/scss/custom/_variables.scss";
  .thumbnail {
      border: $gray-light 1px solid;
  }
  .qty {
      position: relative;
      .check-button {
        outline:none;
        display: none;
        position: absolute;
        top: 3px;
        right: 27px;
        border: none;
        height: 29px;
        width: 40px;
        background: $brand-primary;
        z-index: 2;
        border-left: 10px solid white;
        .material-icons {
          color: white;
        }
      }
  }
  .qty.active {
    .check-button {
      display: block;
    }
  }
  .qty.disabled {
    .check-button {
      display: block;
      background: $gray-light;
      cursor: default;
    }
  }
</style>
