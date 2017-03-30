<template>
  <div id="search" class="row">
    <div class="col-md-8">
      <div class="m-b-2">
        <form @keyup.enter="onKeyEnter" class="search-form" @submit.prevent="onKeyEnter">
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
        if(this.tags.length) {
          let apiRootUrl = data.apiRootUrl.replace(/\?.*/,'');
          this.$store.dispatch('searchByKeywords', {
            url: apiRootUrl,
            keywords: this.tags
          });
        }
      },
      onKeyEnter() {
        $(this.$el).find('.input').blur();
      },
      handleChange(index, text) {
        if (text) {
          this.tags.splice(index, 0, text);
        } else {
          this.tags.splice(index, 1);
        }
      }
    }
  }
</script>
<style lang="sass?outputStyle=expanded">
  @import "~PrestaKit/scss/custom/_variables.scss";
  #search .tags-input {
    background: white;
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
    input.input {
      font-family: Open Sans, sans-serif;
    }
  }
  .search-form {
    position: relative;
    .search-button {
      position: absolute;
      right: 0;
      top: 0;
      border-radius: 0;
      margin-top: 28px;
      height:29px;
    }
  }
</style>