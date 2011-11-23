<script type="text/javascript" src="../modules/{$glob.module_name}/js/service.js"></script>
<p>{l s='Services which the code has only one letter are those offers to companies. So they will only be proposed to customers who filled out the Company field' mod='tntcarrier'}</p><br/>
<div style="float:right;margin-right: 100px;margin-top: 30px;">
	<table class="table" cellspacing="0" cellpading="0">
		<tr><th colspan="2">{l s='Code' mod='tntcarier'}</th></tr>
		<tr><td>N : </td><td>{l s='8:00 Express' mod='tntcarier'}</td></tr>
		<tr><td>A : </td><td>{l s='9:00 Express' mod='tntcarier'}</td></tr>
		<tr><td>T : </td><td>{l s='10:00 Express' mod='tntcarier'}</td></tr>
		<tr><td>M : </td><td>{l s='12:00 Express' mod='tntcarier'}</td></tr>
		<tr><td>J : </td><td>{l s='Express' mod='tntcarier'}</td></tr>
		<tr><td>P : </td><td>{l s='Express (P)' mod='tntcarier'}</td></tr>
		<tr><th colspan="2">{l s='Code Option (Optional)' mod='tntcarrier'}</th></tr>
		<tr><td>D : </td><td>{l s='relay package' mod='tntcarier'}</td></tr>
		<tr><td>Z : </td><td>{l s='Home delivery' mod='tntcarier'}</td></tr>
		<tr><td>&Oslash; : </td><td>{l s='Enterprise Service' mod='tntcarier'}</td></tr>
	</table>
</div>
<a href="index.php?tab={$glob.tab}&configure={$glob.configure}&token={$glob.token}&tab_module={$glob.tab_module}&module_name={$glob.module_name}&id_tab=3&section=service&action=new">
<img src="../img/admin/add.gif" alt="add"/> {l s='Add a TNT service via its specific code' mod='tntcarrier'}</a></br><br/>
<table class="table" cellspacing="0" cellpading="0">
	<tr>
		<th>{l s='Id' mod='tntcarrier'}</th><th>{l s='Name' mod='tntcarrier'}</th><th>{l s='Description' mod='tntcarrier'}</th><th>{l s='code' mod='tntcarrier'}</th><th>{l s='Additionnal Charge(Euros)' mod='tntcarrier'}</th><th>{l s='Activated' mod='tntcarrier'}</th><th></th>
	</tr>
{foreach from=$varService.serviceList  key=k item=v}
	<tr '.($irow++ % 2 ? 'class="alt_row"' : '').'>
		<td>{$v.optionId}</td>
		<td>{$v.name}</td>
		<td>{$v.delay}</td>
		<td>{$v.option}</td>
		<td>{$v.optionOvercost}</td>
		<td>
			{if $v.deleted != 1}
			<img style="cursor:pointer" onclick="changeActive(this,'{$v.optionId}')" src="../img/admin/enabled.gif" />
			{else}
			<img style="cursor:pointer" onclick="changeActive(this,'{$v.optionId}')" src="../img/admin/disabled.gif" />
			{/if}
		</td>
		<td>
			<a href="index.php?tab={$glob.tab}&configure={$glob.configure}&token={$glob.token}&tab_module={$glob.tab_module}&module_name={$glob.module_name}&id_tab=3&section=service&action=edit&service={$v.optionId}">
				<img src="../img/admin/edit.gif" alt="edit" title="{l s='edit' mod='tntcarrier'}"/></a>
			<a href="index.php?tab={$glob.tab}&configure={$glob.configure}&token={$glob.token}&tab_module={$glob.tab_module}&module_name={$glob.module_name}&id_tab=3&section=service&action=del&service={$v.optionId}">
				<img src="../img/admin/delete.gif" alt="delete" title="{l s='delete' mod='tntcarrier'}"/></a></td></tr>
{/foreach}
</table><br/>
<div id="divFormService">
{if ($varService.action == 'edit' || $varService.action == 'new') && $varService.section == 'service'}
{$varService.form}
{/if}
</div>