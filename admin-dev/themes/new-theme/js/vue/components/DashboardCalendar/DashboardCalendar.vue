<script lang="ts">
  import Vue from 'vue';
  import VCalendar from 'v-calendar';

  Vue.use(VCalendar, {
    componentPrefix: 'vc'
  });

  export default Vue.extend({
    name: 'DashboardCalendar',
    props: {
      preselectedFilter: {
        type: String,
        required: true
      },
      dateFrom: {
        type: String,
        required: true
      },
      dateTo: {
        type: String,
        required: true
      },
      customDateFrom: {
        type: String,
        required: true
      },
      customDateTo: {
        type: String,
        required: true
      },
    },
    data() {
      return {
        activeFilter: '',
        range: {
          start: new Date(this.dateFrom),
          end: new Date(this.dateTo),
        },
        customRange: {
          start: new Date(this.customDateFrom),
          end: new Date(this.customDateTo),
        },
        masks: {
          input: 'YYYY-MM-DD',
        },
      }
    },
    mounted() {
      this.setFilter(this.preselectedFilter);
    },
    methods: {
      setFilter(name: string): void {
        this.activeFilter = name;
      }
    },
  });
</script>

<style lang="scss">
  @import "~@scss/config/_settings.scss";

  .dashboard-calendar {
    .submit-custom {
      border-top-left-radius: 0;
      border-bottom-left-radius: 0;
      border-left: 0;
    }

    .vc-container.vc-blue {
      --blue-100: #{lighten($primary, 50%)};
      --blue-200: #{lighten($primary, 45%)};
      --blue-300: #{lighten($primary, 30%)};
      --blue-400: #{lighten($primary, 25%)};
      --blue-500: #{lighten($primary, 20%)};
      --blue-600: #{$primary};
      --blue-700: #{lighten($primary, 10%)};
      --blue-800: #{lighten($primary, 5%)};
      --blue-900: #{$primary};
    }
  }
</style>
