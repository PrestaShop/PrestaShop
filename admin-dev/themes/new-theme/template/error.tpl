{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
{if isset($php_errors) && $php_errors|@count}
<div class="bootstrap">
	<div id="error-modal" class="modal fade">
		<div class="modal-dialog">
			<div class="alert alert-danger clearfix">
				{foreach $php_errors item=php_error}
					{l s='%1$s on line %2$s in file %3$s' sprintf=[$php_error.type|escape, $php_error.errline|escape, $php_error.errfile|escape] d='Admin.Notifications.Error'}<br />
					[{$php_error.errno|escape}] {$php_error.errstr|escape}<br /><br />
				{/foreach}
				<button type="button" class="btn btn-default float-right" data-dismiss="modal"><i class="icon-remove"></i> {l s='Close' d='Admin.Actions'}</button>
			</div>
		</div>
	</div>
</div>
{/if}
