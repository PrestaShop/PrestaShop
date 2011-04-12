var TSPayment = (function()
{
	return {
		payment_type			: {},
		payment_module			: {},
		alert_message			: '',
		module_box				: {
			html	: '<b class="payment-module-label"></b>',
			class	: '.payment-module-label',
			id		:'label-module-',
			css		: {
				display			: 'none',
				padding			:'2px 5px 2px 5px',
				margin			: '0 5px 0 5px',
				cursor			: 'pointer',
				backgroundColor	: '#e2e2e2',
				color			: '#666666'
			}
		},
		alertMessage			: function() {
			if (TSPayment.alert_message != '') {
				alert(TSPayment.alert_message);
			}
		},
		deleteModuleFromList	: function(id_module) {
			$('select[name=payment_module]').find('option[value='+id_module+']').remove();
		},
		setLabelModuleName		: function(id_module) {
			if ($('#'+TSPayment.module_box.id+id_module).length) {
				$('#'+TSPayment.module_box.id+id_module).text(TSPayment.payment_module[id_module].name)
			}
		},
		init					: function() {
			clickAddFunction();
			clickRemoveFunction();
			hoverEffect();
		}
	};
	function hoverEffect()
	{
		$(TSPayment.module_box.class).live('mouseover', function(){
			$(this).css({backgroundColor:'#CF0000', opacity:0.6});
		});
		$(TSPayment.module_box.class).live('mouseout', function(){
			$(this).css({backgroundColor:TSPayment.module_box.css.backgroundColor, opacity:1});
			
		});
	}
	function addModule (payment_type, id_module)
	{
		$('#input-hidden-val').append('<input class="choosen_payment_type" style="display:none;" type="hidden" value="'+id_module+'" name="choosen_payment_type['+payment_type+'][]">');
		if(!$('#block-payment-'+payment_type).length)
		{
			$("#payment_type_list").append('<label style="clear:both;"class="payment-type-label" >'+TSPayment.payment_type[payment_type]+'</label>');
			$("#payment_type_list").append('<div class="margin-form" id="block-payment-'+payment_type+'" ></div>');
		}
		$('#block-payment-'+payment_type).append($(TSPayment.module_box.html).attr("id", TSPayment.module_box.id+id_module).css(TSPayment.module_box.css).fadeIn());
		TSPayment.setLabelModuleName(id_module);
		TSPayment.deleteModuleFromList(id_module);
	}
	function clickAddFunction()
	{
		$('input[name=add_payment_module]').click(function()
		{
			var boolean = true;
			var payment_type = $('select[name=payment_type]').val();
			var payment_module = $('select[name=payment_module]').val();
			$('.choosen_payment_type').each(function() {
				var reg = new RegExp(payment_type, 'i');
				if ($(this).val() == payment_module)
					boolean = false;
			});
			if (boolean) {
				addModule(payment_type, payment_module);
				TSPayment.alert_message = '';
			}
			else
				TSPayment.alert_message = 'A payment module can be choosen for only one payment type';
			
			TSPayment.alertMessage();
		});
	}
	function clickRemoveFunction()
	{
		$('.payment-module-label').live('click', function()
		{
			var id_module = $(this).attr('id').split('-')[2];
			var input_to_delete = $('#input-hidden-val').find('input[value='+id_module+']');
			input_to_delete.remove();
			$('select[name=payment_module]').append('<option value="'+id_module+'" >'+TSPayment.payment_module[id_module].name+'</option>');
			$(this).fadeOut('fast', function()
			{
				var bloc_parent = $(this).parent(); 
				if (bloc_parent.find('b').length == 1)
				{
					bloc_parent.prev('label').remove();
					bloc_parent.remove();
				}
				$(this).remove();
			});
		});
	}
})();