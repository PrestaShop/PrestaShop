/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
