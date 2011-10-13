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
*  @version  Release: $Revision: 7307 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */
class AdminWarehouses extends AdminTab
{
	public function __construct()
	{
		$this->context = Context::getContext();
	 	$this->table = 'warehouse';
	 	$this->className = 'Warehouse';
	 	$this->edit = true;
		$this->delete = false;
		$this->view = false;

		$this->fieldsDisplay = array(
			'reference' => array('title' => $this->l('Reference'), 'width' => 40),
			'name' => array('title' => $this->l('Name'), 'width' => 300, 'havingFilter' => true),
			'management_type' => array('title' => $this->l('Managment type'), 'width' => 40),
			'employee' => array('title' => $this->l('Manager'), 'width' => 150, 'havingFilter' => true),
			'location' => array('title' => $this->l('Location'), 'width' => 150),
			'contact' => array('title' => $this->l('Phone Number'), 'width' => 50),
		);

		$this->_select = 'reference, name, management_type,
							CONCAT(e.lastname, \' \', e.firstname) AS employee,
							ad.phone AS contact,
							CONCAT(ad.city, \' \', c.iso_code) location';
		$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'employee` e ON (e.id_employee = a.id_employee)
						LEFT JOIN `'._DB_PREFIX_.'address` ad ON (ad.id_address = a.id_address)
						LEFT JOIN `'._DB_PREFIX_.'country` c ON (c.id_country = ad.id_country)';

		// Get countries list for warehouse localisation
		$countries = Country::getCountries($this->context->language->id);
		foreach ($countries as $country)
			$this->countries_array[$country['id_country']] = $country['name'];

		// Get employee list for warehouse manager
		$query = new DbQuery();
		$query->select('id_employee, CONCAT(lastname," ",firstname) as name');
		$query->from('employee');
		$query->where('active = 1');
		$employees = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
		foreach ($employees as $employee)
			$this->employees_array[$employee['id_employee']] = $employee['name'];

		parent::__construct();
	}

	public function postProcess()
	{
		// update/create address if not exists
		if (isset($_POST['submitAdd'.$this->table]))
		{
			if (isset($_POST['id_address']) && $_POST['id_address'] > 0)
				//update address
				$address = new Address((int)$_POST['id_address']);
			else
				//create address
				$address = new Address();

			$address->alias = $_POST['name'];
			$address->lastname = $_POST['name'];
			$address->firstname = $_POST['name'];
			$address->address1 = $_POST['address1'];
			$address->address2 = $_POST['address2'];
			$address->postcode = $_POST['postcode'];
			$address->phone = $_POST['phone'];
			$address->id_country = $_POST['id_country'];
			$address->city = $_POST['city'];

			if (isset($_POST['id_address']) && $_POST['id_address'] > 0)
			{
				//update address
				$address->update();
			}
			else {
				$address->save();
				$_POST['id_address'] = $address->id;
			}
		}

		return parent::postProcess();
	}

	public function displayForm($is_main_tab = true)
	{
		parent::displayForm();

		if (!($obj = $this->loadObject(true)))
			return;

		if ($obj->id_address > 0)
			$address = new Address($obj->id_address);

		echo '<form action="'.self::$currentIndex.'&submitAdd'.$this->table.'=1&token='.$this->token.'&addwarehouse" method="post">
		'.($obj->id ? '<input type="hidden" name="id_'.$this->table.'" value="'.$obj->id.'" />' : '').'
			<input type="hidden" name="id_address" value="'.(isset($obj->id_address) ? $obj->id_address : 0).'" />
			<fieldset><legend><img src="../img/admin/tab.gif" />'.$this->l('Warehouse').'</legend>
				<label>'.$this->l('Reference:').'</label>
				<div class="margin-form">
					<input size="20" type="text" name="reference" value="'.htmlentities($this->getFieldValue($obj, 'reference'), ENT_COMPAT, 'UTF-8').'" /><sup> *</sup>
				</div>

				<label>'.$this->l('Name:').'</label>
				<div class="margin-form">
					<input size="50" type="text" name="name" value="'.htmlentities($this->getFieldValue($obj, 'name'), ENT_COMPAT, 'UTF-8').'" /><sup> *</sup>
				</div>

				<label>'.$this->l('Phone Number').'</label>
				<div class="margin-form">
					<input type="text" size="33" name="phone" value="'.htmlentities(isset($address) ? $address->phone : '', ENT_COMPAT, 'UTF-8').'" /> <sup>*</sup>
				</div>

				<label>'.$this->l('Address').'</label>
				<div class="margin-form">
					<input type="text" size="33" name="address1" value="'.htmlentities(isset($address) ? $address->address1 : '', ENT_COMPAT, 'UTF-8').'" /> <sup>*</sup>
				</div>

				<label>'.$this->l('Address').' (2)</label>
				<div class="margin-form">
				      <input type="text" size="33" name="address2" value="'.htmlentities(isset($address) ? $address->address2 : '', ENT_COMPAT, 'UTF-8').'" />
				</div>

				<label>'.$this->l('Postcode/ Zip Code').'</label>
				<div class="margin-form">
					<input type="text" size="33" name="postcode" value="'.htmlentities(isset($address) ? $address->postcode : '', ENT_COMPAT, 'UTF-8').'" />
				</div>

				<label>'.$this->l('City').'</label>
				<div class="margin-form">
					<input type="text" size="33" name="city" value="'.htmlentities(isset($address) ? $address->city : '', ENT_COMPAT, 'UTF-8').'" style="text-transform: uppercase;" /> <sup>*</sup>
				</div>

				<label>'.$this->l('Country').'</label>
				<div class="margin-form">
					<select name="id_country" id="id_country" />';
				$selected_country = $this->getFieldValue($obj, 'id_country');
				foreach ($this->countries_array as $id_country => $name)
					echo '<option value="'.$id_country.'"'.((!$selected_country && Configuration::get('PS_COUNTRY_DEFAULT') == $id_country) ? ' selected="selected"' : ($selectedCountry == $id_country ? ' selected="selected"' : '')).'>'.$name.'</option>';
				echo '</select> <sup>*</sup>
				</div>

				<div id="contains_states" '.(!Country::containsStates((int)$selected_country) ? 'style="display:none;"' : '').'>
					<label>'.$this->l('State').'</label>
					<div class="margin-form">
						<select name="id_state" id="id_state">
						</select>
						<sup>*</sup>
					</div>
				</div>';

				$id_country_ajax = (int)$this->getFieldValue($obj, 'id_country');

				echo '
				<script type="text/javascript">
				$(document).ready(function(){
					ajaxStates ();
					$(\'#id_country\').change(function() {
						ajaxStates ();
						});
					function ajaxStates ()
					{
						$.ajax({
							url: "ajax.php",
							cache: false,
							data: "ajaxStates=1&id_country="+$(\'#id_country\').val()+"&id_state="+$(\'#id_state\').val(),
							success: function(html)
							{
								if (html == \'false\')
								{
									$("#contains_states").fadeOut();
									$(\'#id_state option[value=0]\').attr("selected", "selected");
								}
								else
								{
									$("#id_state").html(html);
									$("#contains_states").fadeIn();
									$(\'#id_state option[value='.(isset($address) ? $address->id_state : 0).']\').attr("selected", "selected");
								}
							}
						});
					};
				});
				</script>

				<label>'.$this->l('Manager').'</label>
				<div class="margin-form">
					<select name="id_employee" id="id_employee" />';
				$selected_employee = $this->getFieldValue($obj, 'id_employee');
				$selected_management_type = $this->getFieldValue($obj, 'management_type');

				foreach ($this->employees_array as $id_employee => $name)
					echo '<option value="'.$id_employee.'"'.(($selected_employee == $id_employee) ? ' selected="selected"' : '').'>'.$name.'</option>';
				echo '</select> <sup>*</sup>
				</div>

				<label>'.$this->l('Management type').'</label>
				<div class="margin-form">
					<select name="management_type" id="management_type">
						<option value="FIFO"'.(($selected_management_type == 'FIFO') ? ' selected="selected"' : '').'>'.$this->l('First In, First Out').'</option>
						<option value="LIFO"'.(($selected_management_type == 'LIFO') ? ' selected="selected"' : '').'>'.$this->l('Last In, First Out').'</option>
						<option value="WA"'.(($selected_management_type == 'WA') ? ' selected="selected"' : '').'>'.$this->l('Weight Average').'</option>
					</select>
					<sup>*</sup>
				</div>

				<div class="clear"></div><br />
				<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitAdd'.$this->table.'" class="button" />
				</div>

			</fieldset>
		</form>';
	}

}


