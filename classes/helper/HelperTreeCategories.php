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

class HelperTreeCategoriesCore extends TreeCore
{
	const DEFAULT_TEMPLATE             = 'tree_categories.tpl';
	const DEFAULT_NODE_FOLDER_TEMPLATE = 'tree_node_folder_radio.tpl';
	const DEFAULT_NODE_ITEM_TEMPLATE   = 'tree_node_item_radio.tpl';

	private $_disabled_categories;
	private $_lang;
	private $_root_category;
	private $_selected_categories;
	private $_shop;
	private $_use_checkbox;
	private $_use_search;

	public function __construct($id, $title = null, $root_category = null,
		$lang = null, $shop = null)
	{
		parent::__construct($id);

		if (isset($title))
			$this->setTitle($title);

		if (isset($root_category))
			$this->setRootCategory($root_category);

		$this->setLang($lang);
		$this->setShop($shop);
	}

	public function getData()
	{
		if (!isset($this->_data))
			$this->setData(Category::getNestedCategories(
				$this->getRootCategory(), $this->getLang()));

		return $this->_data;
	}

	public function setDisabledCategories($value)
	{
		$this->_disabled_categories = $value;
		return $this;
	}

	public function getDisabledCategories()
	{
		return $this->_disabled_categories;
	}

	public function setLang($value)
	{
		$this->_lang = $value;
		return $this;
	}

	public function getLang()
	{
		if (!isset($this->_lang))
			$this->setLang($this->getContext()->employee->id_lang);

		return $this->_lang;
	}

	public function getNodeFolderTemplate()
	{
		if (!isset($this->_node_folder_template))
			$this->setNodeFolderTemplate(self::DEFAULT_NODE_FOLDER_TEMPLATE);

		return $this->_node_folder_template;
	}

	public function getNodeItemTemplate()
	{
		if (!isset($this->_node_item_template))
			$this->setNodeItemTemplate(self::DEFAULT_NODE_ITEM_TEMPLATE);

		return $this->_node_item_template;
	}

	public function setRootCategory($value)
	{
		if (!Validate::isInt($value))
			throw new PrestaShopException('Root category must be an integer value');

		$this->_root_category = $value;
		return $this;
	}

	public function getRootCategory()
	{
		if (!isset($this->_root_category))
			$this->setRootCategory(Category::getRootCategory($this->getLang())
				->id);

		return $this->_root_category;
	}

	public function setSelectedCategories($value)
	{
		if (!is_array($value))
			throw new PrestaShopException('Selected categories value must be an array');

		$this->_selected_categories = $value;
		return $this;
	}

	public function getSelectedCatgories()
	{
		if (!isset($this->_selected_categories))
			$this->_selected_categories = array();

		return $this->_selected_categories;
	}

	public function setShop($value)
	{
		$this->_shop = $value;
		return $this;
	}

	public function getShop()
	{
		if (!isset($this->_shop))
		{
			if (Tools::isSubmit('id_shop'))
				$this->setShop(new Shop(Tools::getValue('id_shop')));
			else
				if ($this->getContext()->shop->id)
					$this->setShop(new Shop($this->getContext()->shop->id));
				else
					if (!Shop::isFeatureActive())
						$this->setShop(new Shop(Configuration::get('PS_SHOP_DEFAULT')));
					else
						$this->setShop(new Shop(0));
		}

		return $this->_shop;
	}

	public function getTemplate()
	{
		if (!isset($this->_template))
			$this->setTemplate(self::DEFAULT_TEMPLATE);

		return $this->_template;
	}

	public function setUseCheckBox($value)
	{
		$this->_use_checkbox = (bool)$value;
		return $this;
	}

	public function setUseSearch($value)
	{
		$this->_use_search = (bool)$value;
		return $this;
	}

	public function useCheckBox()
	{
		return (isset($this->_use_checkbox) && $this->_use_checkbox);
	}

	public function useSearch()
	{
		return (isset($this->_use_search) && $this->_use_search);
	}

	public function render($data = NULL)
	{
		if (!isset($data))
			$data = $this->getData();

		if (isset($this->_disabled_categories)
			&& !empty($this->_disabled_categories))
			$this->_disableCategories($data, $this->getDisabledCategories());

		$this->setActions(array(
			new TreeToolbarLink(
				'Collapse All',
				'#',
				'$(\'#'.$this->getId().'\').tree(\'collapseAll\')',
				'icon-collapse-alt'),
			new TreeToolbarLink(
				'Expand All',
				'#',
				'$(\'#'.$this->getId().'\').tree(\'expandAll\')',
				'icon-expand-alt')
		));

		if ($this->useCheckBox())
		{
			$this->addAction(new TreeToolbarLink(
				'Check All',
				'#',
				'checkAllAssociatedCategories($(\'#'.$this->getId().'\'));',
				'icon-check-sign')
			);
			$this->addAction(new TreeToolbarLink(
				'Uncheck All',
				'#',
				'uncheckAllAssociatedCategories($(\'#'.$this->getId().'\'));',
				'icon-check-empty')
			);

			$this->setNodeFolderTemplate('tree_node_folder_checkbox.tpl');
			$this->setNodeItemTemplate('tree_node_item_checkbox.tpl');
			$this->setAttribute('use_checkbox', $this->useCheckBox());
		}

		if ($this->useSearch())
		{
			$this->addAction(new TreeToolbarSearchCategories(
				'Find a category:',
				$this->getId().'-categories-search')
			);
			$this->setAttribute('use_search', $this->useSearch());
		}

		$this->setAttribute('selected_categories', $this->getSelectedCatgories());		
		return parent::render($data);
	}

	private function _disableCategories(&$categories, $disabled_categories = null)
	{
		foreach ($categories as &$category)
		{
			if (!isset($disabled_categories) || in_array($category['id_category'], $disabled_categories))
			{
				$category['disabled'] = true;
				if (array_key_exists('children', $category) && is_array($category['children']))
					self::_disableCategories($category['children']);
			}
			else if (array_key_exists('children', $category) && is_array($category['children']))
				self::_disableCategories($category['children'], $disabled_categories);
		}
	}
}