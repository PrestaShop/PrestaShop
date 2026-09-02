<!--*
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *-->
<template>
  <div class="ps-loader">
    <div class="timeline-item">
      <div class="animated-background">
        <slot />
      </div>
    </div>
  </div>
</template>

<script lang="ts">
  import {defineComponent} from 'vue';

  export default defineComponent({
  });
</script>

<style lang="scss" scoped>
  @import '~@scss/config/_settings.scss';

  .ps-loader {
    width: 100%;
    .animated-background {
      animation-duration: 1s;
      animation-iteration-count: infinite;
      animation-name: loading;
      animation-timing-function: linear;
      background: var(--#{$cdk}primary-200);
      background:
        linear-gradient(to right, var(--#{$cdk}primary-200) 10%,
        var(--#{$cdk}primary-500) 20%,
        var(--#{$cdk}primary-200) 30%);
      background-size: 100%;
      height: var(--#{$cdk}size-40);
      position: relative;
    }

    .background-masker {
      background: var(--#{$cdk}white);
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
