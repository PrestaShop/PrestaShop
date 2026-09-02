<!--*
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *-->
<template>
  <div class="d-flex flex-wrap bulk-row">
    <div class="col-4" />

    <div class="col-8 d-flex flex-wrap">
      <div
        class="text-center"
        :class="getClasses(types, bulkType === 'view')"
        v-for="(bulk, bulkType) in types"
        :key="bulkType"
      >
        <strong>{{ bulk.label }}</strong>
        <ps-checkbox
          v-model="status"
          @change="updateBulk(bulkType)"
          :value="bulkType"
          :disabled="bulk.value !== true"
        />
      </div>
    </div>
  </div>
</template>

<script lang="ts">
  import PsCheckbox from '@app/components/checkbox.vue';
  import ColSize from '@app/mixins/col-size.vue';
  import {defineComponent} from 'vue';

  const TYPE_ALL: string = 'all';

  export default defineComponent({
    mixins: [
      ColSize,
    ],
    components: {
      PsCheckbox,
    },
    props: {
      types: {
        type: Object,
        required: true,
      },
      profilePermissions: {
        type: Object,
        required: true,
      },
    },
    data(): {status: Array<string>} {
      return {
        status: [],
      };
    },
    watch: {
      profilePermissions: {
        handler: function mandatoryFunctionForDeepWatching(val): void {
          this.refreshPermissionsCheckboxes(val);
        },
        deep: true,
      },
    },
    mounted() {
      this.refreshPermissionsCheckboxes(this.profilePermissions);
    },
    methods: {
      /**
       * Check if checkboxes must be checked
       */
      refreshPermissionsCheckboxes(permissions: Record<string, any>): void {
        Object.keys(this.types).forEach((t: string) => {
          if (t === TYPE_ALL) {
            return;
          }

          let isChecked = true;

          // eslint-disable-next-line no-restricted-syntax
          for (const perm of Object.values(permissions)) {
            if (perm[t] === '0') {
              isChecked = false;

              break;
            }
          }

          if (isChecked && !this.status.includes(t)) {
            this.status.push(t);
          } else if (this.status.includes(t) && !isChecked) {
            this.status.splice(this.status.indexOf(t), 1);
          }
        });

        if (this.status.length === 1 && this.status.includes(TYPE_ALL)) {
          this.status.splice(this.status.indexOf(TYPE_ALL), 1);
        }

        this.checkForTypeAllCheckbox();
      },
      /**
       * Check is type all must be checked
       */
      checkForTypeAllCheckbox(bulkType?: string): void {
        // no need to check there is no type all
        if (!(TYPE_ALL in this.types)) {
          return;
        }

        if (bulkType === TYPE_ALL) {
          this.status = this.status.includes(bulkType)
            ? Object.keys(this.types)
            : [];
          return;
        }

        // Nothing change
        if (this.status.length !== (Object.keys(this.types).length - 1)) {
          return;
        }

        // We can add the TYPE_ALL because we check all checkboxes
        if (this.status.includes(TYPE_ALL)) {
          this.status.splice(this.status.indexOf(TYPE_ALL), 1);
        } else {
          this.status.push(TYPE_ALL);
        }
      },
      /**
       * Update bulk type
       */
      updateBulk(bulkType: string): void {
        this.checkForTypeAllCheckbox(bulkType);
        this.$emit(
          'updateBulk',
          {
            updateType: bulkType,
            status: this.status.includes(bulkType),
            types: bulkType !== TYPE_ALL ? [bulkType] : Object.keys(this.types),
          },
        );
      },
    },
  });
</script>
