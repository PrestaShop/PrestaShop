{if isset($php_errors) && $php_errors|@count}
<div class="bootstrap">
	<div id="error-modal" class="modal fade">
		<div class="modal-dialog">
			<div class="alert alert-danger clearfix">
				{foreach $php_errors item=php_error}
					{l s='%1$s on line %2$s in file %3$s: [%4$s] %5$s' sprintf=[$php_error.type, $php_error.errline, $php_error.errfile, $php_error.errno, $php_error.errstr]}<br /><br />
				{/foreach}
				<button type="button" class="btn btn-default pull-right" data-dismiss="modal"><i class="icon-remove"></i> Close</button>
			</div>
		</div>
	</div>
</div>
{/if}