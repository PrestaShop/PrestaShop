<?php
/*
* 2007-2011 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license	http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminCounty extends AdminTab
{
	public function __construct()
	{
		global $cookie;

	 	$this->table = 'county';
	 	$this->className = 'County';
	 	$this->edit = true;
		$this->delete = true;
	 	$this->_select = 's.`name` AS state';
	 	$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'state` s ON (s.`id_state` = a.`id_state`)';

		$this->fieldsDisplay = array(
		'id_county' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
		'name' => array('title' => $this->l('County'), 'width' => 130, 'filter_key' => 'b!name'),
		'state' => array('title' => $this->l('State'), 'width' => 70, 'filter_key' => 's!name'),
		'a!active' => array('title' => $this->l('Enabled'), 'align' => 'center', 'active' => 'status', 'type' => 'bool', 'orderby' => false, 'filter_key' => 'a!active'));

		parent::__construct();
	}

	public function renderJS()
	{
		return
		'<script type="text/javascript">
		function addZipCode()
		{
			zipcodes = $("#zipcodes").val();
			id_county = $("#id_county").val();

			$.ajax({
				type: "POST",
				url: "ajax.php",
				data: "ajaxAddZipCode=1&zipcodes="+zipcodes+"&id_county="+id_county+"&token='.$this->token.'",
				async : true,
				success: function(msg) {
					res = msg.split(":");
					if (res.length == 2)
					{
						$("#error-msg").html(res[1]);
						$("#zipcodes").css("border", "2px solid #FF0000");
					} else {
						$("#error-msg").html("");
						$("#zipcodes").val("");
						$("#zipcodes").css("border", "1px solid #E0D0B1");
						$("#zipcodes-list").html(msg);
					}
				}
			});
		}

		function populateStates(id_country, id_state)
		{
			$.ajax({
			  url: "ajax.php",
			  cache: false,
			  data: "ajaxStates=1&no_empty=false&id_country="+id_country+"&id_state="+id_state,
			  success: function(html){
				$("#id_state").html(html);
			  }
			});
		}

		function removeZipCodes(zipcodes)
		{
			id_county = $("#id_county").val();

			$.ajax({
				type: "POST",
				url: "ajax.php",
				data: "ajaxRemoveZipCode=1&zipcodes="+zipcodes+"&id_county="+id_county+"&token='.$this->token.'",
				async : true,
				success: function(msg) {
						$("#zipcodes-list").html(msg);
				}
			});
		}
		</script>';
	}

	public function displayForm($isMainTab = true)
	{
		global $currentIndex, $cookie;
		parent::displayForm();

		if (!($obj = $this->loadObject(true)))
			return;

		$cur_id_country = 0;
		$cur_id_state = 0;
		if (isset($obj->id_state))
		{
			$cur_state = new State($obj->id_state);
			if (Validate::isLoadedObject($cur_state))
			{
				$cur_id_country = $cur_state->id_country;
				$cur_id_state = $cur_state->id;
			}
		}

		echo $this->renderJS().
		'<form action="'.$currentIndex.'&submitAdd'.$this->table.'=1&token='.$this->token.'" method="post">
		'.($obj->id ? '<input type="hidden" id="id_county" name="id_'.$this->table.'" value="'.$obj->id.'" />' : '').'
			<fieldset><legend><img src="../img/admin/world.gif" />'.$this->l('Counties').'</legend>';

				if (!isset($obj->id))
					echo '<div class="hint clear" style="display:block;">'.$this->l('Save this county then you will be able to associate zipcodes').'</div><br />';

		$countries = Country::getCountries($cookie->id_lang, true, true);
		echo '<label>'.$this->l('Country:').' </label>
				<div class="margin-form"><select id="id_country" onchange="populateStates($(this).val(), '.(int)($this->getFieldValue($obj, 'id_state')).');">';

		foreach ($countries AS $country)
			echo '<option value="'.(int)$country['id_country'].'" '.($cur_id_country == $country['id_country'] ? 'selected' : '').'>'.Tools::htmlentitiesUTF8($country['name']).'</option>';

		echo '</select></div>';


		echo '<label>'.$this->l('State:').' </label>
				<div class="margin-form">
				<select name="id_state" id="id_state">
				</select>
				</div>
				<script type="text/javascript">
					id_country = $("#id_country").val();
					populateStates(id_country,'.(int)$cur_id_state.');
				</script>';

		echo
			'<label>'.$this->l('Name:').' </label>
				<div class="margin-form">
					<input type="text" size="30" maxlength="64" name="name" value="'.htmlentities($this->getFieldValue($obj, 'name'), ENT_COMPAT, 'UTF-8').'" /> <sup>*</sup>
				</div>
				<label>'.$this->l('Status:').' </label>
				<div class="margin-form">
					<input type="radio" name="active" id="active_on" value="1" '.((!$obj->id OR $this->getFieldValue($obj, 'active')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="active_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="active" id="active_off" value="0" '.((!$this->getFieldValue($obj, 'active') AND $obj->id) ? 'checked="checked" ' : '').'/>
					<label class="t" for="active_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
					<p>'.$this->l('Enabled or disabled').'</p>
				</div>
				<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitAdd'.$this->table.'" class="button" />&nbsp;
					<input type="submit" value="'.$this->l('Save and stay').'" name="submitAdd'.$this->table.'AndStay" class="button" />
				</div>';

				echo '<div class="small"><sup>*</sup> '.$this->l('Required field').'</div>';

				if (isset($obj->id))
				{
					echo '
					<div class="margin-form">
					'.$this->_renderZipCodeForm().'<br />
					<div id="zipcodes-list">
					'.AdminCounty::renderZipCodeList($obj->getZipCodes()).'
					</div></div>';
				}

				echo '
			</fieldset>
		</form>';
	}


	protected function _renderZipCodeForm()
	{
		return '
		<div>
		Add Zip Codes:<br /> <input type="text" id="zipcodes" name="zipcodes" />
		<a href="#" class="button" onclick="addZipCode()">Add</a>
		<div id="error-msg" style="color: #FF0000"></div>
		</div>';
	}


	public static function renderZipCodeList($zip_codes)
	{
		$html = '';
		foreach ($zip_codes AS $zip_code)
		{
			$full_zip_code = $zip_code['from_zip_code'];
			if ($zip_code['to_zip_code'] != 0)
				$full_zip_code .= '-'.$zip_code['to_zip_code'];

			$html .= '<div>'.Tools::htmlentitiesUTF8($full_zip_code).' <a href="#" onclick="removeZipCodes(\''.Tools::htmlentitiesUTF8($full_zip_code).'\')"><img src="../img/admin/delete.gif" alt="" /></a></div>';
		}

		return $html;
	}
}

