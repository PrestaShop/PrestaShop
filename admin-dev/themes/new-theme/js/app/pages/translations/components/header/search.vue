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
  <div id="search" class="col-md-8 mb-2">
    <form class="search-form" @submit.prevent>
      <label>{{trans('search_label')}}</label>
      <div class="search-group">
        <PSTags ref="psTags" :tags="tags" @tagChange="onSearch" :placeholder="trans('search_placeholder')" />
        <button type="button" class="btn btn-primary search-button" @click="onClick">
          <i class="material-icons">search</i>
          {{trans('button_search')}}
        </button>
      </div>
    </form>
  </div>
</template>

<script>
  import PSTags from 'app/widgets/ps-tags';

  export default {
    components: {
      PSTags,
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
<style lang="sass?outputStyle=expanded">
  @import "../../../../../../scss/config/_settings.scss";
  #search {
    .search-input {
      box-shadow: none;
      border: $gray-light 1px solid;
      background-color: white;
      min-height: 35px;
      outline: none;
      border-radius: 0;
      overflow: hidden;
      float: left;
      width: 70%;
    }
  }
  .search-form {
    .search-group {
      overflow: hidden;
    }
    .search-button {
      float: left;
      border-radius: 0;
      height:35px;
      position: absolute;
      bottom: 0;
    }
  }
</style>
