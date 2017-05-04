<template>
  <div class="col-xs-3">
    <div class="card p-a-1">
      <PSTree
        :model="domainsTree"
        className="translationTree"
        :translations="translations"
      />
    </div>
  </div>
</template>

<script>
  import PSTree from 'app/widgets/ps-tree/ps-tree';
  import { EventBus } from 'app/utils/event-bus';

  export default {
    computed: {
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
    components: {
      PSTree
    }
  }
</script>

<style lang="sass">
  @import "~PrestaKit/scss/custom/_variables.scss";
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
