<?php

	if (!isset($_GET['language']))
		$_GET['language'] = 0;
	function getPreinstallXmlLang($object, $field)
	{
		if (property_exists($object, $field.'_'.((int)($_GET['language'])+1)))
			return str_replace(array('!|', '|!'), array('<', '>'), trim($object->{$field.'_'.((int)($_GET['language'])+1)}));
		if (property_exists($object, $field.'_1'))
			return str_replace(array('!|', '|!'), array('<', '>'), trim($object->{$field.'_1'}));
		return '';
	}



	if ($_GET['request'] == 'form')
	{
		$p = addslashes(strtolower($_GET['partner']));
		$c = addslashes(strtolower($_GET['country_iso_code']));

		$context = stream_context_create(array('http' => array('method'=>"GET", 'timeout' => 5)));
		$content = @file_get_contents('https://www.prestashop.com/partner/preactivation/fields.php?version=1.0&partner='.$p.'&country_iso_code='.$c, false, $context);

		if ($content && $content[0] == '<')
		{
			$result = simplexml_load_string($content);
			if ($result)
			{
				$varList = "";
				echo '<br />';
				foreach ($result->field as $field)
				{
					echo '<div class="field"><label class="aligned">'.getPreinstallXmlLang($field, 'label').' :</label>';
					if ($field->type == 'text' || $field->type == 'password')
						echo '<input type="'.$field->type.'" class="text required" id="'.$p.'_'.$c.'_form_'.$field->key.'" name="'.$p.'_'.$c.'_form_'.$field->key.'" '.(isset($field->size) ? 'size="'.$field->size.'"' : '').' value="'.(isset($_GET[trim($field->key)]) ? $_GET[trim($field->key)] : $field->default).'" />';
					elseif ($field->type == 'radio')
					{
						foreach ($field->values as $key => $value)
							echo getPreinstallXmlLang($value, 'label').' <input type="radio" id="'.$p.'_'.$c.'_form_'.$field->key.'_'.$key.'" name="'.$p.'_'.$c.'_form_'.$field->key.'" value="'.$value->value.'" '.($value->value == $field->default ? 'checked="checked"' : '').' />';
					}
					elseif ($field->type == 'select')
					{
						echo '<select id="'.$p.'_'.$c.'_form_'.$field->key.'" name="'.$p.'_'.$c.'_form_'.$field->key.'" style="width:175px;border:1px solid #D41958">';
						foreach ($field->values as $key => $value)
							echo '<option id="'.$p.'_'.$c.'_form_'.$field->key.'_'.$key.'" value="'.$value->value.'" '.(trim($value->value) == trim($field->default) ? 'selected="selected"' : '').'>'.getPreinstallXmlLang($value, 'label').'</option>';
						echo '</select>';
					}
					elseif ($field->type == 'date')
					{
						echo '<select id="'.$p.'_'.$c.'_form_'.$field->key.'_year" name="'.$p.'_'.$c.'_form_'.$field->key.'_year" style="border:1px solid #D41958">';
						for ($i = 81; (date('Y') - $i) <= date('Y'); $i--)
							echo '<option value="'.(date('Y') - $i).'">'.(date('Y') - $i).'</option>';
						echo '</select>';
						echo '<select id="'.$p.'_'.$c.'_form_'.$field->key.'_month" name="'.$p.'_'.$c.'_form_'.$field->key.'_month" style="border:1px solid #D41958">';
						for ($i = 1; $i <= 12; $i++)
							echo '<option value="'.($i < 10 ? '0'.$i : $i).'">'.($i < 10 ? '0'.$i : $i).'</option>';
						echo '</select>';
						echo '<select id="'.$p.'_'.$c.'_form_'.$field->key.'_day" name="'.$p.'_'.$c.'_form_'.$field->key.'_day" style="border:1px solid #D41958">';
						for ($i = 1; $i <= 31; $i++)
							echo '<option value="'.($i < 10 ? '0'.$i : $i).'">'.($i < 10 ? '0'.$i : $i).'</option>';
						echo '</select>';
					}
					if (getPreinstallXmlLang($field, 'help'))
						echo ' '.getPreinstallXmlLang($field, 'help');
					echo '<br /></div><br clear="left" />';
					if ($field->type == 'date')
						$varList .= "'&".$field->key."='+$('#".$p."_".$c."_form_".$field->key."_year').val()+'-'+$('#".$p."_".$c."_form_".$field->key."_month').val()+'-'+$('#".$p."_".$c."_form_".$field->key."_day').val()+\n";
					else
						$varList .= "'&".$field->key."='+ encodeURIComponent($('#".$p."_".$c."_form_".$field->key."').val())+\n";
				}
				echo '
				<script>'."
					$('#btNext').click(function() {
						if (moduleChecked['".strtoupper($c).'_'.$p."'] == 1 && $('select#infosCountry option:selected').attr('rel') == '".strtoupper($c)."')
						{
							$.ajax({
							  url: 'preactivation.php?request=send'+
								'&partner=".$p."'+
								".$varList."
								'&language_iso_code='+isoCodeLocalLanguage+
								'&country_iso_code='+encodeURIComponent($('select#infosCountry option:selected').attr('rel'))+
								'&activity='+ encodeURIComponent($('select#infosActivity').val())+
								'&timezone='+ encodeURIComponent($('select#infosTimezone').val())+
								'&shop='+ encodeURIComponent($('input#infosShop').val())+
								'&firstName='+ encodeURIComponent($('input#infosFirstname').val())+
								'&lastName='+ encodeURIComponent($('input#infosName').val())+
								'&email='+ encodeURIComponent($('input#infosEmail').val()),
							  context: document.body,
							  success: function(data) {
							  }
							});
						}
					});".'
				</script>';
			}
		}

	}


	if ($_GET['request'] == 'send')
	{
		$context = stream_context_create(array('http' => array('method'=>"GET", 'timeout' => 5)));
		$url = 'https://www.prestashop.com/partner/preactivation/actions.php?version=1.0&partner='.addslashes($_GET['partner']);

		// Protect fields
		foreach ($_GET as $key => $value)
			$_GET[$key] = strip_tags(str_replace(array('\'', '"'), '', trim($value)));

		// Encore Get, Send It and Get Answers
		@require_once('../config/settings.inc.php');
		foreach ($_GET as $key => $val)
			$url .= '&'.$key.'='.urlencode($val);
		$url .= '&security='.md5($_GET['email']._COOKIE_IV_);
		$content = @file_get_contents($url, false, $context);
		if ($content)
			echo $content;
		else
			echo 'KO|Could not connect with Prestashop.com';
	}

?>
