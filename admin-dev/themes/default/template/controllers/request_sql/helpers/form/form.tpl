{**
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
 *}

{extends file="helpers/form/form.tpl"}

{block name="after"}
<div class="row">
	<div class="col-lg-3">
		<div class="panel">
			<h3><i class="icon-list"></i> {l s='List of MySQL Tables' d='Admin.Advparameters.Feature'}</h3>
			<div class="form-group" id="selectTables">
				<select id="table" size="10">
					{foreach $tables as $table}
						<option value="{$table}">{$table}</option>
					{/foreach}
				</select>
			</div>
			<div class="form-group">
				<button type="button" id="add_table" class="btn btn-default"><i class="icon-plus-sign"></i> {l s='Add table name to SQL query' d='Admin.Advparameters.Feature'}</button>
			</div>
		</div>
	</div>
	<div class="col-lg-9">
		<div class="panel">
			<h3><i class="icon-list"></i> {l s='List of attributes for this MySQL table' d='Admin.Advparameters.Feature'}</h3>
			<div id="listAttributes">
				<div class="alert alert-warning">{l s='Please choose a MySQL table' d='Admin.Advparameters.Notification'}</div>
			</div>
		</div>
	</div>
</div>

{/block}

{block name="script"}
	$(document).ready(function() {
		$('#selectTables select option').click(function(){
			var table = $(this).val();
			//list attributes:
			$.ajax({
				url: 'index.php',
				data: {
					table: table,
					controller: 'adminrequestsql',
					token: '{$token|escape:'html':'UTF-8'}',
					action: 'addrequest_sql',
					ajax: true
				},
				context: document.body,
				dataType: 'json',
				context: this,
				async: false,
				success: function(data){
					var html = "<table class='table'>";
						html += "<thead>";
							html += "<tr>";
								html += "<th><span class=\"title_box\">{l s='Attribute'}</span></th>";
								html += "<th class=\"fixed-width-md\"><span class=\"title_box\">{l s='Type' d='Admin.Global'}</span></th>";
								html += "<th class=\"fixed-width-md\"><span class=\"title_box\">{l s='Action' d='Admin.Advparameters.Feature'}</span></th>";
							html += "</tr>";
						html += "</thead>";
						html += "<tbody>";
						for (var i=0; i < data.length; i++)
						{
							html += "<tr>";
								html += "<td>"+data[i].Field+"</td>";
								html += "<td>"+data[i].Type+"</td>";
								html += "<td><button type=\"button\" class=\"btn btn-default\" onclick=\"javascript:AddRequestSql('"+data[i].Field+"');\">{l s='Add attribute to SQL query' d='Admin.Advparameters.Feature'}</button></td>";
							html += "</tr>";
						}
						html += "</tbody>";
					html += "</table>";
					$('#listAttributes').html(html);
				}
			});
		});

		$('#add_table').click(function(){
			var table = $('#selectTables select').val();

			if (!table)
				jAlert("{l s='Please choose a table.' js=1 d='Admin.Advparameters.Feature'}");
			else
				AddRequestSql(table);
		});
	});

	function AddRequestSql(string)
	{
		var sql = $('#sql').val();
		$('#sql').val(sql+' '+string);
		return false;
	}
{/block}