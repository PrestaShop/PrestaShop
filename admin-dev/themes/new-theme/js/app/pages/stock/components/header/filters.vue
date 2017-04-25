<template>
  <div>
    <button class="search-input collapse-button" type="button" data-toggle="collapse" data-target="#filters">
      <i class="material-icons m-r-1">filter_list</i>
      <i class="material-icons pull-right ">keyboard_arrow_down</i>
      {{trans('button_advanced_filter')}}
    </button>
    <div class="collapse" id="filters">
      <div class="row">
        <div class="col-md-6">
          <div class="p-y-2 p-x-2">
            <h2>{{trans('filter_suppliers')}}</h2>
            <FilterComponent
              :placeholder="trans('filter_search_suppliers')"
              :list="this.$store.getters.suppliers"
              itemID="supplier_id"
              label="name"
              @active="onFilterActive"
            />
          </div>
        </div>
        <div class="col-md-6">
          <div class="p-y-2 p-x-2">
            <h2>{{trans('filter_categories')}}</h2>
            <FilterComponent
              :placeholder="trans('filter_search_category')"
              :list="this.$store.getters.categories"
              itemID="id_category"
              label="name"
              @active="onFilterActive"
            />
          </div>
        </div>
        <button type="button" class="btn btn-primary pull-right m-y-2 m-x-2" :disabled="disabled" @click="onClick">
          <i class="material-icons m-r-1">filter_list</i>
          {{trans('button_apply_advanced_filter')}}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
  import FilterComponent from './filters/filter-component';

  export default {
    computed : {
      disabled() {
        return this.disabled;
      }
    },
    methods: {
      onClick() {
        this.applyFilter();
      },
      onFilterActive(list, type) {
        if(type === 'supplier') {
          this.suppliers = list;
        }
        else {
          this.categories = list;
        }

        if(!this.suppliers.length && !this.categories.length) {
          this.disabled = true;
        }
        else {
          this.disabled= false;
        }
        if(!list.length) {
          this.applyFilter();
        }
      },
      applyFilter() {
        let request = (this.$route.name === 'overview') ? 'getStock' : 'getMovements';
        this.$store.dispatch(request, {
          suppliers : this.suppliers,
          categories: this.categories
        });
      }
    },
    components: {
      FilterComponent
    },
    mounted() {
      this.$store.dispatch('getSuppliers');
      this.$store.dispatch('getCategories');
    },
    data() {
      return {
        disabled: true,
        suppliers: [],
        categories: []
      }
    }
  }
</script>

<style lang="sass?outputStyle=expanded" scoped>
  @import "~PrestaKit/scss/custom/_variables.scss";
  #filters {
    background: white;
    border-radius: 2px;
    box-shadow: 1px 2px 3px 0 rgba(108, 134, 142, 0.3);
    border: solid 1px #b9cdd2;
  }
  .collapse-button {
    width: 100%;
    text-align: left;
  .material-icons {
    vertical-align: bottom;
    font-size: 20px;
    color: $gray-medium;
  }
  }
</style>
