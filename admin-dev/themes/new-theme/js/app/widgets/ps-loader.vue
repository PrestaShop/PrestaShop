<template>
  <div class="ps-loader">
    <div class="timeline-item">
      <div class="animated-background">
        <slot />
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    props: ['thumb'],
  };
</script>
<style lang="sass" scoped>
  @import "~PrestaKit/scss/custom/_variables.scss";
  .ps-loader {
    width: 100%;
    .animated-background {
        animation-duration: 1s;
        animation-iteration-count: infinite;
        animation-name: loading;
        animation-timing-function: linear;
        background: $gray-soft;
        background: linear-gradient(to right, $gray-soft 8%, #ccc 18%, $gray-soft 33%);
        background-size: 100%;
        height: 40px;
        position: relative;
    }

    .background-masker {
      background: white;
      position: absolute;
    }
  }

  @keyframes loading{
    0%{
        background-position: -500px 0
    }
    100%{
        background-position: 500px 0
    }
  }
</style>
