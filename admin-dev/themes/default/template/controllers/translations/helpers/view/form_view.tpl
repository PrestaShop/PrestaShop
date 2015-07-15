{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<script>
	//build modal validation
	function valid_modal(msg) {
		var modal =
			$('<div class="bootstrap modal hide fade">' +
			'<div class="modal-dialog">' +
			'<div class="modal-content">' +
			'<div class="modal-body">' +
			'<a class="close" data-dismiss="modal" >&times;</a>' +
			'<p>' + msg + '</p>' +
			'</div>' +
			'</div>' +
			'</div>' +
			'</div>');
		modal.modal('show');
	}

	$(document).ready(function() {
		var saveAndStay = false;
		$("button[type='submit']").click(function() {
			saveAndStay = $(this).prop('name').indexOf('Stay') > 0 ? true : false;
		});

		//Override translation form submit
		$( "#translations_form" ).submit(function( event ) {
			event.preventDefault();

			$('#form-translation-error').addClass('hide');
			$("button[type='submit']").prop('disabled', 'disabled');

			$.post( $(this).attr('action'), {
				type: $("input[name='type']").val(),
				lang: $("input[name='lang']").val(),
				theme: $("input[name='theme']").val(),
				data: JSON.stringify($( this ).serializeArray())
			}).done(function(data) {
				var response = JSON.parse(data);
				if(response && response.errors){
					$('#form-translation-error').removeClass('hide').find('.alert').text(response.msg);
					$('html,body').scrollTop(0);
				}else{
					if(saveAndStay){
						valid_modal(response.msg);
					}else{
						window.location.href = response.redirect;
					}
				}

				$("button[type='submit']").removeProp('disabled');
			}, "json");

			return false;
		});
	}); //end dom ready
</script>
<div id="form-translation-error" class="bootstrap hide">
	<div class="alert alert-danger"></div>
</div>

<div class="leadin">{block name="leadin"}{/block}</div>

{block name="override_tpl"}{/block}

{hook h='displayAdminView'}
{if isset($name_controller)}
	{capture name=hookName assign=hookName}display{$name_controller|ucfirst}View{/capture}
	{hook h=$hookName}
{elseif isset($smarty.get.controller)}
	{capture name=hookName assign=hookName}display{$smarty.get.controller|ucfirst|htmlentities}View{/capture}
	{hook h=$hookName}
{/if}
