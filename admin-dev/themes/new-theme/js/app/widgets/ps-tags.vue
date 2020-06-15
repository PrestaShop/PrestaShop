<!--**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *-->
<template>
  <div class="tags-input search-input search d-flex flex-wrap" :class="{ 'search-with-icon': hasIcon }" @click="focus()">
    <div class="tags-wrapper">
      <span v-for="(tag, index) in tags" class="tag">{{ tag }}<i class="material-icons" @click="close(index)">close</i></span>
    </div>
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
