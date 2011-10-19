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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * Use this helper to generate preferences forms, with values stored in the configuration table
 */
class HelperOptionsCore extends Helper
{
	public $first_call = true;
	public $required = false;

	/**
	 * @var array of forms fields
	 * Usage :
	 *
	 */
	protected $fields_form = array();

	public $fields_value = array();

	public $tpl = 'options.tpl';

	/**
	 * Generate a form for options
	 * @param array options
	 * @return string html
	 */
	public function generateOptions($option_list)
	{
		$tab = Tab::getTab($this->context->language->id, $this->id);
		foreach ($option_list as $category => $category_data)
		{
			if (!isset($category_data['image']))
				$category_data['image'] = (!empty($tab['module']) && file_exists($_SERVER['DOCUMENT_ROOT']._MODULE_DIR_.$tab['module'].'/'.$tab['class_name'].'.gif') ? _MODULE_DIR_.$tab['module'].'/' : '../img/t/').$tab['class_name'].'.gif';

			if (!isset($category_data['fields']))
				$category_data['fields'] = array();

			foreach ($category_data['fields'] as $key => $field)
			{
				// Set field value
				$field['value'] = $this->getOptionValue($key, $field);

				// Check if var is invisible (can't edit it in current shop context), or disable (use default value for multishop)
				$isDisabled = $isInvisible = false;
				if (Shop::isFeatureActive())
				{
					if (isset($field['visibility']) && $field['visibility'] > $this->context->shop->getContextType())
					{
						$isDisabled = true;
						$isInvisible = true;
					}
					else if (Context::shop() != Shop::CONTEXT_ALL && !Configuration::isOverridenByCurrentContext($key))
						$isDisabled = true;
				}
				$field['is_disabled'] = $isDisabled;
				$field['is_invisible'] = $isInvisible;

				$field['required'] = isset($field['required']) ? $field['required'] : $this->required;

				// Cast options values if specified
				if ($field['type'] == 'select' && isset($field['cast']))
					foreach ($field['list'] as $option_key => $option)
						$field['list'][$option_key][$field['identifier']] = $field['cast']($option[$field['identifier']]);

				// Fill values for all languages for all lang fields
				if (substr($field['type'], -4) == 'Lang')
				{
					if (!isset($languages))
						$languages = Language::getLanguages(false);

					foreach ($languages as $language)
					{
						if ($field['type'] == 'textLang')
							$value = Tools::safeOutput(Tools::getValue($key.'_'.$language['id_lang'], Configuration::get($key, $language['id_lang'])));
						elseif ($field['type'] == 'textareaLang')
							$value = Configuration::get($key, $language['id_lang']);
						$field['languages'][$language['id_lang']] = $value;
					}
					$field['flags'] = $this->displayFlags($languages, $this->context->language->id, $key, $key, true);
				}

				// Multishop default value
				$field['multishop_default'] = (Shop::isFeatureActive() && Context::shop() != Shop::CONTEXT_ALL && !$isInvisible);

				// Assign the modifications back to parent array
				$category_data['fields'][$key] = $field;

				// Is at least one required field present?
				if (isset($field['required']) && $field['required'])
					$this->required = true;
			}
			// Assign the modifications back to parent array
			$option_list[$category] = $category_data;
		}

		$this->context->smarty->assign(array(
			'current' => $this->currentIndex,
			'table' => $this->table,
			'token' => $this->token,
			'option_list' => $option_list,
			'current_id_lang' => $this->context->language->id,
			'required_fields' => isset($required_fields) ? $required_fields : false,
		));
		return $this->context->smarty->fetch(_PS_ADMIN_DIR_.'/themes/template/'.$this->tpl);
	}

	/**
	 * Type = image
	 * @ TODO
	 */
	public function displayOptionTypeImage($key, $field, $value)
	{
		echo '<table cellspacing="0" cellpadding="0">';
		echo '<tr>';

		$i = 0;
		foreach ($field['list'] as $theme)
		{
			echo '<td class="center" style="width: 180px; padding:0px 20px 20px 0px;">';
				echo '<input type="radio" name="'.$key.'" id="'.$key.'_'.$theme['name'].'_on" style="vertical-align: text-bottom;" value="'.$theme['name'].'"'.(_THEME_NAME_ == $theme['name'] ? 'checked="checked"' : '').' />';
				echo '<label class="t" for="'.$key.'_'.$theme['name'].'_on"> '.Tools::strtolower($theme['name']).'</label>';
				echo '<br />';
				echo '<label class="t" for="'.$key.'_'.$theme['name'].'_on">';
					echo '<img src="../themes/'.$theme['name'].'/preview.jpg" alt="'.Tools::strtolower($theme['name']).'">';
				echo '</label>';
			echo '</td>';
			if (isset($field['max']) && ($i +1 ) % $field['max'] == 0)
				echo '</tr><tr>';
			$i++;
		}
		echo '</tr>';
		echo '</table>';
	}


	/**
	 * Type = selectLang
	 * @ TODO
	 */
	public function displayOptionTypeSelectLang($key, $field, $value)
	{
		$languages = Language::getLanguages(false);
		foreach ($languages as $language)
		{
			echo '<div id="'.$key.'_'.$language['id_lang'].'" style="margin-bottom:8px; display: '.($language['id_lang'] == $this->context->language->id ? 'block' : 'none').'; float: left; vertical-align: top;">';
			echo  '<select name="'.$key.'_'.strtoupper($language['iso_code']).'">';
			foreach ($field['list'] as $k => $v)
				echo  '<option value="'.(isset($v['cast']) ? $v['cast']($v[$field['identifier']]) : $v[$field['identifier']]).'"'.((htmlentities(Tools::getValue($key.'_'.strtoupper($language['iso_code']), (Configuration::get($key.'_'.strtoupper($language['iso_code'])) ? Configuration::get($key.'_'.strtoupper($language['iso_code'])) : '')), ENT_COMPAT, 'UTF-8') == $v[$field['identifier']]) ? ' selected="selected"' : '').'>'.$v['name'].'</option>';
			echo  '</select>';
			echo  '</div>';
		}
		$this->displayFlags($languages, $this->context->language->id, $key, $key);
	}

	/**
	 * Type = price
	 * @ TODO
	 */
	public function displayOptionTypePrice($key, $field, $value)
	{
		echo $this->context->currency->getSign('left');
		$this->displayOptionTypeText($key, $field, $value);
		echo $this->context->currency->getSign('right').' '.$this->l('(tax excl.)');
	}

	/**
	 * Type = disabled
	 *
	 * @ TODO
	 */
	public function displayOptionTypeDisabled($key, $field, $value)
	{
		echo $field['disabled'];
	}

	public function getOptionValue($key, $field)
	{
		$value = Tools::getValue($key, Configuration::get($key));
		if (!Validate::isCleanHtml($value))
			$value = Configuration::get($key);

		if (isset($field['defaultValue']) && !$value)
			$value = $field['defaultValue'];
		return $value;
	}

	public function getFieldsRequired()
	{
		if (isset($this->fields_form['input']))
			foreach ($this->fields_form['input'] as $input)
				if (array_key_exists('required', $input) && $input['required'])
					return true;

		return false;
	}
}