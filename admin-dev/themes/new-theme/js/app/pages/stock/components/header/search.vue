<!--*
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *-->
<template>
  <div
    id="search"
    class="row mb-2"
  >
    <div class="col-md-12">
      <div class="mb-2">
        <form
          class="search-form"
          @submit.prevent
        >
          <label>{{ trans('product_search') }}</label>
          <div class="input-group">
            <PSTags
              ref="psTags"
              :tags="tags"
              @tagChange="onSearch"
            />
            <div class="input-group-append">
              <PSButton
                @click="onClick"
                class="search-button"
                :primary="true"
              >
                <i class="material-icons">search</i>
                {{ trans('button_search') }}
              </PSButton>
            </div>
          </div>
        </form>
      </div>
      <Filters
        ref="filters"
        @applyFilter="applyFilter"
      />
    </div>
    <div class="col-md-4 alert-box">
      <transition name="fade">
        <PSAlert
          v-if="showAlert"
          :alert-type="alertType"
          :has-close="true"
          @closeAlert="onCloseAlert"
        >
          <span v-if="error">{{ trans('alert_bulk_edit') }}</span>
          <span v-else>{{ trans('notification_stock_updated') }}</span>
        </PSAlert>
      </transition>
    </div>
  </div>
</template>

<script lang="ts">
  import PSTags from '@app/widgets/ps-tags.vue';
  import PSButton from '@app/widgets/ps-button.vue';
  import PSAlert from '@app/widgets/ps-alert.vue';
  import {EventEmitter} from '@components/event-emitter';
  import {defineComponent} from 'vue';
  import translate from '@app/pages/stock/mixins/translate';
  import Filters, {FiltersInstanceType} from './filters.vue';

  const Search = defineComponent({
    components: {
      Filters,
      PSTags,
      PSButton,
      PSAlert,
    },
    computed: {
      filtersRef(): FiltersInstanceType {
        return <FiltersInstanceType>(this.$refs.filters);
      },
      error(): boolean {
        return (this.alertType === 'ALERT_TYPE_DANGER');
      },
    },
    mixins: [translate],
    methods: {
      onClick(): void {
        const refPsTags = this.$refs.psTags as VTags;
        const {tag} = refPsTags;
        refPsTags.add(tag);
      },
      onSearch(): void {
        this.$emit('search', this.tags);
      },
      applyFilter(filters: Array<any>): void {
        this.$emit('applyFilter', filters);
      },
      onCloseAlert(): void {
        this.showAlert = false;
      },
    },
    watch: {
      $route() {
        this.tags = [];
      },
    },
    mounted() {
      EventEmitter.on('displayBulkAlert', (type: string) => {
        this.alertType = type === 'success' ? 'ALERT_TYPE_SUCCESS' : 'ALERT_TYPE_DANGER';
        this.showAlert = true;
        setTimeout(() => {
          this.showAlert = false;
        }, 5000);
      });
    },
    data() {
      return {
        tags: [],
        showAlert: false,
        alertType: 'ALERT_TYPE_DANGER',
        duration: false,
      };
    },
  });

  export type SearchInstanceType = InstanceType<typeof Search> | undefined;

  export default Search;
</script>
