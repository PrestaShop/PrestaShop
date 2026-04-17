<!--*
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *-->
<template>
  <div class="media">
    <img
      v-if="displayThumb"
      :src="thumbnail"
      class="thumbnail d-flex"
    >
    <div
      v-else
      class="no-img"
    />
    <div class="ml-2 desc media-body">
      <slot />
    </div>
  </div>
</template>

<script lang="ts">
  import {defineComponent} from 'vue';

  export default defineComponent({
    props: {
      thumbnail: {
        type: String,
        required: false,
        default: '',
      },
    },
    computed: {
      displayThumb(): boolean {
        return this.thumbnail === '' ? false : !!this.thumbnail;
      },
    },
  });
</script>

<style lang="scss" scoped>
  @import '~@scss/config/_settings.scss';

  .product-title {
    .has-combination & {
      font-weight: 600;
    }
  }
  .thumbnail, .no-img {
    border: 1px solid var(--#{$cdk}primary-400);
    max-width: var(--#{$cdk}size-44);
  }
  .no-img {
    background: var(--#{$cdk}white);
    width: var(--#{$cdk}size-44);
    height: var(--#{$cdk}size-44);
    display: inline-block;
    vertical-align: middle;
  }
  .desc {
    white-space: normal;
  }
</style>
