<?php
/*
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class Core_Business_Payment_PaymentOption
{
	private $cta_text;
	private $logo;
	private $action;
	private $method;
	private $inputs;
	private $form;
	private $module_name;

	public function getCtaText()
	{
		return $this->cta_text;
	}

	public function setCtaText($cta_text)
	{
		$this->cta_text = $cta_text;
		return $this;
	}

	public function getLogo()
	{
		return $this->logo;
	}

	public function setLogo($logo)
	{
		$this->logo = $logo;
		return $this;
	}

	public function getAction()
	{
		return $this->action;
	}

	public function setAction($action)
	{
		$this->action = $action;
		return $this;
	}

	public function getMethod()
	{
		return $this->method;
	}

	public function setMethod($method)
	{
		$this->method = $method;
		return $this;
	}

	public function getInputs()
	{
		return $this->inputs;
	}

	public function setInputs($inputs)
	{
		$this->inputs = $inputs;
		return $this;
	}

	public function getForm()
	{
		return $this->form;
	}

	public function setForm($form)
	{
		$this->form = $form;
		return $this;
	}

	public function getModuleName()
	{
		return $this->module_name;
	}

	public function setModuleName($module_name)
	{
		$this->module_name = $module_name;
		return $this;
	}

	/**
	 * Legacy options were specified this way:
	 * - either an array with a top level property 'cta_text'
	 * 	and then the other properties
	 * - or a numerically indexed array or arrays as described above
	 * Since this was a mess, this method is provided to convert them.
	 * It takes as input a legacy option (in either form) and always
	 * returns an array of instances of Core_Business_Payment_PaymentOption
	 */
	public static function convertLegacyOption(array $legacyOption)
	{
		if (!$legacyOption)
			return;

		if (array_key_exists('cta_text', $legacyOption)) {
			$legacyOption = array($legacyOption);
		}

		$newOptions = array();

		$defaults = array(
			'action' => null,
			'form' => null,
			'method' => null,
			'inputs' => array(),
			'logo' => null
		);

		foreach ($legacyOption as $option) {

			$option = array_merge($defaults, $option);

			$newOption = new Core_Business_Payment_PaymentOption();
			$newOption->setCtaText($option['cta_text'])
					  ->setAction($option['action'])
					  ->setForm($option['form'])
					  ->setInputs($option['inputs'])
					  ->setLogo($option['logo'])
					  ->setMethod($option['method']);

			$newOptions[] = $newOption;
		}

		return $newOptions;
	}
}
