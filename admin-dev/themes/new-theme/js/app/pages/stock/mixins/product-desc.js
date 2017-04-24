export default {
  computed: {
    thumbnail() {
      if(this.product.combination_thumbnail !== 'N/A') {
          return `${window.data.baseUrl}/${this.product.combination_thumbnail}`;
      }
      return null;
    },
    combinationName() {
      let arr = this.product.combination_name.split(',');
      let attr = '';
      arr.forEach((attribute)=>{
          let value = attribute.split('-');
          attr += attr.length ? ` - ${value[1]}` : value[1];
      });
      return attr;
    },
    hasCombination() {
      return !!this.product.combination_id;
    }
  }
};
