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
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminDiscounts extends AdminTab
{
	
	public function __construct()
	{
		global $cookie;
	 	
		$this->table = 'discount';
	 	$this->className = 'Discount';
	 	$this->lang = true;
	 	$this->edit = true;
	 	$this->delete = true;
	 	$this->_select = 'dtl.`name` AS discount_type, 
		IF(a.id_discount_type = 1, CONCAT(a.value, " %"),
		IF(a.id_discount_type = 2, CONCAT(a.value, " ", c.sign),
		"--")) as strvalue';
	 	$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'currency` c ON (c.`id_currency` = a.`id_currency`)
						LEFT JOIN `'._DB_PREFIX_.'discount_type` dt ON (dt.`id_discount_type` = a.`id_discount_type`)
						LEFT JOIN `'._DB_PREFIX_.'discount_type_lang` dtl ON (dt.`id_discount_type` = dtl.`id_discount_type` AND dtl.`id_lang` = '.(int)($cookie->id_lang).')';
		
		$typesArray = array();
		$types = Discount::getDiscountTypes((int)($cookie->id_lang));
		foreach ($types AS $type)
			$typesArray[$type['id_discount_type']] = $type['name'];
			
		$this->fieldsDisplay = array(
		'id_discount' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
		'name' => array('title' => $this->l('Code'), 'width' => 85, 'prefix' => '<span class="discount_name">', 'suffix' => '</span>', 'filter_key' => 'a!name'),
		'description' => array('title' => $this->l('Description'), 'width' => 100, 'filter_key' => 'b!description'),
		'discount_type' => array('title' => $this->l('Type'), 'type' => 'select', 'select' => $typesArray, 'filter_key' => 'dt!id_discount_type'),
		'strvalue' => array('title' => $this->l('Value'), 'width' => 50, 'align' => 'right', 'filter_key' => 'a!value'),
		'quantity' => array('title' => $this->l('Qty'), 'width' => 40, 'align' => 'right'),
		'date_to' => array('title' => $this->l('To'), 'width' => 60, 'type' => 'date', 'align' => 'right'),
		'active' => array('title' => $this->l('Status'), 'align' => 'center', 'active' => 'status', 'type' => 'bool', 'orderby' => false));
	
		$this->optionTitle = $this->l('Discounts options');
		$this->_fieldsOptions = array(
		'PS_VOUCHERS' => array('title' => $this->l('Enable vouchers:'), 'desc' => $this->l('Allow the use of vouchers in shop'), 'cast' => 'intval', 'type' => 'bool'),
		);
		parent::__construct();
	}
	
	protected function copyFromPost(&$object, $table)
	{		
		parent::copyFromPost($object, $table);
	
		$object->cumulable = (!isset($_POST['cumulable']) ? false : true);
		$object->cumulable_reduction = (!isset($_POST['cumulable_reduction']) ? false : true);
	}

	public function postProcess()
	{
		global $currentIndex, $cookie;
		$token = Tools::getValue('token') ? Tools::getValue('token') : $this->token;

		if ($discountName = Tools::getValue('name') AND Validate::isDiscountName($discountName) AND Discount::discountExists($discountName, Tools::getValue('id_discount')))
			$this->_errors[] = Tools::displayError('A voucher of this name already exists. Please choose another name.');
	
		if (Tools::getValue('submitAdd'.$this->table))
		{
			if (Tools::getValue('id_discount_type') == 2 AND Tools::getValue('id_currency') == 0)
				$this->_errors[] = Tools::displayError('Please set a currency for this voucher.');
			if (!Validate::isBool_Id(Tools::getValue('id_target')))
				$this->_errors[] = Tools::displayError('Invalid customer or group ID field');
			else
			{
				$rules = explode('_', Tools::getValue('id_target'));
				/* In form, there is one field for two differents fields in object*/
				$_POST[($rules[0] ? 'id_group' : 'id_customer')] = $rules[1];
			}
			/* Checking fields validity */
			$this->validateRules();
			if (!sizeof($this->_errors))
			{
				$id = (int)(Tools::getValue($this->identifier));

				/* Object update */
				if (isset($id) AND !empty($id))
				{
					if ($this->tabAccess['edit'] === '1')
					{
						$object = new $this->className($id);
						if (Validate::isLoadedObject($object))
						{
							/* Specific to objects which must not be deleted */
							if ($this->deleted AND $this->beforeDelete($object))
							{
								$object->deleted = 1;
								$object->update();
								$objectNew = new $this->className();
								$this->copyFromPost($objectNew, $this->table);
								$result = $objectNew->add();
								if (Validate::isLoadedObject($objectNew))
									$this->afterDelete($objectNew, $object->id);
							}
							else
							{
								if (($categories = Tools::getValue('categoryBox')) === false OR (!empty($categories) AND !is_array($categories)))
									die(Tools::displayError());
								$this->copyFromPost($object, $this->table);
								$result = $object->update(true, false, $categories);
							}
							if (!$result)
								$this->_errors[] = Tools::displayError('An error occurred while updating object.').' <b>'.$this->table.'</b>';
							elseif ($this->postImage($object->id))
							{
								if ($back = Tools::getValue('back'))
									Tools::redirectAdmin(urldecode($back).'&conf=4');
								if (Tools::getValue('stay_here') == 'on' || Tools::getValue('stay_here') == 'true' || Tools::getValue('stay_here') == '1')
									Tools::redirectAdmin($currentIndex.'&'.$this->identifier.'='.$object->id.'&conf=4&updatescene&token='.$token);
								Tools::redirectAdmin($currentIndex.'&'.$this->identifier.'='.$object->id.'&conf=4&token='.$token);
								
							}
						}
						else
							$this->_errors[] = Tools::displayError('An error occurred while updating object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
					}
					else
						$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
				}

				/* Object creation */
				else
				{
					if ($this->tabAccess['add'] === '1')
					{
						$object = new $this->className();
						$this->copyFromPost($object, $this->table);
						$categories = Tools::getValue('categoryBox', null);
						if (!$object->add(true, false, $categories))
							$this->_errors[] = Tools::displayError('An error occurred while creating object.').' <b>'.$this->table.'</b>';
						elseif (($_POST[$this->identifier] = $object->id /* voluntary */) AND $this->postImage($object->id) AND $this->_redirect)
							Tools::redirectAdmin($currentIndex.'&'.$this->identifier.'='.$object->id.'&conf=3&token='.$token);
					}
					else
						$this->_errors[] = Tools::displayError('You do not have permission to add here.');
				}
			}
			$this->_errors = array_unique($this->_errors);
		}
		else
			return parent::postProcess();
	}

	public function displayForm($isMainTab = true)
	{
		global $currentIndex, $cookie;
		parent::displayForm();
		
		if (!($obj = $this->loadObject(true)))
			return;
		
		echo '
		<script type="text/javascript">
			function discountType()
			{
				if ($("#id_discount_type").val() == 0)
					$("#value-div").css("display", "none");
				else if ($("#id_discount_type").val() == 1)
				{
					$("#value-div").css("display", "block");
					$("#percent-span").css("display", "block");
					$("#id_currency").css("display", "none");
				}
				else if ($("#id_discount_type").val() == 2)
				{
					$("#value-div").css("display", "block");
					$("#percent-span").css("display", "none");
					$("#id_currency").css("display", "block");
					$(\'#behavior_not_exhausted\').show();
					
				}
				else if ($("#id_discount_type").val() == 3)
					$("#value-div").css("display", "none");
				if ($(\'#id_discount_type\').val() != 2)
					$(\'#behavior_not_exhausted\').hide();
					
			}
			$(document).ready(function(){
				$("#id_discount_type").change(function(){discountType();});
				discountType();
			});
		</script>
		<form action="'.$currentIndex.'&submitAdd'.$this->table.'=1&token='.$this->token.'" id="discount" name="discount" method="post" enctype="multipart/form-data">
		'.($obj->id ? '<input type="hidden" name="id_'.$this->table.'" value="'.$obj->id.'" />' : '').'
			<fieldset><legend><img src="../img/admin/coupon.gif" />'.$this->l('Vouchers').'</legend>
				<label>'.$this->l('Code:').' </label>
				<div class="margin-form">
					<input type="text" size="30" maxlength="32" name="name" value="'.htmlentities($this->getFieldValue($obj, 'name'), ENT_COMPAT, 'UTF-8').'" id="code" />
					<sup>*</sup>
					<img src="../img/admin/news-new.gif" onclick="gencode(8);" style="cursor: pointer" />
					<span class="hint" name="help_box">'.$this->l('Invalid characters: numbers and').' !<>,;?=+()@#"�{}_$%:<span class="hint-pointer">&nbsp;</span></span>
					<p class="clear">'.$this->l('The voucher\'s code, at least 3 characters long, which the customer types in during check-out').'</p>
				</div>
				<label>'.$this->l('Type:').' </label>
				<div class="margin-form">
					<select name="id_discount_type" id="id_discount_type" onchange="free_shipping()">
						<option value="0">'.$this->l('-- Choose --').'</option>';
		$discountTypes = Discount::getDiscountTypes((int)($cookie->id_lang));
		foreach ($discountTypes AS $discountType)
			echo '<option value="'.(int)($discountType['id_discount_type']).'"'.
			(($this->getFieldValue($obj, 'id_discount_type') == $discountType['id_discount_type']) ? ' selected="selected"' : '').'>'.$discountType['name'].'</option>';
		echo '		</select> <sup>*</sup>
				</div>
				<div id="value-div" style="display:none">
					<label>'.$this->l('Value').'</label>
					<div class="margin-form">
						<input style="float:left;width:80px" type="text" name="value" id="discount_value" value="'.(float)($this->getFieldValue($obj, 'value')).'" onKeyUp="javascript:this.value = this.value.replace(/,/g, \'.\'); " />
						<select id="id_currency" name="id_currency" style="float:left;margin-left:10px;width:50px;display:none">
							<option value="0">--</option>';
		foreach (Currency::getCurrencies() as $row)
			echo '			<option value="'.(int)$row['id_currency'].'" '.(($this->getFieldValue($obj, 'id_currency') == $row['id_currency']) ? 'selected="selected"' : '').'>'.$row['sign'].'</option>';
		echo '			</select>
						<span id="percent-span" style="margin-left:10px;display:none;float:left;font-size:12px;font-weight:bold;color:black"> %</span>
						<sup style="float:left;margin-left:5px">*</sup>
						<p class="clear">'.$this->l('Either the monetary amount or the %, depending on Type selected above').'</p>
					</div>
				<div id="behavior_not_exhausted" style="display:none;">
					<label>'.$this->l('Behavior not exhausted:').'</label>
					<div class="margin-form">
						<select name="behavior_not_exhausted">
							<option value="1" '.($obj->behavior_not_exhausted === 1 ? 'selected="selected"' : '').'>'.$this->l('Reduce the voucher to the total order amount').'</option>
							<option value="2" '.($obj->behavior_not_exhausted == 2 ? 'selected="selected"' : '').'>'.$this->l('Create a new voucher with remaining amount').'</option>
							<option value="3" '.($obj->behavior_not_exhausted == 3 ? 'selected="selected"' : '').'>'.$this->l('Create negative invoice').'</option>
						</select>
					</div>
				</div>
				</div>
				<label>'.$this->l('Description:').' </label>
				<div class="margin-form">';
		foreach ($this->_languages as $language)
			echo '	<div id="description_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->_defaultFormLanguage ? 'block' : 'none').'; float: left;">
						<input size="33" type="text" name="description_'.$language['id_lang'].'" value="'.htmlentities($this->getFieldValue($obj, 'description', (int)($language['id_lang'])), ENT_COMPAT, 'UTF-8').'" /><sup> *</sup>
						<span class="hint" name="help_box">'.$this->l('Invalid characters:').' <>;=#{}<span class="hint-pointer">&nbsp;</span></span>
						<p class="clear">'.$this->l('Will appear in cart next to voucher code').'</p>
					</div>';							
		$this->displayFlags($this->_languages, $this->_defaultFormLanguage, 'description', 'description');
		echo '	</div>
				<div class="clear" / >
				<label>'.$this->l('Categories:').' </label>
					<div class="margin-form">
							<table cellspacing="0" cellpadding="0" class="table" style="width: 600px;">
									<tr>
										<th><input type="checkbox" name="checkme" class="noborder" onclick="checkDelBoxes(this.form, \'categoryBox[]\', this.checked)" /></th>
										<th>'.$this->l('ID').'</th>
										<th>'.$this->l('Name').'</th>
									</tr>';
		$done = array();
		$index = array();
		$indexedCategories =  isset($_POST['categoryBox']) ? $_POST['categoryBox'] : ($obj->id ? Discount::getCategories($obj->id) : array());
		$categories = Category::getCategories((int)($cookie->id_lang), false);
		foreach ($indexedCategories AS $k => $row)
			$index[] = $row['id_category'];
		$this->recurseCategoryForInclude((int)(Tools::getValue($this->identifier)), $index, $categories, $categories[0][1], 1, $obj->id);
		echo '
							</table>
							<p style="padding:0px; margin:0px 0px 10px 0px;">'.$this->l('Mark all checkbox(es) of categories to which the discount is to be applied').'<sup> *</sup></p>
						</div>
				<div class="clear" / >
				<label>'.$this->l('Total quantity:').' </label>
				<div class="margin-form">
					<input type="text" size="15" name="quantity" value="'.(int)($this->getFieldValue($obj, 'quantity')).'" /> <sup>*</sup>
					<p class="clear">'.$this->l('Total quantity available (mainly for vouchers open to everyone)').'</p>
				</div>
				<label>'.$this->l('Qty per each user:').' </label>
				<div class="margin-form">
					<input type="text" size="15" name="quantity_per_user" value="'.(int)($this->getFieldValue($obj, 'quantity_per_user')).'" /> <sup>*</sup>
					<p class="clear">'.$this->l('Number of times a single customer can use this voucher').'</p>
				</div>
				<label>'.$this->l('Minimum amount').'</label>
				<div class="margin-form">
					<input type="text" size="15" name="minimal" value="'.($this->getFieldValue($obj, 'minimal') ? (float)($this->getFieldValue($obj, 'minimal')) : '0').'" onkeyup="javascript:this.value = this.value.replace(/,/g, \'.\'); " /> <sup>*</sup>
					<p class="clear">'.$this->l('0 if not applicable').'</p>
				</div>
				<div class="margin-form">
					<p>
						<input type="checkbox" name="cumulable"'.(($this->getFieldValue($obj, 'cumulable') == 1) ? ' checked="checked"' : '').' id="cumulable_on" value="1" />
						<label class="t" for="cumulable_on"> '.$this->l('Cumulative with other vouchers').'</label>
					</p>
				</div>
				<div class="margin-form">
					<p>
						<input type="checkbox" name="cumulable_reduction"'.(($this->getFieldValue($obj, 'cumulable_reduction') == 1) ? ' checked="checked"' : '').' id="cumulable_reduction_on" value="1" />
						<label class="t" for="cumulable_reduction_on"> '.$this->l('Cumulative with price reductions').'</label>
					</p>
				</div>
				<label>'.$this->l('To be used by:').' </label>
								<div class="margin-form">
					<input type="hidden" name="id_customer" value="0">
					<input type="hidden" name="id_group" value="0">
					<select name="id_target" id="id_target">
					<option value="0_0">-- '.$this->l('All customers').' --</option>
						<optgroup label="'.$this->l('Groups').'" id="id_target_group">
						</optgroup>
						<optgroup label="'.$this->l('Customers').'" id="id_target_customers">
						</optgroup>
					</select><br />'.$this->l('Filter:').' <input type="text" size="25" name="filter" id="filter" onkeyup="fillCustomersAjax();" class="space" value="" />
					<script type="text/javascript">
						var formDiscount = document.layers ? document.forms.discount : document.discount;	
						function fillCustomersAjax()
						{
							var filterValue = \''.(($value = (int)($this->getFieldValue($obj, 'id_customer'))) ? '0_'.$value : (($value = (int)($this->getFieldValue($obj, 'id_group'))) ? '1_'.$value : '')).'\';
							if ($(\'#filter\').val())
								filterValue = $(\'#filter\').val();
							
							$.getJSON("'.dirname($currentIndex).'/ajax.php",{ajaxDiscountCustomers:1,filter:filterValue},
								function(obj) {
									var groups_length = obj.groups.length;
									if (obj.groups.length == 0)
										groups_length = 1;
									var customers_length = obj.customers.length;
									if (obj.customers.length == 0)
										customers_length = 1;
									formDiscount.id_target.length = 1 + customers_length + groups_length;
									
									if (obj.groups.length == 0)
									{
										formDiscount.id_target.options[1].value = -1;
										formDiscount.id_target.options[1].text = \''.$this->l('No match found').'\';
										formDiscount.id_target.options[1].className = "groups_filtered";
									}
									else
									{
										for (i = 0; i < obj.groups.length && i < 50; i++)
										{
											formDiscount.id_target.options[i+1].value = obj.groups[i]["value"];
											formDiscount.id_target.options[i+1].text = obj.groups[i]["text"];
											formDiscount.id_target.options[i+1].className = "groups_filtered";
										}
										if (obj.groups.length >= 50)
										{
											formDiscount.id_target.options[50].text = "'.$this->l('Too many results...',__CLASS__ , true, false).'";
											formDiscount.id_target.options[50].value = "_";
											formDiscount.id_target.options[50].className = "groups_filtered";
										}
									}
									
									if (obj.customers.length == 0)
									{
										formDiscount.id_target.options[groups_length+1].value = -1;
										formDiscount.id_target.options[groups_length+1].text = \''.$this->l('No match found').'\';
										formDiscount.id_target.options[groups_length+1].className = "customers_filtered";
									}										
									else
									{
										for (i = 0; i < obj.customers.length && i < 50; i++)
										{
											formDiscount.id_target.options[groups_length+1+i].value = obj.customers[i]["value"];
											formDiscount.id_target.options[groups_length+1+i].text = obj.customers[i]["text"];
											formDiscount.id_target.options[groups_length+1+i].className = "customers_filtered";
										}
										if (obj.customers.length >= 50)
										{
											formDiscount.id_target.options[groups_length+50+i].text = "'.$this->l('Too many results...',__CLASS__ , true, false).'";
											formDiscount.id_target.options[groups_length+50+i].value = "_";
											formDiscount.id_target.options[groups_length+50+i].className = "customers_filtered";
										}
									}
									$(".groups_filtered").appendTo($("#id_target_group"));
									$(".customers_filtered").appendTo($("#id_target_customers"));
									if ($(\'#filter\').val())
									{
										if (formDiscount.id_target.options[1].value != -1)
											formDiscount.id_target.options.selectedIndex = 1;
										else
											formDiscount.id_target.options.selectedIndex = 2;
									}
									else if(filterValue)
										for (i = 0; i < (customers_length + groups_length); i++)
											if (formDiscount.id_target.options[i+1].value == filterValue)
												formDiscount.id_target.options.selectedIndex = i + 1;
								}
							);
						}
						fillCustomersAjax(); 
					</script>
				</div><br />';
		includeDatepicker(array('date_from', 'date_to'), true);
		echo '		
				<label>'.$this->l('From:').' </label>
				<div class="margin-form">
					<input type="text" size="20" id="date_from" name="date_from" value="'.($this->getFieldValue($obj, 'date_from') ? htmlentities($this->getFieldValue($obj, 'date_from'), ENT_COMPAT, 'UTF-8') : date('Y-m-d H:i:s')).'" /> <sup>*</sup>
					<p class="clear">'.$this->l('Start date/time from which voucher can be used').'<br />'.$this->l('Format: YYYY-MM-DD HH:MM:SS').'</p>
				</div>
				<label>'.$this->l('To:').' </label>
				<div class="margin-form">
					<input type="text" size="20" id="date_to" name="date_to" value="'.($this->getFieldValue($obj, 'date_to') ? htmlentities($this->getFieldValue($obj, 'date_to'), ENT_COMPAT, 'UTF-8') : (date('Y') + 1).date('-m-d H:i:s')).'" /> <sup>*</sup>
					<p class="clear">'.$this->l('End date/time at which voucher is no longer valid').'<br />'.$this->l('Format: YYYY-MM-DD HH:MM:SS').'</p>
				</div>
				<label>'.$this->l('Display the voucher in the cart summary:').' </label>
				<div class="margin-form">
					<input type="radio" name="cart_display" id="cart_active_on" value="1" '.($this->getFieldValue($obj, 'cart_display') ? 'checked="checked" ' : '').'/>
					<label class="t" for="cart_display_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="cart_display" id="cart_active_off" value="0" '.(!$this->getFieldValue($obj, 'cart_display') ? 'checked="checked" ' : '').'/>
					<label class="t" for="cart_display_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
				</div>
				<div class="clear" / >
				<label>'.$this->l('Status:').' </label>
				<div class="margin-form">
					<input type="radio" name="active" id="active_on" value="1" '.($this->getFieldValue($obj, 'active') ? 'checked="checked" ' : '').'/>
					<label class="t" for="active_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="active" id="active_off" value="0" '.(!$this->getFieldValue($obj, 'active') ? 'checked="checked" ' : '').'/>
					<label class="t" for="active_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
					<p>'.$this->l('Enable or disable voucher').'</p>
				</div>
				<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitAdd'.$this->table.'" class="button" />
				</div>
				<div class="small"><sup>*</sup> '.$this->l('Required field').'</div>
			</fieldset>
		</form>';
	}
		/**
	 * Build a categories tree
	 *
	 * @param array $indexedCategories Array with categories where product is indexed (in order to check checkbox)
	 * @param array $categories Categories to list
	 * @param array $current Current category
	 * @param integer $id_category Current category id
	 */
	public static function recurseCategoryForInclude($id_obj, $indexedCategories, $categories, $current, $id_category = 1, $id_category_default = NULL, $has_suite = array())
	{
		global $done;
		static $irow;

		if (!isset($done[$current['infos']['id_parent']]))
			$done[$current['infos']['id_parent']] = 0;
		$done[$current['infos']['id_parent']] += 1;

		$todo = sizeof($categories[$current['infos']['id_parent']]);
		$doneC = $done[$current['infos']['id_parent']];

		$level = $current['infos']['level_depth'] + 1;

		echo '
		<tr class="'.($irow++ % 2 ? 'alt_row' : '').'">
			<td>
				<input type="checkbox" name="categoryBox[]" class="categoryBox'.($id_category_default == $id_category ? ' id_category_default' : '').'" id="categoryBox_'.$id_category.'" value="'.$id_category.'"'.((in_array($id_category, $indexedCategories) OR ((int)(Tools::getValue('id_category')) == $id_category AND !(int)($id_obj)) OR Tools::getIsset('adddiscount')) ? ' checked="checked"' : '').' />
			</td>
			<td>
				'.$id_category.'
			</td>
			<td>';
			for ($i = 2; $i < $level; $i++)
				echo '<img src="../img/admin/lvl_'.$has_suite[$i - 2].'.gif" alt="" style="vertical-align: middle;"/>';
			echo '<img src="../img/admin/'.($level == 1 ? 'lv1.gif' : 'lv2_'.($todo == $doneC ? 'f' : 'b').'.gif').'" alt="" style="vertical-align: middle;"/> &nbsp;
			<label for="categoryBox_'.$id_category.'" class="t">'.stripslashes($current['infos']['name']).'</label></td>
		</tr>';

		if ($level > 1)
			$has_suite[] = ($todo == $doneC ? 0 : 1);
		if (isset($categories[$id_category]))
			foreach ($categories[$id_category] AS $key => $row)
				if ($key != 'infos')
					self::recurseCategoryForInclude($id_obj, $indexedCategories, $categories, $categories[$id_category][$key], $key, $id_category_default, $has_suite);
	}
}


