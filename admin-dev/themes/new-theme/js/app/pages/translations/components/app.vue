<!--**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
  import TranslationsHeader from './header/translations-header';
  import Search from './header/search';
  import Sidebar from './sidebar';
  import Principal from './principal';
  import PSModal from 'app/widgets/ps-modal';
  import { EventBus } from 'app/utils/event-bus';


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

<style lang="sass?outputStyle=expanded">
  @import "../../../../../scss/config/_settings.scss";
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
