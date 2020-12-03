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
  <div v-if="isReady" id="app" class="translations-app">
    <TranslationsHeader />
    <div class="container-fluid">
      <div class="row justify-content-between align-items-center">
        <Search @search="onSearch" />
        <div class="translations-summary">
          <span>{{ totalTranslations }}</span>
          <span v-show="totalMissingTranslations"> - <span class="missing">{{ totalMissingTranslationsString }}</span></span>
        </div>
      </div>

      <div class="row">
        <Sidebar :modal="this.$refs.transModal" :principal="this.$refs.principal"/>
        <Principal :modal="this.$refs.transModal" ref="principal" />
      </div>
    </div>
    <PSModal ref="transModal" :translations="translations"/>
  </div>
</template>

<script>
  import TranslationsHeader from '@app/pages/translations/components/header/translations-header';
  import Search from '@app/pages/translations/components/header/search';
  import Sidebar from '@app/pages/translations/components/sidebar';
  import Principal from '@app/pages/translations/components/principal';
  import PSModal from '@app/widgets/ps-modal';

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
      translations() {
        return {
          button_save: this.trans('button_save'),
          button_leave: this.trans('button_leave'),
          modal_content: this.trans('modal_content'),
          modal_title: this.trans('modal_title'),
        };
      },
    },
    mounted() {
      $('a').on('click', (e) => {
        if ($(e.currentTarget).attr('href')) {
          this.destHref = $(e.currentTarget).attr('href');
        }
      });
      window.onbeforeunload = () => {
        if (!this.destHref && this.isEdited() && !this.leave) {
          return true;
        }
        if (!this.leave && this.isEdited()) {
          setTimeout(() => {
            window.stop();
          }, 500);
          this.$refs.transModal.showModal();
          this.$refs.transModal.$once('save', () => {
            this.$refs.principal.saveTranslations();
            this.leavePage();
          });
          this.$refs.transModal.$once('leave', () => {
            this.leavePage();
          });
          return null;
        }
      };
    },
    methods: {
      onSearch(keywords) {
        this.$store.dispatch('getDomainsTree', {
          store: this.$store,
        });
        this.$store.currentDomain = '';
      },
      /**
       * Set leave to true and redirect the user to the new location
       */
      leavePage() {
        this.leave = true;
        window.location.href = this.destHref;
      },
      isEdited() {
        return this.$refs.principal.edited();
      },
    },
    data: () => ({
      destHref: null,
      leave: false,
    }),
    components: {
      TranslationsHeader,
      Search,
      Sidebar,
      Principal,
      PSModal,
    },
  };
</script>

<style lang="scss" type="text/scss">
  @import "../../../../../scss/config/_settings.scss";
  // hide the layout header
  #main-div > .header-toolbar {
    height: 0;
    display: none;
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
