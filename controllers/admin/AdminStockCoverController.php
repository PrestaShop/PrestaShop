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
 * @since 1.5.0
 */
class AdminStockCoverController extends AdminController
{
	public function __construct()
	{
		$this->context = Context::getContext();
		$this->table = 'product';
		$this->className = 'Product';
		$this->lang = true;

		$this->addRowAction('details');

		$this->fieldsDisplay = array(
			'reference' => array('title' => $this->l('Reference'), 'align' => 'center', 'width' => 100, 'widthColumn' => 150),
			'ean13' => array('title' => $this->l('EAN13'), 'align' => 'center', 'width' => 75, 'widthColumn' => 100),
			'name' => array('title' => $this->l('Name'), 'width' => 350, 'widthColumn' => 'auto', 'filter_key' => 'b!name'),
			'coverage' => array('title' => $this->l('Average time left'), 'width' => 50, 'widthColumn' => 60, 'orderby' => false, 'search' => false),
			'stock' => array('title' => $this->l('Qty in stock'), 'width' => 50, 'widthColumn' => 60, 'orderby' => false, 'search' => false),
		);

		$this->_select = 'a.id_product as id, COUNT(pa.id_product_attribute) as variations, s.physical_quantity as stock';
		$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (pa.id_product = a.id_product)
						LEFT JOIN `'._DB_PREFIX_.'stock` s ON (s.id_product = a.id_product AND s.id_product_attribute = 0)';

		parent::__construct();
	}

	/**
	 * Method called when an ajax request is made
	 * @see AdminController::postProcess()
	 */
	public function ajaxProcess()
	{
		if (Tools::isSubmit('id'))
		{

			$this->lang = false;
			$lang_id = (int)$this->context->language->id;
			$product_id = (int)Tools::getValue('id');

			$query = '
			SELECT a.id_product_attribute as id, a.id_product, a.reference, a.ean13,
				   IFNULL(CONCAT(pl.name, \' : \', GROUP_CONCAT(agl.`name`, \' - \', al.name SEPARATOR \', \')),pl.name) as name,
				   s.physical_quantity as stock
			FROM '._DB_PREFIX_.'product_attribute a
			INNER JOIN '._DB_PREFIX_.'product_lang pl ON (pl.id_product = a.id_product AND pl.id_lang = '.$lang_id.')
			LEFT JOIN '._DB_PREFIX_.'product_attribute_combination pac ON (pac.id_product_attribute = a.id_product_attribute)
			LEFT JOIN '._DB_PREFIX_.'attribute atr ON (atr.id_attribute = pac.id_attribute)
			LEFT JOIN '._DB_PREFIX_.'attribute_lang al ON (al.id_attribute = atr.id_attribute AND al.id_lang = '.$lang_id.')
			LEFT JOIN '._DB_PREFIX_.'attribute_group_lang agl ON (agl.id_attribute_group = atr.id_attribute_group AND agl.id_lang = '.$lang_id.')
			LEFT JOIN '._DB_PREFIX_.'stock s ON (a.id_product_attribute = s.id_product_attribute)
			WHERE a.id_product = '.$product_id.'
			GROUP BY a.id_product_attribute';

			$datas = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
			foreach ($datas as &$data)
				$data['coverage'] = StockManagerFactory::getManager()->getProductCoverage($data['id'], $data['id_product'], 7);

			echo Tools::jsonEncode(array('data'=> $datas, 'fields_display' => $this->fieldsDisplay));
		}
		die;
	}

	/**
	 * AdminController::initContent() override
	 * @see AdminController::initContent()
	 */
	public function initContent()
	{
		$this->display = 'list';

		$stock_cover_periods = array(
			$this->l('One week') => 7,
			$this->l('Two weeks') => 14,
			$this->l('Three weeks') => 21,
			$this->l('One month') => 31,
			$this->l('Six months') => 186,
			$this->l('One year') => 365
		);

		$this->context->smarty->assign('stock_cover_periods', $stock_cover_periods);
		$this->context->smarty->assign('stock_cover_cur_period', $this->getCurrentCoveragePeriod());

		parent::initContent();
	}

	/**
	 * AdminController::getList() override
	 * @see AdminController::getList()
	 */
	public function getList($id_lang, $orderBy = null, $orderWay = null, $start = 0, $limit = null, $id_lang_shop = false)
	{
		parent::getList($id_lang, $orderBy, $orderWay, $start, $limit, $id_lang_shop);

		if ($this->display == 'list')
		{
			$nb_items = count($this->_list);
			for ($i = 0; $i < $nb_items; ++$i)
			{
				$item = &$this->_list[$i];
				if ((int)$item['variations'] <= 0)
				{
					$item['coverage'] = StockManagerFactory::getManager()->getProductCoverage($item['id'], 0, 4);
					$this->addRowActionSkipList('details', array($item['id']));
				}
			}
		}
	}

	/**
	 * Gets the current coverage period used
	 *
	 * @return int coverage period
	 */
	private function getCurrentCoveragePeriod()
	{
		static $coverage_period = 0;

		// if coverage period == 0 then it is set to 7, otherwise, checks if we can get it via $_GET
		$coverage_period = ($coverage_period == 0 ? 7 : ((int)Tools::getValue('coverage_period') ? (int)Tools::getValue('coverage_period') : 7));

		return $coverage_period;
	}
}