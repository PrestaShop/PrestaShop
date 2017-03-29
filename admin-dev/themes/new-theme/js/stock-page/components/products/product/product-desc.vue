<template>
    <div class="d-inline">
        <img v-if="displayThumb" :src="imagePath" class="thumbnail"  />
        <div v-else class="no-img"></div>
        <span class="m-l-1 product-title">{{ name }}</span>
    </div>
</template>
<script>
  export default {
    props: ['name','thumbnail'],
    computed: {
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
</style>