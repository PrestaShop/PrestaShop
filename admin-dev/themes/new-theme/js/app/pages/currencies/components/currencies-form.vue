<template>
  <form>
    <div class="row">
      <div  class="col-sm-4">
        <label>{{trans('label_currency')}}</label>
        <PSHelp 
          content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus." 
          placement="right"
        />
        <PSSelect data-toggle="select2">--</PSSelect>
      </div>
      <div  class="col-sm-2">
        <label class="d-block">{{trans('label_switch')}}</label>
        <PSSwitch />
      </div>
    </div>
    <div class="row align-items-end mt-4">
      <div  class="col-sm-5">
        <label>{{trans('label_currency_name')}}</label>
        <PSInput />
      </div>
      <div class="col-sm-1">
        <label>{{trans('label_symbol')}}</label>
        <PSInput />
      </div>
      <div class="col-sm-1 align-content-end">
        <PSSelect>en</PSSelect>
      </div>
    </div>
    <div class="row mt-4">
      <div  class="col-sm-2">
        <label>{{trans('label_code')}}</label>
        <PSHelp 
          content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus." 
          placement="right"
        />
        <PSInput />
      </div>
      <div class="col-sm-2">
        <label>{{trans('label_code_numeric')}}</label>
        <PSHelp 
          content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus." 
          placement="right"
        />
        <PSInput />
      </div>
      <div class="col-sm-1">
        <label>{{trans('label_decimals')}}</label>
        <PSHelp 
          content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus." 
          placement="right"
        />
        <PSSelect :items="decimals" itemName="number">0</PSSelect>
      </div>
      <div class="col-sm-1">
        <label>{{trans('label_exchange')}}<sup>*</sup></label>
        <PSInput />
      </div>
    </div>
  </form>
</template>

<script>
  import PSSelect from 'app/widgets/ps-select';
  import PSSwitch from 'app/widgets/ps-switch';
  import PSInput from 'app/widgets/ps-input';
  import PSHelp from 'app/widgets/ps-help';

  export default {
    components: {
      PSSelect,
      PSSwitch,
      PSInput,
      PSHelp,
    },
    computed: {
      decimals() {
        return [{
          number: 1,
        }, {
          number: 2,
        }, {
          number: 3,
        }];
      },
    },
    mounted() {
      $('[data-toggle="select2"]').select2({
        data: ['dollar', 'euro']
      });
    },
  };
</script>

<style lang="sass" scoped>
  @import "../../../../../scss/config/_settings.scss";
  sup {
    color: $brand-danger;
  }
</style>
