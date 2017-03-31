<template>
  <div id="search" class="row">
    <div class="col-md-8">
      <div class="m-b-2">
        <form @keyup.enter="onSubmit" class="search-form" @submit.prevent="onSubmit">
          <label>Search products (search by name,reference,supplier)</label>
          <Tags :tags="tags" @tags-change="handleChange"  />
          <button type="button" class="btn btn-primary search-button" @click="onSubmit">
            <i class="material-icons">search</i>Search
          </button>
        </form>
      </div>
      <SearchFilter />
    </div>
  </div>
</template>

<script>
  import SearchFilter from './search-filter';
  import Tags from 'vue-tagsinput';

  export default {
    components: {
      SearchFilter,
      Tags
    },
    data() {
      return {
        tags: []
      }
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
      },
      handleChange(index, text) {
        if (text) {
          this.tags.splice(index, 0, text);
        } else {
          this.tags.splice(index, 1);
          this.onSubmit();
        }
      }
    }
  }
</script>
<style lang="sass?outputStyle=expanded">
  @import "~PrestaKit/scss/custom/_variables.scss";
  #search .tags-input {
    background: white;
    padding: 0 10px;
    min-height: 27px;
    .tag {
      background: $brand-primary;
      color: white;
      padding: 2px 4px;
      border-radius: 0;
      font-weight: lighter;
      .hl-click {
        height: 100%;
        width: 15px;
      }
    }
    .gap:first-of-type .input {
      margin-left: -6px;
    }
    input.input {
      font-family: Open Sans, sans-serif;
      cursor: text;
      padding-left: 2px;
    }
  }
  .search-form {
    width: calc(100% - 100px);
    .search-button {
      float: left;
      position: absolute;
      right: 12px;
      top: 0;
      border-radius: 0;
      margin-top: 28px;
      height:29px;
    }
  }
</style>
