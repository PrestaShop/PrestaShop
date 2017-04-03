<template>
    <div class="flex">
      <div>
        <img v-if="displayThumb" :src="imagePath" class="thumbnail"  />
        <div v-else class="no-img"></div>
      </div>
      <div class="m-l-1 desc">
        <p>{{ name }}<small v-if="hasCombination"><br />{{ combination }}</small></p>
      </div>
    </div>
</template>
<script>
  export default {
    props: ['name','thumbnail','combinationName', 'hasCombination'],
    computed: {
      combination() {
        let arr = this.combinationName.split(',');
        let attr = '';
        arr.forEach((attribute)=>{
         let value = attribute.split('-');
         attr += attr.length ? ` - ${value[1]}` : value[1];
        });
        return attr;
      },
      displayThumb() {
        if(this.imagePath) {
          return true;
        }
      },
      imagePath() {
        if(this.thumbnail !== 'N/A') {
          return `${data.baseUrl}/${this.thumbnail}`;
        }
        return null;
      }
    }
  }
</script>

<style lang="sass?outputStyle=expanded" scoped>
  @import "~PrestaKit/scss/custom/_variables.scss";
  .product-title {
    .has-combination & {
      font-weight: 600;
    }
  }
  .thumbnail, .no-img {
      border: $gray-light 1px solid;
  }
  .no-img {
    background: white;
    width: 47px;
    height: 47px;
    display: inline-block;
    vertical-align: middle;
  }
  .desc {
    white-space: normal;
  }
  small {
    color: $gray-medium;
  }
</style>
