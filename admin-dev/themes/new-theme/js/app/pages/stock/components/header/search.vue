<template>
  <div id="search" class="row m-b-2">
    <div class="col-md-8">
      <div class="m-b-2">
        <form class="search-form" @submit.prevent="onSubmit" @keyup.enter="onSubmit">
          <label>Search products (search by name,reference,supplier)</label>
          <PSTags :tags="tags" @tagChange="onSubmit"/>
          <button type="button" class="btn btn-primary search-button" @click="onSubmit">
            <i class="material-icons">search</i>
            Search
          </button>
        </form>
      </div>
      <Filters />
    </div>
  </div>
</template>

<script>
  import Filters from './filters';
  import PSTags from 'app/widgets/ps-tags';

  export default {
    components: {
      Filters,
      PSTags
    },
    methods: {
      onSubmit() {
        let request = (this.$route.name === 'overview') ? 'getStock' : 'getMovements';

        this.$store.dispatch(request, {
          order: this.$store.getters.order,
          page_size: this.$store.state.productsPerPage,
          page_index: this.$store.getters.pageIndex,
          keywords: this.tags
        });
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
