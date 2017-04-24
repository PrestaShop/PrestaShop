<template>
    <div class="media">
      <div class="media-left">
        <img v-if="displayThumb" :src="thumbnail" class="thumbnail media-object"  />
        <div v-else class="no-img"></div>
      </div>
      <div class="m-l-1 desc media-body">
        <slot />
      </div>
    </div>
</template>
<script>
  export default {
    props: ['thumbnail'],
    computed: {
      displayThumb() {
        if(this.thumbnail) {
          return true;
        }
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
