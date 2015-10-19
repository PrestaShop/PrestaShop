/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

$( document ).ready(function() {
	$('.btn-next').click(function(){
		$('.nav-tabs > .active').next('li').find('a').trigger('click');
	});

	$('.btn-prev').click(function(){
		$('.nav-tabs > .active').prev('li').find('a').trigger('click');
	});

	//send form
	$("#form").submit(function( event ) {
		event.preventDefault();
		var data = $(this, 'input, textarea, select').not(':input[type=button], :input[type=submit], :input[type=reset]').serialize();

		$.ajax({
			type: "POST",
			data: data,
			beforeSend: function() {
				$('#form #submit').attr("disabled", "disabled");
				$('.help-block').remove();
				$( "*.has-error" ).removeClass("has-error");
			},
			success: function(response){
				$('#form_id_product').val(response.product.id);
				showSuccessMessage(translate_javascripts['Form update success']);
			},
			error: function(response){
				showErrorMessage(translate_javascripts['Form update errors']);

				$.each(jQuery.parseJSON(response.responseText), function(key, errors){
					var html = '<span class="help-block"><ul class="list-unstyled">';
					$.each(errors, function(key, error){
						html += '<li><span class="glyphicon glyphicon-exclamation-sign"></span> ' + error + '</li>';
					});
					html += '</ul></span>';

					$('#form_'+key).parent().append(html);
					$('#form_'+key).parent().addClass('has-error');
				});

				//scroll to 1st error
				$('html, body').animate({
					scrollTop: $(".has-error").first().offset().top - $('.page-head').height() - $('.navbar-header').height()
				}, 500);
			},
			complete: function(){
				$('#form #submit').removeAttr("disabled");
			}
		});
	});

	//form nested categories ------------------

	//remove all default category list
	$('#form_step1_id_category_default').find('option:not([value='+$('#form_step1_id_category_default').val()+'])').remove();

	//add to default categories select each category seleted
	$.each($("#form_step1_categories input[type=checkbox]:checked"), function(){
		addOrRemoveCategoryToDefaultCategorySelector($(this));
	});

	$(document).on( "click", "#form_step1_categories input[type=checkbox]", function() {
		if($("#form_step1_categories input[type=checkbox]:checked").length == 0){
			return false;
		}
		addOrRemoveCategoryToDefaultCategorySelector($(this));
	});

	function addOrRemoveCategoryToDefaultCategorySelector(obj){
		if (obj.is (':checked')){
			if(obj.val() != $('#form_step1_id_category_default').val()){
				$('#form_step1_id_category_default').append('<option value="'+ obj.val() +'">'+ obj.parent().text() +'</option>');
			}
		} else  {
			$('#form_step1_id_category_default').find('option[value='+obj.val()+']').remove();
		}
	}
	//end form nested categories ------------------

	//subform add category ----------------------
	$("#form_step1_new_category button.submit").click(function(){
		$.ajax({
			type: "POST",
			url: $('#form_step1_new_category').attr('data-action'),
			data: {
				'form[category][name]': $('#form_step1_new_category_name').val(),
				'form[category][id_parent]': $('#form_step1_new_category_id_parent').val(),
				'form[_token]': $('#form #form__token').val()
			},
			beforeSend: function() {
				$('#form_step1_new_category button.submit').attr("disabled", "disabled");
				$('#form_step1_new_category .help-block').remove();
				$("#form_step1_new_category *.has-error" ).removeClass("has-error");
			},
			success: function(response){
				$("#form_step1_new_category_name").val("");

				var html = '<li><div class="checkbox"><label><input type="checkbox" name="form[step1][categories][tree][]" value="'+response.category.id+'">'+response.category.name[1]+'</label></div></li>';
				var parentElement = $("#form_step1_categories input[value="+response.category.id_parent+"]" ).parent().parent();
				if(parentElement.next('ul').length == 0){
					html = '<ul>'+html+'</ul>';
					parentElement.append(html);
				}else{
					parentElement.next('ul').append(html);
				}
			},
			error: function(response){
				$.each(jQuery.parseJSON(response.responseText), function(key, errors){
					console.log(key, errors)
					var html = '<span class="help-block"><ul class="list-unstyled">';
					$.each(errors, function(key, error){
						html += '<li><span class="glyphicon glyphicon-exclamation-sign"></span> ' + error + '</li>';
					});
					html += '</ul></span>';

					$('#form_step1_new_'+key).parent().append(html);
					$('#form_step1_new_'+key).parent().addClass('has-error');
				});
			},
			complete: function(){
				$('#form_step1_new_category button.submit').removeAttr("disabled");
			}
		});
	});
	//end subform add category ----------------------

	//manage default supplier value
	var defaultSupplierRow = $("#form_step6_default_supplier").parent().parent();
	if($("#form_step6_suppliers input:checked").length <= 1){
		defaultSupplierRow.hide();
	}
	$("#form_step6_suppliers input").change(function(){
		if($("#form_step6_suppliers input").length >= 1 && $("#form_step6_suppliers input:checked").length >= 1){
			defaultSupplierRow.show();
		} else {
			defaultSupplierRow.hide();
		}
	});
});