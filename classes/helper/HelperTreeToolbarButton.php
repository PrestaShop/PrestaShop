<?php
/*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

abstract class HelperTreeToolbarButtonCore
{
	const DEFAULT_TEMPLATE_DIRECTORY = 'helpers/tree';

	private   $_action;
	private   $_attributes;
	private   $_class;
	private   $_context;
	private   $_label;
	protected $_template;
	protected $_template_directory;

	public function __construct($label, $action = null, $class = null)
	{
		$this->setLabel($label);
		$this->setAction($action);
		$this->setClass($class);
	}

	public function __toString()
	{
		return $this->render();
	}

	public function setAction($value)
	{
		$this->_action = $value;
		return $this;
	}

	public function getAction()
	{
		return $this->_action;
	}

	public function setAttributes($value)
	{
		if (!is_array($value) && !$value instanceof Traversable)
			throw new PrestaShopException('Data value must be an traversable array');

		$this->_attributes = $value;
		return $this;
	}

	public function getAttributes()
	{
		if (!isset($this->_attributes))
			$this->_attributes = array();

		return $this->_attributes;
	}

	public function setClass($value)
	{
		$this->_class = $value;
		return $this;
	}

	public function getClass()
	{
		return $this->_class;
	}

	public function setContext($value)
	{
		$this->_context = $value;
		return $this;
	}

	public function getContext()
	{
		if (!isset($this->_context))
			$this->_context = Context::getContext();

		return $this->_context;
	}

	public function setLabel($value)
	{
		$this->_label = $value;
		return $this;
	}

	public function getLabel()
	{
		return $this->_label;
	}

	public function setTemplate($value)
	{
		$this->_template = $value;
		return $this;
	}

	public function getTemplate()
	{
		return $this->_template;
	}

	public function setTemplateDirectory($value)
	{
		$this->_template_directory = $this->_normalizeDirectory($value);
		return $this;
	}

	public function getTemplateDirectory()
	{
		if (!isset($this->_template_directory))
			$this->_template_directory = $this->_normalizeDirectory(
				$this->getContext()->smarty->getTemplateDir(0)
				.self::DEFAULT_TEMPLATE_DIRECTORY);

		return $this->_template_directory;
	}

	public function addAttribute($name, $value)
	{
		if (!isset($this->_attributes))
			$this->_attributes = array();

		$this->_attributes[$name] = $value;
		return $this;
	}

	public function render()
	{
		return $this->getContext()->smarty->createTemplate(
			$this->getTemplateDirectory().$this->getTemplate(),
			$this->getContext()->smarty
		)->assign($this->getAttributes());
	}

	private function _normalizeDirectory($directory)
	{
		$last = $directory[strlen($directory) - 1];
        
        if (in_array($last, array('/', '\\'))) {
            $directory[strlen($directory) - 1] = DIRECTORY_SEPARATOR;
            return $directory;
        }
        
        $directory .= DIRECTORY_SEPARATOR;
        return $directory;
	}
}