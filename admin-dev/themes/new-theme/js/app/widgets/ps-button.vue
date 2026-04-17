<!--*
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *-->
<template>
  <button
    type="button"
    class="btn"
    :class="classObject"
  >
    <slot />
  </button>
</template>

<script lang="ts">
  import {defineComponent} from 'vue';

  export default defineComponent({
    props: {
      primary: {type: Boolean},
      ghost: {type: Boolean},
    },
    computed: {
      classObject(): {
        'btn-outline-primary'?: boolean,
        'btn-outline-secondary'?: boolean,
        'btn-primary'?: boolean,
        'btn-secondary'?: boolean
      } {
        if (this.ghost) {
          return {
            'btn-outline-primary': this.primary,
            'btn-outline-secondary': !this.primary,
          };
        }

        return {
          'btn-primary': this.primary,
          'btn-secondary': !this.primary,
        };
      },
    },
  });
</script>
