<template>
  <div id="search" class="row m-b-2">
    <div class="col-md-8">
      <div class="m-b-2">
        <form class="search-form" @submit.prevent>
          <label>{{trans('product_search')}}</label>
          <PSTags :tags="tags" @tagChange="onSearch" />
          <PSButton @click="onSearch" class="search-button" :primary="true">
            <i class="material-icons">search</i>
            {{trans('button_search')}}
          </PSButton>
        </form>
      </div>
      <Filters @applyFilter="applyFilter"/>
    </div>
  </div>
</template>

<script>
  import Filters from './filters';
  import PSTags from 'app/widgets/ps-tags';
  import PSButton from 'app/widgets/ps-button';

  export default {
    components: {
      Filters,
      PSTags,
      PSButton,
    },
    methods: {
      onSearch() {
        this.$emit('search', this.tags);
      },
      applyFilter(filters) {
        this.$emit('applyFilter', filters);
      },
    },
    watch: {
      $route() {
        this.tags = [];
      },
    },
    data: () => ({ tags: [] }),
  };
</script>
<style lang="sass">
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
      right: 12px;
      top: 1px;
      margin-top: 28px;
    }
  }
</style>
