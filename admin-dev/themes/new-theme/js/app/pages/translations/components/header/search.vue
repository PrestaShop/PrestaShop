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
  <div id="search" class="col-md-8">
    <div class="m-b-2">
      <form class="search-form" @submit.prevent>
        <label>{{trans('search_label')}}</label>
        <PSTags :tags="tags" @tagChange="onSearch" :placeholder="trans('search_placeholder')" />
        <button type="button" class="btn btn-primary search-button" @click="onSearch">
          <i class="material-icons">search</i>
          {{trans('button_search')}}
        </button>
      </form>
    </div>
  </div>
</template>

<script>
  import PSTags from 'app/widgets/ps-tags';

  export default {
    components: {
      PSTags
    },
    methods: {
      onSearch() {
        this.$emit('search', this.tags);
      }
    },
    watch: {
      '$route' () {
        this.tags = [];
      }
    },
    data() {
      return {
        tags:[]
      }
    }
  }
</script>
<style lang="sass?outputStyle=expanded">
  @import "~PrestaKit/scss/custom/_variables.scss";
  #search {
    .search-input {
      box-shadow: none;
      border: $gray-light 1px solid;
      background-color: white;
      min-height: 35px;
      outline: none;
      border-radius: 0;
    }
  }
  .search-form {
    width: calc(100% - 100px);
    .search-button {
      float: right;
      position: absolute;
      right: 8px;
      top: 1px;
      border-radius: 0;
      margin-top: 28px;
      height:35px;
    }
  }
</style>
