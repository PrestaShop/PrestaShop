<div style="float:right;margin-right: 100px;margin-top: 30px;">
	<table class="table" cellspacing="0" cellpading="0">
		<tr><th colspan="2">{l s='Code'}</th></tr>
		<tr><td>N : </td><td>{l s='8:00 Express'}</td></tr>
		<tr><td>A : </td><td>{l s='9:00 Express'}</td></tr>
		<tr><td>T : </td><td>{l s='10:00 Express'}</td></tr>
		<tr><td>M : </td><td>{l s='12:00 Express'}</td></tr>
		<tr><td>J : </td><td>{l s='Express'}</td></tr>
		<tr><td>P : </td><td>{l s='Express (P)'}</td></tr>
		<tr><th colspan="2">{l s='Code Option (Optional)'}</th></tr>
		<tr><td>D : </td><td>{l s='relay package'}</td></tr>
		<tr><td>Z : </td><td>{l s='Home delivery'}</td></tr>
		<tr><td>&Oslash; : </td><td>{l s='Enterprise Service'}</td></tr>
	</table>
</div>
<a href="index.php?tab={$glob.tab}&configure={$glob.configure}&token={$glob.token}&tab_module={$glob.tab_module}&module_name={$glob.module_name}&id_tab=3&section=service&action=new">
<img src="../img/admin/add.gif" alt="add"/> {$lang.newService}</a></br><br/>
<table class="table" cellspacing="0" cellpading="0">
	<tr>
		<th>{$lang.id}</th><th>{$lang.name}</th><th>{$lang.description}</th><th>{$lang.code}</th><th>{$lang.additionnalCharge}</th><th>{$lang.activated}</th><th></th>
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
			<img src="../img/admin/enabled.gif" />
			{else}
			<img src="../img/admin/disabled.gif" />
			{/if}
		</td>
		<td>
			<a href="index.php?tab={$glob.tab}&configure={$glob.configure}&token={$glob.token}&tab_module={$glob.tab_module}&module_name={$glob.module_name}&id_tab=3&section=service&action=edit&service={$v.optionId}">
				<img src="../img/admin/edit.gif" alt="edit" title="{$lang.edit}"/></a>
			<a href="index.php?tab={$glob.tab}&configure={$glob.configure}&token={$glob.token}&tab_module={$glob.tab_module}&module_name={$glob.module_name}&id_tab=3&section=service&action=del&service={$v.optionId}">
				<img src="../img/admin/delete.gif" alt="delete" title="{$lang.delete}"/></a></td></tr>
{/foreach}
</table><br/>
<div id="divFormService">
{if ($varService.action == 'edit' || $varService.action == 'new') && $varService.section == 'service'}
{$varService.form}
{/if}
</div>