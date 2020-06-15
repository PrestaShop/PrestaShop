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
  <div class="col-sm-3">
    <div class="card p-3">
      <PSTree
        ref="domainTree"
        :model="domainsTree"
        className="translationTree"
        :translations="translations"
        :currentItem="currentItem"
        v-if="treeReady"
      />
      <PSSpinner v-else />
    </div>
  </div>
</template>

<script>
  import PSTree from '@app/widgets/ps-tree/ps-tree';
  import PSSpinner from '@app/widgets/ps-spinner';
  import { EventBus } from '@app/utils/event-bus';

  export default {
    props: [
      'modal',
      'principal',
    ],
    computed: {
      treeReady() {
        return !this.$store.state.sidebarLoading;
      },
      currentItem() {
        if (this.$store.getters.currentDomain === '' || typeof this.$store.getters.currentDomain === 'undefined') {
          if (this.domainsTree.length) {
            const domain = this.getFirstDomainToDisplay(this.domainsTree);
            EventBus.$emit('reduce');
            this.$store.dispatch('updateCurrentDomain', domain);

            if (domain !== '') {
              this.$store.dispatch('getCatalog', { url: domain.dataValue });
              EventBus.$emit('setCurrentElement', domain.full_name);
              return domain.full_name;
            }

            this.$store.dispatch('updatePrincipalLoading', false);
            return '';
          }
        }

        return this.$store.getters.currentDomain;
      },
      domainsTree() {
        return this.$store.getters.domainsTree;
      },
      translations() {
        return {
          expand: this.trans('sidebar_expand'),
          reduce: this.trans('sidebar_collapse'),
          extra: this.trans('label_missing'),
          extra_singular: this.trans('label_missing_singular'),
        };
      },
    },
    mounted() {
      this.$store.dispatch('getDomainsTree', {
        store: this.$store,
      });
      EventBus.$on('lastTreeItemClick', (el) => {
        if (this.edited()) {
          this.modal.showModal();
          this.modal.$once('save', () => {
            this.principal.saveTranslations();
            this.itemClick(el);
          });
          this.modal.$once('leave', () => {
            this.itemClick(el);
          });
        } else {
          this.itemClick(el);
        }
      });
    },
    methods: {
      /**
       * Update the domain, retrieve the translations catalog, set the page to 1
       * and reset the modified translations
       * @param {object} el - Domain to set
       */
      itemClick: function itemClick(el) {
        this.$store.dispatch('updateCurrentDomain', el.item);
        this.$store.dispatch('getCatalog', { url: el.item.dataValue });
        this.$store.dispatch('updatePageIndex', 1);
        this.$store.state.modifiedTranslations = [];
      },
      getFirstDomainToDisplay: function getFirstDomainToDisplay(tree) {
        const keys = Object.keys(tree);
        let toDisplay = '';

        for (let i = 0; i < tree.length; i++) {
          if (!tree[keys[i]].disable) {
            if (tree[keys[i]].children && tree[keys[i]].children.length > 0) {
              return getFirstDomainToDisplay(tree[keys[i]].children);
            }

            toDisplay = tree[keys[i]];
            break;
          }
        }

        return toDisplay;
      },
      /**
       * Check if some translations have been edited
       * @returns {boolean}
       */
      edited: function edited() {
        return this.$store.state.modifiedTranslations.length > 0;
      },
    },
    components: {
      PSTree,
      PSSpinner,
    },
  };
</script>

<style lang="scss" type="text/scss">
  @import "../../../../../../scss/config/_settings.scss";
  .translationTree {
    .tree-name {
      margin-bottom: .9375rem;

      &.active {
        font-weight: bold;
      }

      &.extra {
        color: $danger;
      }
    }
    .tree-extra-label {
      color: $danger;
      text-transform: uppercase;
      font-size: .65rem;
      margin-left: auto;
    }
    .tree-extra-label-mini {
      background-color: $danger;
      color: #ffffff;
      padding: 0 0.5rem;
      border-radius: 0.75rem;
      display: inline-block;
      font-size: .75rem;
      height: 1.5rem;
      margin-left: auto;
    }
    .tree-label {
      &:hover {
        color: $primary;
      }
    }
  }
  .ps-loader {
    $loader-white-height: 20px;
    $loader-line-height: 16px;
    .animated-background {
      height: 144px!important;
      animation-duration: 2s!important;
    }
    .background-masker {
      &.header-left {
        left: 0;
        top: $loader-line-height;
        height: 108px;
        width: 20px;
      }
      &.content-top {
        left: 0;
        top: $loader-line-height;
        height: $loader-white-height;
      }
      &.content-first-end {
        left: 0;
        top: $loader-line-height*2+$loader-white-height;
        height: $loader-white-height;
      }
      &.content-second-end {
        left: 0;
        top: $loader-line-height*3+$loader-white-height*2;
        height: $loader-white-height;
      }
      &.content-third-end {
        left: 0;
        top: $loader-line-height*4+$loader-white-height*3;
        height: $loader-white-height;
      }
    }
  }
</style>
