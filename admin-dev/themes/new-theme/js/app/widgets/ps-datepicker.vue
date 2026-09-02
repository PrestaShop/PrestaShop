<!--*
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *-->
<template>
  <div class="input-group date">
    <input
      ref="datepicker"
      type="text"
      :class="['form-control', `datepicker-${type}`]"
    >
    <div class="input-group-append">
      <span class="input-group-text">
        <i class="material-icons">event</i>
      </span>
    </div>
  </div>
</template>

<script lang="ts">
  import {defineComponent} from 'vue';

  export default defineComponent({
    props: {
      locale: {
        type: String,
        required: true,
        default: 'en',
      },
      type: {
        type: String,
        required: true,
      },
    },
    mounted() {
      $(<HTMLInputElement> this.$refs.datepicker).datetimepicker({
        format: 'YYYY-MM-DD',
        showClear: true,
        useCurrent: false,
      }).on('dp.change', (infos: Record<string, any>) => {
        infos.dateType = this.type;
        this.$emit(
          infos.date ? 'dpChange' : 'reset',
          infos,
        );
      });
    },
  });
</script>

<style lang="scss">
  @import '~@scss/config/_settings.scss';

  .date {
    a[data-action='clear']::before {
      font-family: var(--#{$cdk}font-family-material-icons);
      content: "\E14C";
      font-size: var(--#{$cdk}size-20);
      position: absolute;
      bottom: var(--#{$cdk}size-16);
      left: 50%;
      margin-left: calc(-1 * var(--#{$cdk}size-10));
      color: var(--#{$cdk}primary-800);
      cursor: pointer;
    }
    .bootstrap-datetimepicker-widget tr td span:hover {
      background-color: var(--#{$cdk}white);
    }
  }

</style>
