<template>
  <div id="search" class="row m-b-2">
    <div class="col-md-8">
      <div class="m-b-2">
        <form @keyup.enter="onSubmit" class="search-form" @submit.prevent="onSubmit">
          <label>Search products (search by name,reference,supplier)</label>
          <Tags :tags="tags" @tagChange="onSubmit"/>
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
  import Tags from '../utils/tags';

  export default {
    components: {
      Filters,
      Tags
    },
    methods: {
      onSubmit() {
        $(this.$el).find('.input').blur();
        this.$store.dispatch('getStock', {
          order: this.$store.state.order,
          page_size: this.$store.state.productsPerPage,
          page_index: this.$store.state.pageIndex,
          keywords: this.tags
        });
        setTimeout(() => $(this.$el).find('.gap:last-of-type .input').focus() , 15);
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
      padding: 0 10px;
      min-height: 29px;
      outline: none;
      border-radius: 0;
    }
  }
  .search-form {
    width: calc(100% - 100px);
    .search-button {
      float: left;
      position: absolute;
      right: 12px;
      top: 1px;
      border-radius: 0;
      margin-top: 28px;
      height:29px;
    }
  }
</style>
