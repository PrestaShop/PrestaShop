<template>
    <div class="d-inline">
        <img v-if="displayThumb" :src="imagePath" class="thumbnail" :class="{ 'is-combination' : isCombination }" />
        <div v-else class="no-img"></div>
        <span class="m-l-1">{{ name }}</span>
    </div>
</template>
<script>
  export default {
    props: ['name','thumbnail','isCombination'],
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

</style>