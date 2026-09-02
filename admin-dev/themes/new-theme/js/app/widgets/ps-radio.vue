<!--*
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *-->

<template>
  <div class="ps-radio">
    <input
      type="radio"
      :id="id"
      name="radio-group"
      :checked="checked"
      @change="onChange"
    >
    <label :for="id">{{ label }}</label>
  </div>
</template>

<script lang="ts">
  import {defineComponent} from 'vue';

  export default defineComponent({
    props: {
      id: {
        type: String,
        required: true,
      },
      label: {
        type: String,
        required: false,
        default: '',
      },
      checked: {
        type: Boolean,
        required: false,
      },
      value: {
        type: String,
        required: false,
        default: '',
      },
    },
    methods: {
      onChange(): void {
        this.$emit('change', this.value);
      },
    },
  });
</script>
