{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}

{if count($groups) && isset($groups)}
<div class="row">
	<div class="col-lg-6">
		<table class="table table-bordered">
			<thead>
				<tr>
					<th class="fixed-width-xs">
						<span class="title_box">
							<input type="checkbox" name="checkme" id="checkme" onclick="checkDelBoxes(this.form, 'groupBox[]', this.checked)" />
						</span>
					</th>
					<th class="fixed-width-xs"><span class="title_box">{l s='ID' d='Admin.Global'}</span></th>
					<th>
						<span class="title_box">
							{l s='Group name'}
						</span>
					</th>
				</tr>
			</thead>
			<tbody>
			{foreach $groups as $key => $group}
				<tr>
					<td>
						{assign var=id_checkbox value=groupBox|cat:'_'|cat:$group['id_group']}
						<input type="checkbox" name="groupBox[]" class="groupBox" id="{$id_checkbox}" value="{$group['id_group']}" {if $fields_value[$id_checkbox]}checked="checked"{/if} />
					</td>
					<td>{$group['id_group']}</td>
					<td>
						<label for="{$id_checkbox}">{$group['name']}</label>
					</td>
				</tr>
			{/foreach}
			</tbody>
		</table>
	</div>
</div>
{else}
<p>
	{l s='No group created'}
</p>
{/if}
