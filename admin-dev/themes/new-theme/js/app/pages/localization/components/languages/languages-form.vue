<template>
  <form>
    <div class="row">
      <div  class="col-sm-4">
        <label>{{trans('label_currency')}}</label>
        <PSHelp 
          content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus." 
          placement="right"
        />
        <PSSelect id="select-languages" data-toggle="select2">--</PSSelect>
      </div>
      <div  class="col-sm-2">
        <label class="d-block">{{trans('label_switch')}}</label>
        <PSSwitch />
      </div>
    </div>
    <div class="row align-items-end mt-4">
      <div  class="col-sm-5">
        <label>{{trans('label_language_name')}}</label>
        <PSInput />
      </div>
      <div class="col-sm-1">
        <label class="d-block">
          {{trans('label_switch_rtl')}}
          <PSHelp 
            content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus." 
            placement="right"
          />
        </label>
        <PSSwitch />
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
        <label>{{trans('label_code_language')}}</label>
        <PSHelp 
          content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus." 
          placement="right"
        />
        <PSInput />
      </div>
      <div class="col-sm-2">
        <label>{{trans('label_date_format')}}</label>
        <PSHelp 
          content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus." 
          placement="right"
        />
        <PSDatePicker />
      </div>
      <div class="col-sm-2">
        <label>
          {{trans('label_date_format_full')}}<sup>*</sup>
          <PSHelp 
            content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus." 
            placement="right"
          />
        </label>
        <PSDatePicker />
      </div>
    </div>
    <div class="row mt-4">
      <div class="col-sm-4">
        <label>
          {{trans('label_flag')}}<sup>*</sup>
          <PSHelp 
            content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus." 
            placement="right"
          />
        </label>
        <PSFile>
          {{trans('label_file')}}
        </PSFile>
      </div>
      <div class="col-sm-4">
        <label>
          {{trans('label_image')}}<sup>*</sup>
          <PSHelp 
            content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus." 
            placement="right"
          />
        </label>
        <PSFile>
          {{trans('label_file')}}
        </PSFile>
      </div>
    </div>
  </form>
</template>

<script>
  import PSSelect from 'app/widgets/ps-select';
  import PSSwitch from 'app/widgets/ps-switch';
  import PSInput from 'app/widgets/ps-input';
  import PSHelp from 'app/widgets/ps-help';
  import PSDatePicker from 'app/widgets/ps-datepicker';
  import PSFile from 'app/widgets/ps-file';

  export default {
    components: {
      PSSelect,
      PSSwitch,
      PSInput,
      PSHelp,
      PSDatePicker,
      PSFile,
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
      $('#select-languages').select2({
        data: ['english', 'french'],
      });
    },
  };
</script>

<style lang="sass" scoped>
  @import "../../../../../../scss/config/_settings.scss";
  sup {
    color: $brand-danger;
  }
</style>
