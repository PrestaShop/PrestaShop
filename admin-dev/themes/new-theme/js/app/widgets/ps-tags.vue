<!--**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *-->
<template>
  <div class="tags-input search-input search" :class="{ 'search-with-icon': hasIcon }" @click="focus()">
    <span v-for="(tag, index) in tags" class="input-tag">
      <span class="tag">{{ tag }}<i class="material-icons" @click="close(index)">close</i></span>
    </span>
    <input
      ref = "tags"
      :placeholder="placeholderToDisplay"
      type="text"
      v-model="tag"
      class="form-control input"
      @keyup="onKeyUp"
      @keydown.enter="add(tag)"
      @keydown.delete.stop="remove()"
      :size="inputSize"
    />
  </div>
</template>

<script>

  export default {
    props: ['tags', 'placeholder', 'hasIcon'],
    computed: {
      inputSize() {
        return !this.tags.length && this.placeholder ? this.placeholder.length : 0;
      },
      placeholderToDisplay() {
        return this.tags.length ? '' : this.placeholder;
      },
    },
    methods: {
      onKeyUp() {
        this.$emit('typing', this.$refs.tags.value);
      },
      add(tag) {
        if (tag) {
          this.tags.push(tag.trim());
          this.tag = '';
          this.focus();
          this.$emit('tagChange', this.tag);
        }
      },
      close(index) {
        const tagName = this.tags[index];
        this.tags.splice(index, 1);
        this.$emit('tagChange', tagName);
      },
      remove() {
        if (this.tags.length && !this.tag.length) {
          const tagName = this.tags[this.tags.length - 1];
          this.tags.pop();
          this.$emit('tagChange', tagName);
        }
      },
      focus() {
        this.$refs.tags.focus();
      },
    },
    data: () => ({ tag: null }),
  };
</script>
<style lang="sass" type="text/scss">
  @import "../../../scss/config/_settings.scss";
  .tags-input {
    .tag {
      background: $brand-primary;
      color: white;
      padding: 2px 4px;
      border-radius: 0;
      font-weight: lighter;
      display: inline-block;
      margin: 2px;
      min-height: 25px;
      .material-icons {
        vertical-align: middle;
        color: $gray-dark;
        margin: 0 2px 0 5px;
        cursor: pointer;
      }
    }
    input.input, input.input:focus {
      border: none;
      outline: none;
      min-height: 33px;
      width: auto;
    }
  }
</style>
