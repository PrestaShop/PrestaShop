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
  <div id="search">
    <form
      class="search-form"
      @submit.prevent
    >
      <label>{{ trans('search_label') }}</label>
      <div class="input-group">
        <PSTags
          ref="psTags"
          :tags="tags"
          @tagChange="onSearch"
          :placeholder="trans('search_placeholder')"
        />
        <div class="input-group-append">
          <PSButton
            @click="onClick"
            class="search-button"
            :primary="true"
          >
            <i class="material-icons">search</i>
            {{ trans('button_search') }}
          </PSButton>
        </div>
      </div>
    </form>
  </div>
</template>

<script lang="ts">
  import PSTags from '@app/widgets/ps-tags.vue';
  import PSButton from '@app/widgets/ps-button.vue';
  import {defineComponent} from 'vue';
  import TranslationMixin from '@app/pages/translations/mixins/translate';

  export default defineComponent({
    components: {
      PSTags,
      PSButton,
    },
    mixins: [TranslationMixin],
    methods: {
      onClick() {
        const refPsTags = this.$refs.psTags as VTags;
        const {tag} = refPsTags;
        refPsTags.add(tag);
      },
      onSearch() {
        this.$store.dispatch('updateSearch', this.tags);
        this.$emit('search', this.tags);
      },
    },
    watch: {
      $route() {
        this.tags = [];
      },
    },
    data() {
      return {
        tags: [],
      };
    },
  });
</script>
