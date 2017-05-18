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
  <div v-if="isReady" id="app" class="translations-app">
    <TranslationsHeader />
    <div class="container-fluid">
      <div class="row">
        <Search @search="onSearch" />
      </div>

      <div class="row">
        <Sidebar />
        <Principal />
      </div>
    </div>
  </div>
</template>

<script>
  import TranslationsHeader from './header/translations-header';
  import Search from './header/search';
  import Sidebar from './sidebar';
  import Principal from './principal';

  export default {
    name: 'app',
    computed: {
      isReady() {
        return this.$store.getters.isReady;
      },
    },
    methods: {
      onSearch(keywords) {
        let desc = this.$route.name === 'overview' ? '' : ' desc';
        this.$store.dispatch('updateKeywords', keywords);
        this.fetch(desc);
      },
    },
    components: {
      TranslationsHeader,
      Search,
      Sidebar,
      Principal
    },
  }
</script>

<style lang="sass?outputStyle=expanded">
  @import "~PrestaKit/scss/custom/_variables.scss";
  .header-toolbar {
    z-index: 0;
    height: 128px;
  }
  .translations-app {
    padding-top: 3em;
  }
  .flex {
    display: flex;
    align-items: center;
  }
</style>
