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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *-->
<template>
  <div
    v-if="isReady"
    id="app"
    class="translations-app"
  >
    <div class="container-fluid">
      <div class="row justify-content-between align-items-center">
        <Search @search="onSearch" />
        <div class="translations-summary">
          <span>{{ totalTranslations }}</span>
          <span v-show="totalMissingTranslations">
            -
            <span class="missing">{{ totalMissingTranslationsString }}</span>
          </span>
        </div>
      </div>

      <div class="row">
        <Sidebar
          :modal="$refs.transModal"
          :principal="$refs.principal"
        />
        <Principal
          :modal="$refs.transModal"
          ref="principal"
        />
      </div>
    </div>
    <PSModal
      ref="transModal"
      :translations="translations"
    />
  </div>
</template>

<script lang="ts">
  import {defineComponent} from 'vue';
  import Search from '@app/pages/translations/components/header/search.vue';
  import Sidebar from '@app/pages/translations/components/sidebar/index.vue';
  import Principal from '@app/pages/translations/components/principal/index.vue';
  import TranslationMixin from '@app/pages/translations/mixins/translate';
  import PSModal from '@app/widgets/ps-modal.vue';

  export default defineComponent({
    name: 'App',
    mixins: [TranslationMixin],
    computed: {
      isReady(): boolean {
        return this.$store.getters.isReady;
      },
      totalTranslations(): string {
        return this.$store.state.totalTranslations <= 1
          ? this.trans('label_total_domain_singular')
            .replace('%nb_translation%', this.$store.state.totalTranslations.toString())
          : this.trans('label_total_domain')
            .replace('%nb_translations%', this.$store.state.totalTranslations.toString());
      },
      totalMissingTranslations(): number {
        return this.$store.state.totalMissingTranslations;
      },
      totalMissingTranslationsString(): string {
        return this.totalMissingTranslations === 1
          ? this.trans('label_missing_singular')
          : this.trans('label_missing').replace('%d', <string><unknown> this.totalMissingTranslations);
      },
      translations(): Record<string, any> {
        return {
          button_save: this.trans('button_save'),
          button_leave: this.trans('button_leave'),
          modal_content: this.trans('modal_content'),
          modal_title: this.trans('modal_title'),
        };
      },
    },
    beforeMount() {
      this.$store.dispatch('getTranslations');
    },
    mounted() {
      $('a').on('click', (e: JQueryEventObject): void => {
        if ($(e.currentTarget).attr('href')) {
          this.destHref = <string>$(e.currentTarget).attr('href');
        }
      });
      window.onbeforeunload = (): any => {
        if (!this.destHref && this.isEdited() && !this.leave) {
          return true;
        }

        if (!this.leave && this.isEdited()) {
          setTimeout(() => {
            window.stop();
          }, 500);

          if (this.$refs.transModal && this.$refs.principal) {
            const refTransModal = this.$refs.transModal as VTransModal;
            refTransModal.showModal();
            refTransModal.$once('save', (): void => {
              (this.$refs.principal as VPrincipal).saveTranslations();
              this.leavePage();
            });

            refTransModal.$once('leave', () => {
              this.leavePage();
            });
          }
          return null;
        }

        return undefined;
      };
    },
    methods: {
      onSearch(): void {
        this.$store.dispatch('getDomainsTree', {
          store: this.$store,
        });
        this.$store.state.currentDomain = '';
      },
      /**
       * Set leave to true and redirect the user to the new location
       */
      leavePage(): void {
        this.leave = true;
        window.location.href = <string> this.destHref;
      },
      isEdited(): boolean {
        return (this.$refs.principal as VPrincipal).edited();
      },
    },
    data: () => ({
      destHref: null as null | string,
      leave: false,
    }),
    components: {
      Search,
      Sidebar,
      Principal,
      PSModal,
    },
  });
</script>

<style lang="scss" type="text/scss">
  @import '~@scss/config/_settings.scss';

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
