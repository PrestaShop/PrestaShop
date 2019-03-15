
<!--**
     * 2007-2019 PrestaShop and Contributors
     *
     * NOTICE OF LICENSE
     *
     * This source file is subject to the Open Software License (OSL 3.0)
     * that is bundled with this package in the file LICENSE.txt.
     * It is also available through the world-wide-web at this URL:
     * https://opensource.org/licenses/OSL-3.0
     * If you did not receive a copy of the license and are unable to
     * obtain it through the world-wide-web, please send an email
     * to license@prestashop.com so we can send you a copy immediately.
     *
     * DISCLAIMER
     *
     * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
     * versions in the future. If you wish to customize PrestaShop for your
     * needs please refer to https://www.prestashop.com for more information.
     *
     * @author    PrestaShop SA <contact@prestashop.com>
     * @copyright 2007-2019 PrestaShop SA and Contributors
     * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
     * International Registered Trademark & Property of PrestaShop SA
     *-->
<template>
  <div class="card">
    <h3 class="card-header">
      {{ messages.menu }}
    </h3>
    <div class="card-body">
      <div class="table js-permissions-table">
        <bulk
          :types="types"
          :profile-permissions.sync="profileDataPermissions"
          @updateBulk="updateBulk"
        />
        <div class="col-xs-12" v-if="permissions === null">
          <td colspan="6">{{ messages.noMenu }}</td>
        </div>

        <template v-else v-for="permission, permissionId in permissions">
          <row
            :can-edit="canEdit"
            :level-depth="1"
            :max-level-depth="4"
            :permission="permission"
            :permission-id="permissionId"
            :permission-key="permissionKey"
            :profile-permissions.sync="profileDataPermissions"
            :employee-permissions="employeePermissions"
            :parent="true"
            :types="Object.keys(types)"
            @sendRequest="sendRequest"
          >
          </row>
        </template>
      </div>
    </div>
  </div>
</template>

<script>
  import Bulk from './components/bulk.vue';
  import Row from './components/row.vue';

  const $ = window.$;

  export default {
    name: 'permission',
    components: {
      Bulk,
      Row,
    },
    props: {
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
    data() {
      return {
        profileDataPermissions: this.profilePermissions,
      };
    },
    methods: {
      /**
       * Send ajax request to target url
       */
      sendRequest(data) {
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
      updateBulk(data) {
        Object.keys(this.profileDataPermissions).forEach((key) => {
          this.profileDataPermissions[key][data.type] = data.status ? '1' : '0';
        });
        const params = {
          expected_status: data.status,
          permission: data.type,
        };
        params[this.permissionKey] = '-1';

        this.sendRequest(params);
      },
    },
  };
</script>

<style lang="scss">
  .js-permissions-table {
    .permission-row {
      padding: 4px 0;
      border-bottom: 1px solid #bbcdd2;
    }

    .bulk-row {
      font-size: 12px;
      border-bottom: .125rem solid #25b9d7;
    }
  }
</style>
