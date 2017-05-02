<template>
  <div id="search" class="row m-b-2">
    <div class="col-md-8">
      <div class="m-b-2">
        <form class="search-form" @submit.prevent>
          <label>{{trans('search_label')}}</label>
          <PSTags :tags="tags" @tagChange="onSearch" :placeholder="trans('search_placeholder')" />
          <button type="button" class="btn btn-primary search-button" @click="onSearch">
            <i class="material-icons">search</i>
            {{trans('button_search')}}
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
  import PSTags from 'app/widgets/ps-tags';

  export default {
    components: {
      PSTags
    },
    methods: {
      onSearch() {
        this.$emit('search', this.tags);
      }
    },
    watch: {
      '$route' () {
        this.tags = [];
      }
    },
    data() {
      return {
        tags:[]
      }
    }
  }
</script>
<style lang="sass?outputStyle=expanded">
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
      right: 8px;
      top: 1px;
      border-radius: 0;
      margin-top: 28px;
      height:35px;
    }
  }
</style>
