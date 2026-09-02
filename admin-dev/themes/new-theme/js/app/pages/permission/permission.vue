<!--*
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *-->
<template>
  <div class="card">
    <h3 class="card-header">
      {{ title }}
    </h3>
    <div class="card-body">
      <div class="table js-permissions-table">
        <bulk
          :types="types"
          :profile-permissions.sync="profileDataPermissions"
          @updateBulk="updateBulk"
        />
        <div
          class="col-xs-12"
          v-if="permissions === null"
        >
          <td colspan="6">
            {{ emptyData }}
          </td>
        </div>

        <template
          v-else
          v-for="(permission, permissionId) in permissions"
          :key="permissionId"
        >
          <row
            :can-edit="canEdit"
            :level-depth="1"
            :max-level-depth="4"
            :permission="permission"
            :permission-id="permissionId"
            :permission-key="permissionKey"
            :profile-permissions.sync="profileDataPermissions"
            :employee-permissions="employeePermissions"
            :parent="permission.children !== undefined"
            :types="Object.keys(types)"
            @sendRequest="sendRequest"
          />
        </template>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
  import {defineComponent} from 'vue';
  import Bulk from './components/bulk.vue';
  import Row from './components/row.vue';

  const {$} = window;

  interface Data {
    profileDataPermissions: Record<string, any>;
  }

  export default defineComponent({
    name: 'Permission',
    components: {
      Bulk,
      Row,
    },
    props: {
      title: {
        type: String,
        required: true,
      },
      emptyData: {
        type: String,
        required: true,
      },
      profileId: {
        type: Number,
        required: true,
      },
      messages: {
        type: Object,
        required: true,
      },
      updateUrl: {
        type: String,
        required: true,
      },
      permissionKey: {
        type: String,
        required: true,
      },
      types: {
        type: Object,
        required: true,
      },
      permissions: {
        type: Object,
        required: true,
      },
      profilePermissions: {
        type: Object,
        required: true,
      },
      employeePermissions: {
        type: Object,
        required: true,
      },
      canEdit: {
        type: Boolean,
        required: false,
        default: false,
      },
    },
    data(): Data {
      return {
        profileDataPermissions: this.profilePermissions,
      };
    },
    methods: {
      /**
       * Send ajax request to target url
       */
      sendRequest(data: Record<string, any>): void {
        data.profile_id = this.profileId;

        $.ajax(
          this.updateUrl,
          {
            method: 'POST',
            data,
          },
        ).then((response) => {
          if (response.success) {
            window.showSuccessMessage(this.messages.success);
            return;
          }

          window.showErrorMessage(this.messages.error);
        }).catch(() => {
          window.showErrorMessage(this.messages.error);
        });
      },
      /**
       * Update user permissions from bulk action
       */
      updateBulk(data: Record<string, any>): void {
        Object.keys(this.profileDataPermissions).forEach((key: string) => {
          data.types.forEach((type: string) => {
            this.profileDataPermissions[key][type] = data.status ? '1' : '0';
          });
        });

        const params: Record<string, any> = {
          is_active: data.status,
          permission: data.updateType,
        };

        params[this.permissionKey] = '-1';

        this.sendRequest(params);
      },
    },
  });
</script>

<style lang="scss" type="text/scss">
@import "~@scss/config/_settings.scss";

.js-permissions-table {
  .permission-row {
    padding: var(--#{$cdk}size-4) 0;
    border-bottom: 1px solid var(--#{$cdk}primary-500);
  }

  .bulk-row {
    padding-bottom: var(--#{$cdk}size-10);
    border-bottom: var(--#{$cdk}size-2) solid var(--#{$cdk}primary-800);
    strong {
      display: block;
      font-size: var(--#{$cdk}size-12);
      font-weight: 600;
      font-family: var(--#{$cdk}font-family-primary);
      white-space: nowrap;
      padding-bottom: var(--#{$cdk}size-5);
    }
  }
}
</style>
