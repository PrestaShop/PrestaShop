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
        <div class="translations-summary pull-xs-right">
          <span>{{ totalTranslations }}</span>
          <span v-show="totalMissingTranslations"> - <span class="missing">{{ totalMissingTranslationsString }}</span></span>
        </div>
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
      totalTranslations() {
        return (this.$store.state.totalTranslations <= 1) ? this.trans('label_total_domain_singular').replace('%nb_translation%', this.$store.state.totalTranslations) : this.trans('label_total_domain').replace('%nb_translations%', this.$store.state.totalTranslations);
      },
      totalMissingTranslations() {
        return this.$store.state.totalMissingTranslations;
      },
      totalMissingTranslationsString() {
        return this.totalMissingTranslations === 1 ? this.trans('label_missing_singular') : this.trans('label_missing').replace('%d', this.totalMissingTranslations);
      },
    },
    methods: {
      onSearch(keywords) {
        this.$store.dispatch('getDomainsTree', {
          search: keywords,
          store: this.$store,
        });
        this.$store.currentDomain = '';
      },
    },
    components: {
      TranslationsHeader,
      Search,
      Sidebar,
      Principal,
    },
  };
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

  .missing {
    color: $danger;
  }

  .translations-summary {
    font-weight: $font-weight-semibold;
    font-size: 1rem;
  }
</style>
