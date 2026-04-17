<!--*
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *-->
<template>
  <div class="md-checkbox">
    <label>
      <input
        type="checkbox"
        :id="id"
        v-model="checked"
        :class="{'indeterminate' : isIndeterminate }"
      >
      <i class="md-checkbox-control" />
      <slot name="label" />
    </label>
  </div>
</template>

<script lang="ts">
  import {defineComponent} from 'vue';

  /**
   * @deprecated since 8.0, use app/components/checkbox.vue instead
   */
  export default defineComponent({
    props: {
      id: {
        type: String,
        required: false,
        default: '',
      },
      model: {
        type: Object,
        required: false,
        default: () => ({}),
      },
      isIndeterminate: {
        type: Boolean,
        required: false,
        default: false,
      },
    },
    watch: {
      checked(val: boolean): void {
        this.$emit('checked', {
          checked: val,
          item: this.model,
        });
      },
    },
    data() {
      return {
        checked: false,
      };
    },
  });
</script>
