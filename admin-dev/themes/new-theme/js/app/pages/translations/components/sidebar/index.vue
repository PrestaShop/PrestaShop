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
        if (this.domainsTree.length) {
          let domain = this.getFirstDomainToDisplay(this.domainsTree);
          this.$store.dispatch('getCatalog', {url: domain.dataValue});
          return domain.full_name;
        }

        return '';
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
        this.$store.dispatch('getCatalog', {url: el.item.dataValue});
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
