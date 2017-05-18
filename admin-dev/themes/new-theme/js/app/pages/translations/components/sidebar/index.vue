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
  <div class="col-xs-3">
    <div class="card p-a-1">
      <PSTree
        ref="domainTree"
        :model="domainsTree"
        className="translationTree"
        :translations="translations"
        :currentItem="currentItem"
      />
    </div>
  </div>
</template>

<script>
  import PSTree from 'app/widgets/ps-tree/ps-tree';
  import { EventBus } from 'app/utils/event-bus';

  export default {
    computed: {
      currentItem() {
        if (this.$store.getters.currentDomain === '') {
          if (this.domainsTree.length) {
            let domain = this.getFirstDomainToDisplay(this.domainsTree);
            this.$store.dispatch('getCatalog', {url: domain.dataValue});
            return domain.full_name;
          }
        }

        return this.$store.getters.currentDomain;
      },
      domainsTree () {
        return this.$store.getters.domainsTree;
      },
      translations () {
        return {
          expand: this.trans('sidebar_expand'),
          reduce: this.trans('sidebar_collapse'),
          extra: this.trans('label_missing'),
        };
      }
    },
    mounted () {
      this.$store.dispatch('getDomainsTree');
      EventBus.$on('lastTreeItemClick', (el) => {
        this.$store.dispatch('updateCurrentDomain', el.item.full_name);
        this.$store.dispatch('getCatalog', {url: el.item.dataValue});
        this.$store.dispatch('updatePageIndex', 1);
      })
    },
    methods: {
      getFirstDomainToDisplay: function getFirstDomainToDisplay (tree) {
        let keys = Object.keys(tree);
        let firstElement = tree[keys[0]];

        if (firstElement.children && firstElement.children.length > 0) {
          return getFirstDomainToDisplay(firstElement.children);
        } else {
          return firstElement;
        }
      }
    },
    components: {
      PSTree
    }
  }
</script>

<style lang="sass">
  @import "~PrestaKit/scss/custom/_variables.scss";
  .tree-header {
    .pointer {
      font-size: .65rem;
      strong {
        font-weight: 700;
      }
      .material-icons {
        font-size: 25px;
      }
    }
  }
  .translationTree {
    .tree-name {
      margin-bottom: .9375rem;
      line-height: 1.5rem;

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
    .tree-label {
      &:hover {
        color: $primary;
      }
    }
  }
</style>
