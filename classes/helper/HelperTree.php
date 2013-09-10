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

class HelperTreeCore
{
	const DEFAULT_TEMPLATE_DIRECTORY   = 'helpers/tree';
	const DEFAULT_TEMPLATE             = 'tree.tpl';
	const DEFAULT_HEADER_TEMPLATE      = 'tree_header.tpl';
	const DEFAULT_NODE_FOLDER_TEMPLATE = 'tree_node_folder.tpl';
	const DEFAULT_NODE_ITEM_TEMPLATE   = 'tree_node_item.tpl';

	private $_children_key;
	private $_context;
	private $_data;
	private $_headerTemplate;
	private $_id;
	private $_id_key;
	private $_name_key;
	private $_node_folder_template;
	private $_node_item_template;
	private $_template_directory;
	private $_title;

	public function __construct($id, $data = null)
	{
		$this->setId($id);

		if (isset($data))
			$this->setData($data);
	}

	public function __toString()
	{
		return $this->render();
	}

	public function setActions($value)
	{
		if (!isset($this->_toolbar))
			$this->setToolbar(new HelperTreeToolbarCore());

		$this->getToolbar()->setActions($value);
		return $this;
	}

	public function getActions()
	{
		if (!isset($this->_toolbar))
			$this->setToolbar(new HelperTreeToolbarCore());

		return $this->getToolbar()->getActions();
	}

	public function addAction($action)
	{
		if (!isset($this->_toolbar))
			$this->setToolbar(new HelperTreeToolbarCore());

		$this->getToolbar()->addAction($action);
		return $this;
	}

	public function setChildrenKey($value)
	{
		$this->_children_key = (string)$value;
		return $this;
	}

	public function getChildrenKey()
	{
		if (!isset($this->_children_key))
			$this->setChildrenKey('children');

		return $this->_children_key;
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

	public function setData($value)
	{
		if (!is_array($value) && !$value instanceof Traversable)
			throw new PrestaShopException('Data value must be an traversable array');

		$this->_data = $value;
		return $this;
	}

	public function getData()
	{
		if (!isset($this->_data))
			$this->_data = array();

		return $this->_data;
	}

	public function setHeaderTemplate($value)
	{
		$this->_headerTemplate = $value;
		return $this;
	}

	public function getHeaderTemplate()
	{
		if (!isset($this->_headerTemplate))
			$this->setHeaderTemplate(self::DEFAULT_HEADER_TEMPLATE);

		return $this->_headerTemplate;
	}

	public function setId($value)
	{
		$this->_id = $value;
		return $this;
	}

	public function getId()
	{
		return $this->_id;
	}

	public function setIdKey($value)
	{
		$this->_id_key = (string)$value;
		return $this;
	}

	public function getIdKey()
	{
		if (!isset($this->_id_key))
			$this->setIdKey('id');

		return $this->_id_key;
	}

	public function setNameKey($value)
	{
		$this->_name_key = (string)$value;
		return $this;
	}

	public function getNameKey()
	{
		if (!isset($this->_name_key))
			$this->setNameKey('name');

		return $this->_name_key;
	}

	public function setNodeFolderTemplate($value)
	{
		$this->_node_folder_template = $value;
		return $this;
	}

	public function getNodeFolderTemplate()
	{
		if (!isset($this->_node_folder_template))
			$this->setNodeFolderTemplate(self::DEFAULT_NODE_FOLDER_TEMPLATE);

		return $this->_node_folder_template;
	}

	public function setNodeItemTemplate($value)
	{
		$this->_node_item_template = $value;
		return $this;
	}

	public function getNodeItemTemplate()
	{
		if (!isset($this->_node_item_template))
			$this->setNodeItemTemplate(self::DEFAULT_NODE_ITEM_TEMPLATE);

		return $this->_node_item_template;
	}

	public function setTemplate($value)
	{
		$this->_template = $value;
		return $this;
	}

	public function getTemplate()
	{
		if (!isset($this->_template))
			$this->setTemplate(self::DEFAULT_TEMPLATE);

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
		{
			$this->_template_directory = $this->_normalizeDirectory(
				$this->getContext()->smarty->getTemplateDir(0)
				.self::DEFAULT_TEMPLATE_DIRECTORY);
		}

		return $this->_template_directory;
	}

	public function setTitle($value)
	{
		$this->_title = $value;
		return $this;
	}

	public function getTitle()
	{
		return $this->_title;
	}

	public function setToolbar($value)
	{
		if (!is_object($value))
			throw new PrestaShopException('Toolbar must be a class object');

		$reflection = new ReflectionClass($value);

		if (!$reflection->implementsInterface('HelperITreeToolbarCore'))
			throw new PrestaShopException('Toolbar class must implements ITreeToolbarCore interface');

		$this->_toolbar = $value;
		return $this;
	}

	public function getToolbar()
	{
		if (isset($this->_toolbar))
			$this->_toolbar->setData($this->getData());

		return $this->_toolbar;
	}

	public function useInput()
	{
		return isset($this->_input_type);
	}

	public function useToolbar()
	{
		return isset($this->_toolbar);
	}

	public function render($data = null)
	{
		//Adding tree.js
		$admin_webpath = str_ireplace(_PS_ROOT_DIR_, '', _PS_ADMIN_DIR_);
		$admin_webpath = preg_replace('/^'.preg_quote(DIRECTORY_SEPARATOR, '/').'/', '', $admin_webpath);
		$bo_theme = ((Validate::isLoadedObject($this->getContext()->employee)
			&& $this->getContext()->employee->bo_theme) ? $this->getContext()->employee->bo_theme : 'default');

		if (!file_exists(_PS_BO_ALL_THEMES_DIR_.$bo_theme.DIRECTORY_SEPARATOR
			.'template'))
			$bo_theme = 'default';

		if ($this->getContext()->controller->ajax)
			$html = '<script type="text/javascript" src="'.__PS_BASE_URI__.$admin_webpath
				.'/themes/'.$bo_theme.'/js/tree.js"></script>';
		else
			$this->getContext()->controller->addJs(__PS_BASE_URI__.$admin_webpath
				.'/themes/'.$bo_theme.'/js/tree.js');

		//Create Tree Template
		$template = $this->getContext()->smarty->createTemplate(
			$this->getTemplateDirectory().$this->getTemplate(),
			$this->getContext()->smarty
		);

		if (trim($this->getTitle()) != '' || $this->useToolbar())
		{
			//Create Tree Header Template
			$headerTemplate = $this->getContext()->smarty->createTemplate(
				$this->getTemplateDirectory().$this->getHeaderTemplate(),
				$this->getContext()->smarty
			);
			$headerTemplate->assign(array(
				'title'   => $this->getTitle(),
				'toolbar' => $this->useToolbar() ? $this->renderToolbar() : null
			));
			$template->assign('header', $headerTemplate->fetch());
		}
		
		//Assign Tree nodes
		$template->assign(array(
			'id'    => $this->getId(),
			'nodes' => $this->renderNodes($data)
		));

		return (isset($html)?$html:'').$template->fetch();
	}

	public function renderNodes($data = null)
	{
		if (!isset($data))
			$data = $this->getData();

		if (!is_array($data) && !$data instanceof Traversable)
			throw new PrestaShopException('Data value must be an traversable array');

		$html = '';

		foreach ($data as $item)
		{
			if (array_key_exists($this->getChildrenKey(), $item)
				&& !empty($item[$this->getChildrenKey()]))
				$html .= $this->getContext()->smarty->createTemplate(
					$this->getTemplateDirectory().$this->getNodeFolderTemplate(),
					$this->getContext()->smarty
				)->assign(array(
					'name'     => $item[$this->getNameKey()],
					'children' => $this->renderNodes($item[$this->getChildrenKey()]),
					'node'     => $item
				))->fetch();
			else
				$html .= $this->getContext()->smarty->createTemplate(
					$this->getTemplateDirectory().$this->getNodeItemTemplate(),
					$this->getContext()->smarty
				)->assign(array(
					'name' => $item[$this->getNameKey()],
					'node' => $item
				))->fetch();
		}

		return $html;
	}

	public function renderToolbar()
	{
		return $this->getToolbar()->render();
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