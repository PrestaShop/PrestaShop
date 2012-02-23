<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 7048 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class BlockManufacturer extends Module
{
    public function __construct()
    {
        $this->name = 'blockmanufacturer';
        $this->tab = 'front_office_features';
        $this->version = 1.0;
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

        parent::__construct();

		$this->displayName = $this->l('Manufacturers block');
        $this->description = $this->l('Displays a block of manufacturers/brands');
    }

	public function install()
	{
		Configuration::updateValue('MANUFACTURER_DISPLAY_TEXT', true);
		Configuration::updateValue('MANUFACTURER_DISPLAY_TEXT_NB', 5);
		Configuration::updateValue('MANUFACTURER_DISPLAY_FORM', true);
        return parent::install() && $this->registerHook('leftColumn') && $this->registerHook('header');
    }

	public function hookLeftColumn($params)
	{
		$this->smarty->assign(array(
			'manufacturers' => Manufacturer::getManufacturers(),
			'text_list' => Configuration::get('MANUFACTURER_DISPLAY_TEXT'),
			'text_list_nb' => Configuration::get('MANUFACTURER_DISPLAY_TEXT_NB'),
			'form_list' => Configuration::get('MANUFACTURER_DISPLAY_FORM'),
			'display_link_manufacturer' => Configuration::get('PS_DISPLAY_SUPPLIERS'),
		));
		return $this->display(__FILE__, 'blockmanufacturer.tpl');
	}

	public function hookRightColumn($params)
	{
		return $this->hookLeftColumn($params);
	}

	public function getContent()
	{
		$output = '<h2>'.$this->displayName.'</h2>';
		if (Tools::isSubmit('submitBlockManufacturers'))
		{
			$text_list = (int)(Tools::getValue('text_list'));
			$text_nb = (int)(Tools::getValue('text_nb'));
			$form_list = (int)(Tools::getValue('form_list'));
			if ($text_list && !Validate::isUnsignedInt($text_nb))
				$errors[] = $this->l('Invalid number of elements');
			elseif (!$text_list && !$form_list)
				$errors[] = $this->l('Please activate at least one system list');
			else
			{
				Configuration::updateValue('MANUFACTURER_DISPLAY_TEXT', $text_list);
				Configuration::updateValue('MANUFACTURER_DISPLAY_TEXT_NB', $text_nb);
				Configuration::updateValue('MANUFACTURER_DISPLAY_FORM', $form_list);
			}
			if (isset($errors) && count($errors))
				$output .= $this->displayError(implode('<br />', $errors));
			else
				$output .= $this->displayConfirmation($this->l('Settings updated'));
		}
		return $output.$this->displayForm();
	}

	public function displayForm()
	{
		$output = '
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset><legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Settings').'</legend>
				<label>'.$this->l('Use a plain-text list').'</label>
				<div class="margin-form">
					<input type="radio" name="text_list" id="text_list_on" value="1" '.(Tools::getValue('text_list', Configuration::get('MANUFACTURER_DISPLAY_TEXT')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="text_list_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="text_list" id="text_list_off" value="0" '.(!Tools::getValue('text_list', Configuration::get('MANUFACTURER_DISPLAY_TEXT')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="text_list_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
					&nbsp;&nbsp;&nbsp;'.$this->l('Display').' <input type="text" size="2" name="text_nb" value="'.(int)(Tools::getValue('text_nb', Configuration::get('MANUFACTURER_DISPLAY_TEXT_NB'))).'" /> '.$this->l('elements').'
					<p class="clear">'.$this->l('To display manufacturers in a plain-text list').'</p>
				</div>
				<label>'.$this->l('Use a drop-down list').'</label>
				<div class="margin-form">
					<input type="radio" name="form_list" id="form_list_on" value="1" '.(Tools::getValue('form_list', Configuration::get('MANUFACTURER_DISPLAY_FORM')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="form_list_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="form_list" id="form_list_off" value="0" '.(!Tools::getValue('form_list', Configuration::get('MANUFACTURER_DISPLAY_FORM')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="form_list_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
					<p class="clear">'.$this->l('To display manufacturers in a drop-down list').'</p>
				</div>
				<center><input type="submit" name="submitBlockManufacturers" value="'.$this->l('Save').'" class="button" /></center>
			</fieldset>
		</form>';
		return $output;
	}

	public function hookHeader($params)
	{
		$this->context->controller->addCSS(($this->_path).'blockmanufacturer.css', 'all');
	}
}
