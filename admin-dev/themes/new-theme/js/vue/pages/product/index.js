import Vue from 'vue';
import ProductImageUpload from './ProductImageUpload';

export default new Vue({
  //@todo: this could be the root vue instance for whole product page
  //  filled with various components which would be able to share the state.
  el: '#productPageVue',
  components: {
    ProductImageUpload,
  },
});
