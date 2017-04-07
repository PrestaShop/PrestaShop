<template>
  <div>
    <button class="search-input collapse-button" type="button" data-toggle="collapse" data-target="#filters">
      <i class="material-icons m-r-1">filter_list</i>
      <i class="material-icons pull-right ">keyboard_arrow_down</i>
      Advanced Filters
    </button>
    <div class="collapse" id="filters">
      <div class="row">
        <div class="col-md-6">
          <div class="p-y-2 p-x-2">
            <h2>Filter by Supplier</h2>
            <FilterComponent
              placeholder="Search a supplier"
              :list="this.$store.state.suppliers"
              itemID="supplier_id"
              label="name"
              getData="getSuppliers"
              @active="onFilterActive"
            />
          </div>
        </div>
        <div class="col-md-6">
          <div class="p-y-2 p-x-2">
            <h2>Filter by categories</h2>
            <FilterComponent
              placeholder="Search a category"
              :list="this.$store.state.categories"
              itemID="id_category"
              label="name"
              getData="getCategories"
            />
          </div>
        </div>
        <button type="button" class="btn btn-primary pull-right m-y-2 m-x-2" :disabled="disabled" @click="onClick">
          <i class="material-icons m-r-1">filter_list</i>
          APPLY ADVANCED FILTERS
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
        this.supplierFilter();
      },
      onFilterActive(val, list) {
        this.suppliers = list;
        this.disabled= !val;
        if(!list) {
          this.supplierFilter();
        }
      },
      supplierFilter() {
        this.$store.dispatch('getStock', {
          suppliers : this.suppliers
        });
      }
    },
    components: {
      FilterComponent
    },
    data() {
      return {
        disabled: true,
        suppliers: []
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
