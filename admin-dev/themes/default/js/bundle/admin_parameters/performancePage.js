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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
function PerformancePage(addServerUrl, removeServerUrl, testServerUrl) {
    this.addServerUrl = addServerUrl;
    this.removeServerUrl = removeServerUrl;
    this.testServerUrl = testServerUrl;

    this.getAddServerUrl = function() {
        return this.addServerUrl;
    };

    this.getRemoveServerlUrl = function() {
        return this.removeServerUrl;
    };
    this.getTestServerUrl = function() {
        return this.testServerUrl;
    };

    this.getFormValues = function() {
        var serverIpInput = document.getElementById('form_add_memcache_server_memcache_ip');
        var serverPortInput = document.getElementById('form_add_memcache_server_memcache_port');
        var serverWeightInput = document.getElementById('form_add_memcache_server_memcache_weight');

        return {
            'server_ip': serverIpInput.value,
            'server_port': serverPortInput.value,
            'server_weight': serverWeightInput.value,
        };
    };

    this.createRow = function(params) {
        var serversTable = document.getElementById('servers-table');
        var newRow = document.createElement('tr');
        newRow.setAttribute('id', 'row_'+ params.id);
        newRow.innerHTML =
            '<td>'+ params.id +'</td>\n' +
            '<td>'+ params.server_ip +'</td>\n' +
            '<td>'+ params.server_port +'</td>\n' +
            '<td>'+ params.server_weight +'</td>\n' +
            '<td>\n' +
            '    <a class="btn btn-default" href="#" onclick="app.removeServer('+ params.id +');"><i class="material-icons">remove_circle</i> Remove</a>\n' +
            '</td>\n';
        serversTable.appendChild(newRow);
    };

    this.addServer = function() {
        var app = this;
        this.send(this.getAddServerUrl(), 'POST', this.getFormValues(), function(results) {
            if (!results.hasOwnProperty('error')) {
                app.createRow(results);
            }
        });
    };

    this.removeServer = function(serverId, removeMsg) {
        var removeOk = confirm(removeMsg);

        if (removeOk) {
            this.send(this.getRemoveServerlUrl(), 'DELETE', {'server_id': serverId}, function(results) {
                if (results === undefined) {
                    var row = document.getElementById('row_'+serverId);
                    row.parentNode.removeChild(row);
                }
            });
        }

    };

    this.testServer = function() {
        var app = this;

        this.send(this.getTestServerUrl(), 'GET', this.getFormValues(), function(results) {
            if (results.hasOwnProperty('error') || results.test === false) {
                app.addClass('is-invalid');
                return;
            }

            app.addClass('is-valid');
        });
    };

    this.addClass = function(className) {
      var serverFormInputs = document.querySelectorAll('#server-form input[type=text]');
      for (var i = 0; i < serverFormInputs.length; i++) {
        serverFormInputs[i].className = 'form-control '+ className;
      }
    }

    /* global $ */
    this.send = function(url, method, params, callback) {
        return $.ajax({
            url: url,
            method: method,
            data: params
        }).done(callback);
    };
}
