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
  <div id="search" class="col-md-8 mb-4">
    <form class="search-form" @submit.prevent>
      <label>{{trans('search_label')}}</label>
      <div class="input-group">
        <PSTags ref="psTags" :tags="tags" @tagChange="onSearch" :placeholder="trans('search_placeholder')" />
        <span class="input-group-btn">
          <PSButton @click="onClick" class="search-button" :primary="true">
              <i class="material-icons">search</i>
              {{trans('button_search')}}
          </PSButton>
        </span>
      </div>
    </form>
  </div>
</template>

<script>
  import PSTags from 'app/widgets/ps-tags';
  import PSButton from 'app/widgets/ps-button';

  export default {
    components: {
      PSTags,
      PSButton,
    },
    methods: {
      onClick() {
        const tag = this.$refs.psTags.tag;
        this.$refs.psTags.add(tag);
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
  };
</script>
