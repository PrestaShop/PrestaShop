<!--*
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *-->
<template>
  <div
    class="ps-alert alert"
    :class="classObject"
    role="alert"
  >
    <button
      v-if="hasClose"
      type="button"
      class="close"
      data-dismiss="alert"
      aria-label="Close"
      @click.stop="onClick"
    >
      <span class="material-icons">close</span>
    </button>
    <p class="alert-text">
      <slot />
    </p>
  </div>
</template>

<script lang="ts">
  import {defineComponent} from 'vue';

  const ALERT_TYPE_INFO = 'ALERT_TYPE_INFO';
  const ALERT_TYPE_WARNING = 'ALERT_TYPE_WARNING';
  const ALERT_TYPE_DANGER = 'ALERT_TYPE_DANGER';
  const ALERT_TYPE_SUCCESS = 'ALERT_TYPE_SUCCESS';

  export default defineComponent({
    props: {
      duration: {
        type: Boolean,
        required: false,
        default: false,
      },
      alertType: {
        type: String,
        required: true,
      },
      hasClose: {
        type: Boolean,
        required: true,
      },
    },
    computed: {
      classObject(): {
        'alert-info': boolean,
        'alert-warning': boolean,
        'alert-danger': boolean,
        'alert-success': boolean
      } {
        return {
          'alert-info': this.alertType === ALERT_TYPE_INFO,
          'alert-warning': this.alertType === ALERT_TYPE_WARNING,
          'alert-danger': this.alertType === ALERT_TYPE_DANGER,
          'alert-success': this.alertType === ALERT_TYPE_SUCCESS,
        };
      },
      isInfo(): boolean {
        return this.alertType === ALERT_TYPE_INFO;
      },
    },
    methods: {
      onClick(): void {
        this.$emit('closeAlert');
      },
    },
  });
</script>
