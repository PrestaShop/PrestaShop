/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
// eslint-disable-next-line
function PerformancePage(addServerUrl, removeServerUrl, testServerUrl) {
  this.addServerUrl = addServerUrl;
  this.removeServerUrl = removeServerUrl;
  this.testServerUrl = testServerUrl;

  this.getAddServerUrl = function () {
    return this.addServerUrl;
  };

  this.getRemoveServerlUrl = function () {
    return this.removeServerUrl;
  };
  this.getTestServerUrl = function () {
    return this.testServerUrl;
  };

  this.getFormValues = function () {
    return {
      server_ip: document.getElementById('memcache_ip').value,
      server_port: document.getElementById('memcache_port').value,
      server_weight: document.getElementById('memcache_weight').value,
    };
  };

  this.createRow = function (params) {
    const serversTable = document.getElementById('servers-table');
    const newRow = document.createElement('tr');
    newRow.setAttribute('id', `row_${params.id}`);
    newRow.innerHTML = `<td>${params.id}</td>\n`
            + `<td>${params.server_ip}</td>\n`
            + `<td>${params.server_port}</td>\n`
            + `<td>${params.server_weight}</td>\n`
            + '<td>\n'
            // eslint-disable-next-line
            + `    <a class="btn btn-default" href="#" onclick="app.removeServer(${params.id});"><i class="material-icons">remove_circle</i> Remove</a>\n`
            + '</td>\n';
    serversTable.appendChild(newRow);
  };

  this.addServer = function () {
    const app = this;
    this.send(this.getAddServerUrl(), 'POST', this.getFormValues(), (results) => {
      // eslint-disable-next-line
      if (!results.hasOwnProperty('error')) {
        app.createRow(results);
      }
    });
  };

  this.removeServer = function (serverId, removeMsg) {
    const removeOk = confirm(removeMsg);

    if (removeOk) {
      this.send(this.getRemoveServerlUrl(), 'DELETE', {server_id: serverId}, (results) => {
        if (results === undefined) {
          const row = document.getElementById(`row_${serverId}`);
          row.parentNode.removeChild(row);
        }
      });
    }
  };

  this.testServer = function () {
    const app = this;

    this.send(this.getTestServerUrl(), 'GET', this.getFormValues(), (results) => {
      // eslint-disable-next-line
      if (results.hasOwnProperty('error') || results.test === false) {
        app.addClass('is-invalid');
        return;
      }

      app.addClass('is-valid');
    });
  };

  this.addClass = function (className) {
    const serverFormInputs = document.querySelectorAll('#server-form input[type=text]');

    for (let i = 0; i < serverFormInputs.length; i += 1) {
      serverFormInputs[i].className = `form-control ${className}`;
    }
  };

  this.send = function (url, method, params, callback) {
    return $.ajax({
      url,
      method,
      data: params,
    }).done(callback);
  };
}
