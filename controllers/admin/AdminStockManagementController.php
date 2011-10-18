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
class AdminStockManagementController extends AdminController
{
	public function __construct()
	{
		$this->context = Context::getContext();
		$this->table = 'product';
		$this->className = 'Product';
		$this->lang = true;

		$this->addRowAction('details');
		$this->addRowAction('addstock');
		$this->addRowAction('removestock');
		$this->addRowAction('transferstock');

		$this->fieldsDisplay = array(
			'reference' => array('title' => $this->l('Reference'), 'align' => 'center', 'width' => 100, 'widthColumn' => 150),
			'ean13' => array('title' => $this->l('EAN13'), 'align' => 'center', 'width' => 75, 'widthColumn' => 100),
			'name' => array('title' => $this->l('Name'), 'width' => 350, 'widthColumn' => 'auto', 'filter_key' => 'b!name'),
			'stock' => array('title' => $this->l('Total quantities in stock'), 'width' => 50, 'widthColumn' => 60),
		);

		$this->_select = 'a.id_product as id, COUNT(pa.id_product_attribute) as variations';

		$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (pa.id_product = a.id_product)';

		parent::__construct();
	}

	/**
	 * method call when ajax request is made with the details row action
	 */
	public function ajaxProcess()
	{
		// test if an id is submit
		if (Tools::isSubmit('id'))
		{
			// desactivate lang gestion
			$this->lang = false;

			// get lang id
			$lang_id = (int)$this->context->language->id;

			// Get product id
			$product_id = (int)Tools::getValue('id');

			// Load product attributes with sql override
			$this->table = 'product_attribute';

			$this->_select = 'a.id_product_attribute as id, a.id_product, a.reference, a.ean13,
				IFNULL(CONCAT(pl.name, \' : \', GROUP_CONCAT(agl.`name`, \' - \', al.name SEPARATOR \', \')),pl.name) as name';

			$this->_join = '
				INNER JOIN '._DB_PREFIX_.'product_lang pl ON (pl.id_product = a.id_product AND pl.id_lang = '.$lang_id.')
				LEFT JOIN '._DB_PREFIX_.'product_attribute_combination pac ON (pac.id_product_attribute = a.id_product_attribute)
				LEFT JOIN '._DB_PREFIX_.'attribute atr ON (atr.id_attribute = pac.id_attribute)
				LEFT JOIN '._DB_PREFIX_.'attribute_lang al ON (al.id_attribute = atr.id_attribute AND al.id_lang = '.$lang_id.')
				LEFT JOIN '._DB_PREFIX_.'attribute_group_lang agl ON (agl.id_attribute_group = atr.id_attribute_group AND agl.id_lang = '.$lang_id.')';

			$this->_where = 'AND a.id_product = '.$product_id;
			$this->_group = 'GROUP BY a.id_product_attribute';

			// override this attributes
			$this->identifier = 'id_product_attribute';
			$this->display = 'list';

			// get list and force no limit clause in the request
			$this->getList($this->context->language->id, null, null, 0, false);

			// Render list
			$helper = new HelperList();
			$helper->actions = $this->actions;
			$helper->list_skip_actions = $this->list_skip_actions;
			$helper->shopLinkType = '';
			$helper->identifier = $this->identifier;
			// Force render - no filter, form, js, sorting ...
			$helper->simple_header = true;
			$content = $helper->generateList($this->_list, $this->fieldsDisplay);

			echo Tools::jsonEncode(array('use_parent_structure' => false, 'data' => $content));
		}

		die;
	}

	/**
	 * getList override
	 */
	public function getList($id_lang, $orderBy = null, $orderWay = null, $start = 0, $limit = null, $id_lang_shop = false)
	{
		parent::getList($id_lang, $orderBy, $orderWay, $start, $limit, $id_lang_shop);

		if ($this->display == 'list')
		{
			// Check each row to see if there are combinations and get the correct action in consequence
			$nb_items = count($this->_list);

			for ($i = 0; $i < $nb_items; $i++)
			{
				$item = &$this->_list[$i];

				// if it's an ajax request we have to consider manipulating a product variation
				if ($this->ajax == '1')
				{
					// no details for this row
					$this->addRowActionSkipList('details', array($item['id']));

					// specify actions in function of stock
					$this->skipActionByStock($item, $i, true);
				}
				// If current product has variations
				else if ((int)$item['variations'] > 0)
				{
					// we have to desactivate stock actions on current row
					$this->addRowActionSkipList('addstock', array($item['id']));
					$this->addRowActionSkipList('removestock', array($item['id']));
					$this->addRowActionSkipList('transferstock', array($item['id']));
				}
				else
				{
					//there are no variations of current product, so we don't want to show details action
					$this->addRowActionSkipList('details', array($item['id']));

					// specify actions in function of stock
					$this->skipActionByStock($item, $i, false);
				}
			}
		}
	}

	/**
	 * Check stock for a given product or product attribute
	 * and manage action available in consequence
	 *
	 * @param array $item reference to the current item
	 * @param bool $is_product_attribute specify if it's a product or a product variation
	 */
	private function skipActionByStock(&$item, $is_product_variation = false)
	{
		$stock_manager = StockManagerFactory::getManager();

		//get stocks for this product
		if ($is_product_variation)
			$stock = $stock_manager->getProductPhysicalQuantities($item['id'], $item['id_product']);
		else
			$stock = $stock_manager->getProductPhysicalQuantities($item['id'], 0);

		//affects stock to the list for display
		$item['stock'] = $stock;

		if ($stock <= 0)
		{
			//there is no stock, we can only add stock
			$this->addRowActionSkipList('removestock', array($item['id']));
			$this->addRowActionSkipList('transferstock', array($item['id']));
		}
	}

	/**
	 * initContent override
	 */
	public function initContent()
	{
		if ($this->display != 'edit' && $this->display != 'add')
			$this->display = 'list';

		parent::initContent();
	}

	 /**
	 * Display addstock action link
	 */
	public function displayAddstockLink($token = null, $id)
	{
        if (!array_key_exists('AddStock', self::$cache_lang))
            self::$cache_lang['AddStock'] = $this->l('Add stock');

        $this->context->smarty->assign(array(
            'href' => self::$currentIndex.
            	'&'.$this->identifier.'='.$id.
            	'&addstock'.
            	'&token='.($token != null ? $token : $this->token),
            'action' => self::$cache_lang['AddStock'],
        ));

        return $this->context->smarty->fetch(_PS_ADMIN_DIR_.'/themes/template/list_action_addstock.tpl');
	}

    /**
	 * Display removestock action link
	 */
    public function displayRemovestockLink($token = null, $id)
    {
        if (!array_key_exists('RemoveStock', self::$cache_lang))
            self::$cache_lang['RemoveStock'] = $this->l('Remove stock');

        $this->context->smarty->assign(array(
            'href' => self::$currentIndex.
            	'&'.$this->identifier.'='.$id.
            	'&removestock'.
            	'&token='.($token != null ? $token : $this->token),
            'action' => self::$cache_lang['RemoveStock'],
        ));

        return $this->context->smarty->fetch(_PS_ADMIN_DIR_.'/themes/template/list_action_removestock.tpl');
    }

    /**
	 * Display transferstock action link
	 */
    public function displayTransferstockLink($token = null, $id)
    {
        if (!array_key_exists('TransferStock', self::$cache_lang))
            self::$cache_lang['TransferStock'] = $this->l('Transfer stock');

        $this->context->smarty->assign(array(
            'href' => self::$currentIndex.
            	'&'.$this->identifier.'='.$id.
            	'&transferstock'.
            	'&token='.($token != null ? $token : $this->token),
            'action' => self::$cache_lang['TransferStock'],
        ));

        return $this->context->smarty->fetch(_PS_ADMIN_DIR_.'/themes/template/list_action_transferstock.tpl');
    }
}