<div class="admin-box1">
<h5>{l s='A good beginning...'}
	<span style="float:right">
	<a id="optimizationTipsFold" href="#">
		<img alt="v" style="padding-top:0px; padding-right: 5px;" src="../img/admin/{if $hide_tips}down-white.gif{else}close-white.png{/if}" />
	</a>
	</span>
</h5>
			<ul id="list-optimization-tips" class="admin-home-box-list" {if $hide_tips}style="display:none"{/if} >
			{foreach from=$opti_list item=i key=k}
				<li style="background-color:{$i.color}">
				<img src="../img/admin/{$i.image}" class="pico" />
					<a href="{$i.href}">{$i.title}</a>
				</li>
				
			{/foreach}
			</ul>

</div>

<script type="text/javascript">
$(document).ready(function(){
	{if !$hide_tips}
		$("#optimizationTipsFold").click(function(e){
			e.preventDefault();
			$.ajax({
						url: "ajax-tab.php",
						type: "POST",
						data:{
							token: "{$token}",
							ajax: "1",
							controller : "AdminHome",
							action: "hideOptimizationTips"
						},
						dataType: "json",
						success: function(json){
							if(json.result == "ok")
								showSuccessMessage(json.msg);
							else
								showErrorMessage(json.msg);

						} ,
						error: function(XMLHttpRequest, textStatus, errorThrown)
						{

						}
					});

		});
	{/if}
	$("#optimizationTipsFold").click(function(e){
		e.preventDefault();
		$("#list-optimization-tips").toggle(function(){
			if($("#optimizationTipsFold").children("img").attr("src") == "../img/admin/down-white.gif")
				$("#optimizationTipsFold").children("img").attr("src","../img/admin/close-white.png");
			else
				$("#optimizationTipsFold").children("img").attr("src","../img/admin/down-white.gif");
		});
	})
});
</script>
