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
*  @version  Release: $Revision: 7300 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminTaxRulesGroup extends AdminTab
{
    public $tax_rule;
    public $selected_countries = array();
    public $selected_states = array();
    public $_errors_tax_rule;

	public function __construct()
	{
		global $cookie;
	 	$this->table = 'tax_rules_group';
	 	$this->className = 'TaxRulesGroup';
	 	$this->edit = true;
	 	$this->delete = true;
	 	$this->ajax = false;

		$this->fieldsDisplay = array(
		'id_tax_rules_group' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
		'name' => array('title' => $this->l('Name'), 'width' => 140),
		'active' => array('title' => $this->l('Enabled'), 'width' => 25, 'align' => 'center', 'active' => 'status', 'type' => 'bool', 'orderby' => false));

       parent::__construct();
	}


	/**
	* retrieve a tax rule via ajax
	* @return tax rule in json format
	*/
	public function ajaxProcessGetTaxRule()
	{
			$id_rule	= (int)Tools::getValue('id_tax_rule');

			if ($tax_rule = TaxRule::retrieveById($id_rule))
				die(Tools::jsonEncode($tax_rule));
			else
				die('error');
	}


	public function ajaxPreProcess() {}

	public function postProcess()
	{
      $action = Tools::getValue('action');
		if ($action == 'delete_rule')
		{
			$id_rule	= (int)Tools::getValue('id_tax_rule');
			$tax_rule = new TaxRule($id_rule);

			if (Validate::isLoadedObject($tax_rule))
			{
				$tax_rule->delete();
				Tools::redirectAdmin(self::$currentIndex.'&'.$this->identifier.'='.$tax_rule->id_tax_rules_group.'&conf=4&update'.$this->table.'&token='.$this->token);
			}
		}
		else if ($action == 'create_rule')
		{
			$zipcode = Tools::getValue('zipcode');
			$id_rule	= (int)Tools::getValue('id_tax_rule');

			$this->selected_countries = Tools::getValue('country');
			$this->selected_states = Tools::getValue('states');

			if (empty($this->selected_states) || sizeof($this->selected_states) == 0)
				$this->selected_states = array(0);

			foreach ($this->selected_countries as $id_country)
			{
				foreach ($this->selected_states as $id_state)
				{
					$tr = new TaxRule();

					// update or creation?
					if (isset($id_rule))
						$tr->id = $id_rule;

					$tr->id_tax = (int)Tools::getValue('tax');
					$tr->id_tax_rules_group = (int)Tools::getValue('id_tax_rules_group');
					$tr->id_country = (int)$id_country;
					$tr->id_state = (int)$id_state;
					list($tr->zipcode_from, $tr->zipcode_to) = $tr->breakDownZipCode($zipcode);
					$tr->behavior = (int)Tools::getValue('behavior');
					$tr->description = Tools::getValue('description');

					$this->tax_rule = $tr;
					$this->_errors_tax_rule = $this->validateTaxRule($tr);
					if (sizeof($this->_errors_tax_rule) == 0)
					{
						if (!$tr->save())
							die(Tools::displayError('An error has occured: Can\'t save the current tax rule'));
					} else
						Tools::redirectAdmin(self::$currentIndex.'&'.$this->identifier.'='.$tr->id_tax_rules_group.'&conf=4&update'.$this->table.'&token='.$this->token);
				}
			}

			if (sizeof($this->_errors_tax_rule) == 0)
				Tools::redirectAdmin(self::$currentIndex.'&'.$this->identifier.'='.$tr->id_tax_rules_group.'&conf=4&update'.$this->table.'&token='.$this->token);

		} else
         parent::postProcess();
	}

	/**
	* check if the tax rule could be added in the database
	* @param TaxRule $tr
	*/
    protected function validateTaxRule(TaxRule $tr)
    {
       // TODO: check if the rule already exists
       return $tr->validateController();
    }

	protected function displayJS()
	{
		global $cookie;

		$all = $this->l('All');
		$javascript = <<<EOT
		<script type="text/javascript">
		function populateStates(id_country, id_state)
		{
				if ($("#country option:selected").size() > 1)
				{
					$("#zipcode-label").hide();
					$("#zipcode").hide();

					$("#state-select").hide();
					$("#state-label").hide();
				} else {
					$.ajax({
						  url: "ajax.php",
						  cache: false,
						  data: "ajaxStates=1&id_country="+id_country+"&id_state="+id_state+"&empty_value=$all",
						  success: function(html){
								if (html == "false")
								{
									$("#state-label").hide();
									$("#state-select").hide();
								}
								else
								{
									$("#state-label").show();
									$("#state-select").show();
									$("#states").html(html);
								}
						  }
					});

					$("#zipcode-label").show();
					$("#zipcode").show();

				}
		}

		function loadTaxRule(id_tax_rule)
		{
			$.ajax({
				url: "ajax-tab.php",
				cache: false,
				dataType: "json",
				data: "action=get_tax_rule&id_tax_rule="+id_tax_rule+"&tab=AdminTaxRulesGroup&token=$this->token",
				success: function(data){
					$('#rule_form').show();
					$('#id_tax_rule').val(data.id_tax_rule);
					$('#country').val(data.id_country);
					$('#state').val(data.id_state);

                    zipcode = 0;
                    if (data.zipcode_from != 0)
                    {
                        zipcode = data.zipcode_from;

                        if (data.zipcode_to != 0)
                            zipcode = zipcode +"-"+data.zipcode_to
                    }

					$('#zipcode').val(zipcode);
					$('#behavior').val(data.behavior);
					$('#tax').val(data.id_tax);
					$('#description').val(data.description);

					populateStates(data.id_country, data.id_state);
				},
			  	error: function(data)
			   {

			   }
			});
		}

		function initForm()
		{
			$('#id_tax_rule').val('');
			$('#country').val(0);
			$('#state').val(0);
			$('#zipcode').val(0);
			$('#behavior').val(0);
			$('#tax').val(0);
			$('#description').val('');

			populateStates(0,0);
		}
		</script>
EOT;

		echo $javascript;
	}


	public function displayTaxRulesErrors()
	{
		if ($nbErrors = count($this->_errors_tax_rule) AND $this->_includeContainer)
		{

			echo '<script type="text/javascript">
				$(document).ready(function() {
					$(\'#hideErrorTaxRules\').unbind(\'click\').click(function(){
						$(\'.error\').hide(\'slow\', function (){
							$(\'.error\').remove();
						});
						return false;
					});
				});

                $(document).ready(function() {
    				$(\'#rule_form\').show();
                    populateStates("'.(int)$this->tax_rule->id_country.'", "'.(int)$this->tax_rule->id_state.'")
                });
			  </script>
			<div class="error"><span style="float:right"><a id="hideErrorTaxRules" href=""><img alt="X" src="../img/admin/close.png" /></a></span><img src="../img/admin/error2.png" />';
			if (count($this->_errors_tax_rule) == 1)
				echo $this->_errors_tax_rule[0];
			else
			{
				echo $nbErrors.' '.$this->l('errors').'<br /><ol>';
				foreach ($this->_errors_tax_rule as $error)
					echo '<li>'.$error.'</li>';
				echo '</ol>';
			}
			echo '</div>';
		}
	}


    public function display()
    {
		if ((Tools::getValue('submitAdd'.$this->table) AND sizeof($this->_errors_tax_rule)) OR isset($_GET['add'.$this->table]))
		{
			if ($this->tabAccess['add'] === '1')
				$this->displayForm();
			else
				echo $this->l('You do not have permission to add here');
		}
        else parent::display();
    }

	/**
	* displays the tax rules group form
	*/
    protected function displayRuleGroupForm()
    {
		global $cookie, $currentIndex;
		parent::displayForm();
		if (!($obj = $this->loadObject(true)))
			return;

        // if the user come from the product page
		$param_product = Tools::getValue('id_product') ? '&id_product='.Tools::getValue('id_product') : '';

        echo '<form action="'.$currentIndex.'&submitAdd'.$this->table.'=1&token='.$this->token.$param_product.'" method="post">
				'.($obj->id ? '<input type="hidden" name="id_'.$this->table.'" value="'.$obj->id.'" />' : '').'
				<fieldset><legend><img src="../img/admin/dollar.gif" />'.$this->l('Tax Rules').'</legend>
				<input type="hidden" name="tabs" id="tabs" value="0" />
				';

		echo '<label>'.$this->l('Name').'</label>
				<div class="margin-form">
					<input size="33" type="text" name="name" value="'.Tools::htmlentitiesUTF8($this->getFieldValue($obj, 'name')).'" /><sup> *</sup>
					<span class="hint" name="help_box">'.$this->l('Invalid characters:').' <>;=#{}<span class="hint-pointer">&nbsp;</span></span>
			<p class="clear"></p>
			</div>';

		echo '
			<label>'.$this->l('Enable:').' </label>
			<div class="margin-form">
				<input type="radio" name="active" id="active_on" value="1" '.($this->getFieldValue($obj, 'active') ? 'checked="checked" ' : '').'/>
				<label class="t" for="active_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
				<input type="radio" name="active" id="active_off" value="0" '.(!$this->getFieldValue($obj, 'active') ? 'checked="checked" ' : '').'/>
				<label class="t" for="active_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
			</div>
			<div class="margin-form">
				<input type="submit" value="'.$this->l('Save and stay').'" name="submitAdd'.$this->table.'AndStay" class="button" />
			</div>
			</form>';
    }

	/**
	* Display the tax rule form
	*
 	* @param int $id_rule_group
	*/
	protected function displayRuleForm($id_rule_group)
	{
		global $cookie, $currentIndex;

        if (Validate::isLoadedObject($this->tax_rule))
            die(Tools::displayError('Unable to load the tax rule!'));

		$country_select = Helper::selectInput(Country::getCountries((int)$cookie->id_lang),
 				 			  					 array('id' => 'country',
 				 			  					 		'name' => 'country[]',
 				 			  					 		'onclick' => 'populateStates($(this).val(), \'\')',
 				 			  					 		'multiple' => 'multiple',
												 		'size' => 15),
												 array('key' => 'id_country',
												 		'value' => 'name',
												 		'selected' => $this->selected_countries,
												 		'empty' => $this->l('All')));


		$tax_select = Helper::selectInput(Tax::getTaxes((int)$cookie->id_lang),
 				 			   				  array('id' => 'tax', 'name' => 'tax'),
				 								  array('key' => 'id_tax',
				 								  		  'value' => 'name',
				 								  		  'empty' => $this->l('No Tax')));

		$behavior_select = Helper::selectInput(array(0 => $this->l('This tax only'),
																	1 => $this->l('Combine'),
																	2 => $this->l('One After Another')
																	),
				 				 			   				  array('id' => 'behavior', 'name' => 'behavior'));

		echo '<a href="#" onclick="initForm();$(\'#rule_form\').slideToggle();return false;"><img src="../img/admin/add.gif" alt="" /> '.$this->l('Add a new tax rule').'</a>
				<form action="'.$currentIndex.'&submitAdd'.$this->table.'=1&token='.$this->token.'" method="post">
				<div id="rule_form" style="display: none">
					<div style="float: left">
					<label>'.$this->l('Country').'</label>
					<div class="margin-form">
						'.$country_select.'
					</div>
					</div>

					<div style="float: left">
					<label id="state-label">'.$this->l('State').'</label>
					<div id="state-select" class="margin-form">
						<select id="states" name="states[]" multiple="multiple">
						</select>
					</div>
					</div>

					<div style="clear: both"></div>
					<label id="zipcode-label">'.$this->l('ZipCode range').'</label>
					<div class="margin-form">
                  <input type="hidden" name="action" value="create_rule" />
                  <input type="hidden" id="id_tax_rules_group" name="id_tax_rules_group" value="'.(int)$id_rule_group.'" />
						<input type="hidden" id="id_tax_rule" name="id_tax_rule" />
						<input type="text" id="zipcode" name="zipcode" />&nbsp;
						<div class="hint" style="display: block">'.$this->l('You can define a range (eg: 75000-75015) or a simple zipcode').'</div>
					</div>

					<label>'.$this->l('Behavior').'</label>
					<div class="margin-form">
						'.$behavior_select.'
						<div class="hint" style="display: block">
							'.$this->l('Define the behavior if an address matches multiple rules:').'<br />
							<b>'.$this->l('This Tax Only:').'</b> '.$this->l('Will apply only this tax').'<br />
							<b>'.$this->l('Combine:').'</b> '.$this->l('Combine taxes (eg: 10% + 5% => 15%)').'<br />
							<b>'.$this->l('One After Another:').'</b> '.$this->l('Apply taxes one after another (eg: 100€ + 10% => 110€ + 5% => 115.5€)').
						'</div>
					</div>

					<label>'.$this->l('Tax').'</label>
					<div class="margin-form">
						'.$tax_select.' '.$this->l('(Total tax:').'9%'.')
					</div>

					<label>'.$this->l('Description').'</label>
					<div class="margin-form">
						<input type="text" id="description" name="description" />&nbsp;
					</div>

					<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="create_rule" class="button" />
					</div>
				</div>
				</form>
				<br /><br />
				';
	}

	/**
	* display the list of rules
	*
	* @param int $id_rule_group
	*/
	protected function displayRulesList($id_rule_group)
	{
		echo '<table class="table" cellspacing="0" cellpadding="0" style="text-align: center; width: 100%;">
					<thead>
						<tr>
							<th>'.$this->l('Country').'</th>
							<th>'.$this->l('State').'</th>
							<th>'.$this->l('ZipCodes').'</th>
							<th>'.$this->l('Behavior').'</th>
							<th>'.$this->l('Tax').'</th>
							<th>'.$this->l('Description').'</th>
							<th>'.$this->l('Actions').'</th>
						</tr>
					</thead>
					<tbody>
						'.$this->displayTaxRules((int)$id_rule_group).'
					</tbody>
				</table>';
	}


	/**
	* display the tax rules list table body
	*
	* @param int $id_rule_group
	*/
	protected function displayTaxRules($id_rule_group)
	{
		global $currentIndex, $cookie;

		$html = '';
		$tax_rules = TaxRule::getTaxRulesByGroupId((int)$cookie->id_lang, (int)$id_rule_group);
		if (count($tax_rules) == 0)
		{
			$html .= '<tr>
					  	<td colspan="7">'.$this->l('No rules defined').'</td>
					  </tr>';
		} else {
			foreach ($tax_rules as $tax_rule)
			{
				//  format fields for display
				$country_name = ($tax_rule['country_name'] == '' ? '*' : $tax_rule['country_name']);
				$state_name = ($tax_rule['state_name'] == '' ? '*' : $tax_rule['state_name']);
				$zipcodes = '*';
				if (isset($tax_rule['zipcode_from']) && $tax_rule['zipcode_from'] != 0)
				{
					$zipcodes = $tax_rule['zipcode_from'];
					if (isset($tax_rule['zipcode_to']) && $tax_rule['zipcode_to'] != 0 && $tax_rule['zipcode_to'] != $tax_rule['zipcode_from'])
						$zipcodes .= '-'.$tax_rule['zipcode_to'];
				}

				$tax = ((float)$tax_rule['rate'] == 0 ? '-' : (float)$tax_rule['rate'].'%');
				$behavior = $this->l('This tax only');
				if (TaxCalculator::COMBINE_METHOD == $tax_rule['behavior'])
					$behavior = $this->l('Compute with others');
				else if (TaxCalculator::ONE_AFTER_ANOTHER_METHOD == $tax_rule['behavior'])
					$behavior = $this->l('One after another');

				// render fields
				$html .= '<tr>
							<td>'.Tools::htmlentitiesUTF8($country_name).'</td>
							<td>'.Tools::htmlentitiesUTF8($state_name).'</td>
							<td>'.Tools::htmlentitiesUTF8($zipcodes).'</td>
							<td>'.Tools::htmlentitiesUTF8($behavior).'</td>
							<td>'.Tools::htmlentitiesUTF8($tax).'</td>
							<td>'.Tools::htmlentitiesUTF8($tax_rule['description']).'</td>
							<td>
								<a href="#" onclick="loadTaxRule(\''.$tax_rule['id_tax_rule'].'\'); return false;">
									<img src="../img/admin/edit.gif" border="0" alt="'.$this->l('Edit').'" title="'.$this->l('Edit').'" />
								</a>
								<a href="'.$currentIndex.'&id_tax_rule='.$tax_rule['id_tax_rule'].'&action=delete_rule&token='.$this->token.'" onclick="return confirm(\''.addslashes($this->l('Delete item ?')).'\');">
									<img src="../img/admin/delete.gif" border="0" alt="'.$this->l('Delete').'" title="'.$this->l('Delete').'" />
								</a>

							</td>
						</tr>';
			}
		}

		return $html;
	}

	/**
	* @param boolean $firstCall
	*/
	public function displayForm($firstCall = true)
	{
		if (!($obj = $this->loadObject(true)))
			return;

		parent::displayForm();
		$this->displayRuleGroupForm();
		$this->displayJS();

		// display tax rules only if the group has already been created
		if ($obj->id)
		{
			echo '<hr />';
			$this->displayTaxRulesErrors();
			$this->displayRuleForm($obj->id);
			$this->displayRulesList($obj->id);
		}
	}
}

