<!--*
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *-->
<template>
  <div
    class="ps-select"
    :id="itemId"
  >
    <select
      class="form-control"
      v-model="selected"
      @change="onChange"
    >
      <option
        value="default"
        selected
      >
        <slot />
      </option>
      <option
        v-for="(item, index) in items"
        :key="index"
        :value="item[itemId]"
      >
        {{ item[itemName] }}
      </option>
    </select>
  </div>
</template>

<script lang="ts">
  import {defineComponent, PropType} from 'vue';

  export default defineComponent({
    props: {
      items: {
        type: Array as PropType<Array<Record<string, any>>>,
        required: true,
      },
      itemId: {
        type: String,
        required: false,
        default: '',
      },
      itemName: {
        type: String,
        required: false,
        default: '',
      },
    },
    methods: {
      onChange(): void {
        this.$emit('change', {
          value: this.selected,
          itemId: this.itemId,
        });
      },
    },
    data() {
      return {
        selected: 'default',
      };
    },
  });
</script>

<style lang="scss" scoped>
  @import '~@scss/config/_settings.scss';

  .ps-select {
    position: relative;
    select {
      appearance: none;
      border-radius: 0;
    }
    &::after {
      content: "\E313";
      font-family: var(--#{$cdk}font-family-material-icons);
      color: var(--#{$cdk}primary-400);
      font-size: var(--#{$cdk}size-20);
      position: absolute;
      right: var(--#{$cdk}size-5);
      top: var(--#{$cdk}size-5);
    }
  }
</style>
