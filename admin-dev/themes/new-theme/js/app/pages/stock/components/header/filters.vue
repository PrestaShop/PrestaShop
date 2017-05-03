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
          <div v-if="isOverview" class="p-y-2 p-x-2">
            <h2>{{trans('filter_suppliers')}}</h2>
            <FilterComponent
              :placeholder="trans('filter_search_suppliers')"
              :list="this.$store.getters.suppliers"
              itemID="supplier_id"
              label="name"
              @active="onFilterActive"
            />
          </div>
          <div v-else class="p-y-2 p-x-2">
            <h2>{{trans('filter_movements_type')}}</h2>
            <PSSelect :items="movementsTypes" itemID="id_stock_mvt_reason" itemName="display_name">
              {{trans('none')}}
            </PSSelect>
            <h2 class="m-t-2">{{trans('filter_movements_employee')}}</h2>
            <PSSelect :items="employees" itemID="id_employee" itemName="display_name">
             {{trans('none')}}
            </PSSelect>
            <h2 class="m-t-2">{{trans('filter_movements_period')}}</h2>
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
        <PSButton type="button" class="pull-right m-y-2 m-x-2" :primary="true" :disabled="disabled" @click="onClick">
          <i class="material-icons m-r-1">filter_list</i>
          {{trans('button_apply_advanced_filter')}}
        </PSButton>
      </div>
    </div>
  </div>
</template>

<script>
  import FilterComponent from './filters/filter-component';
  import PSSelect from 'app/widgets/ps-select';
  import PSButton from 'app/widgets/ps-button';

  export default {
    computed : {
      isOverview() {
        return this.$route.name === 'overview';
      },
      employees() {
        return this.$store.getters.employees;
      },
      movementsTypes() {
        return this.$store.getters.movementsTypes;
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
      FilterComponent,
      PSSelect,
      PSButton
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
