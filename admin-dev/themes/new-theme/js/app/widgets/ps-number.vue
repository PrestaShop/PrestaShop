<!--*
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *-->

<template>
  <div
    class="ps-number"
    :class="{ 'hover-buttons': hoverButtons }"
  >
    <input
      type="number"
      class="form-control"
      :class="{ danger }"
      :value="value"
      placeholder="0"
      @keyup="onKeyup($event)"
      @keydown="onKeydown($event)"
      @focus="focusIn($event)"
      @blur="focusOut($event)"
    >
    <div
      class="ps-number-spinner d-flex"
      v-if="buttons"
    >
      <span
        class="ps-number-up"
        @click="increment($event)"
      />
      <span
        class="ps-number-down"
        @click="decrement($event)"
      />
    </div>
  </div>
</template>

<script lang="ts">
  import {defineComponent} from 'vue';

  export default defineComponent({
    props: {
      value: {
        type: [Number, String],
        default: 0,
      },
      danger: {
        type: Boolean,
        default: false,
      },
      buttons: {
        type: Boolean,
        default: false,
      },
      hoverButtons: {
        type: Boolean,
        default: false,
      },
    },
    methods: {
      getValue(): number {
        const value = Number.isNaN(this.value) ? 0 : Number.parseInt(<string> this.value, 10);

        return Number.isNaN(value) ? 0 : value;
      },
      onKeyup($event: Event): void {
        this.$emit('keyup', $event);
      },
      onKeydown($event: Event): void {
        this.$emit('keydown', $event);
      },
      focusIn($event: Event): void {
        this.$emit('focus', $event);
      },
      focusOut($event: Event): void {
        this.$emit('blur', $event);
      },
      increment($event: Event) {
        (<HTMLInputElement>$event.target).value = `${this.getValue() + 1}`;
        this.$emit('change', $event);
      },
      decrement($event: Event): void {
        (<HTMLInputElement>$event.target).value = `${this.getValue() - 1}`;
        this.$emit('change', $event);
      },
    },
  });
</script>
