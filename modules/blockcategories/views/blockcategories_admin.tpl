{for $foo=0 to 2}
<div class="form-group">
	<input id="{$name}-{$foo}" type="file" name="{$name}-{$foo}" class="hide"/>
	<div class="dummyfile input-group">
		<span class="input-group-addon"><i class="icon-file"></i></span>
		<input id="{$name}-name-{$foo}" type="text" class="disabled" readonly/>
		<span class="input-group-btn">
			<button id="{$name}-selectbutton-{$foo}" type="button" class="btn btn-default">
				<i class="icon-folder-open"></i> {l s='Choose a file'}
			</button>
		</span>
	</div>
{if isset($images.$foo) && $images.$foo}
	<div class="clearfix">&nbsp;</div>
	<div id="image" class="thumbnail">
		{$images.$foo.image}
		<div class="text-center">
			<a class="btn btn-default" href="">
				<i class="icon-remove text-danger"></i> {l s='Delete'}
			</a>
		</div>
	</div>
{/if}
</div>
{/for}

<script>
	$(document).ready(function() {
		$('button[id|="{$name}-selectbutton"]').click(function(e) {			
			var id = $(this).prop('id').replace('{$name}-selectbutton-', '');
			$('#{$name}-'+id).trigger('click');
		});
		$('input[id|="{$name}-name"]').click(function(e){
			var id = $(this).prop('id').replace('{$name}-name-', '');
			$('#{$name}-'+id).trigger('click');
		});
		$('input[id|="{$name}"]').change(function(e){
			var val = $(this).val();
			var id = $(this).prop('id').replace('{$name}-', '');
			var file = val.split(/[\\/]/);
			$('#{$name}-name-'+id).val(file[file.length-1]);
		});
	});
</script>
