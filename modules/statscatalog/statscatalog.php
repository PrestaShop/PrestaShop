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
*  @version  Release: $Revision: 7060 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class StatsCatalog extends Module
{
	private $_join = '';
	private $_where = '';

	public function __construct()
	{
		$this->name = 'statscatalog';
		$this->tab = 'analytics_stats';
		$this->version = 1.0;
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Catalog statistics');
		$this->description = $this->l('General statistics about your catalog.');
	}

	public function install()
	{
		return (parent::install() && $this->registerHook('AdminStatsModules'));
	}

	public function getQuery1()
	{
		$sql = 'SELECT COUNT(DISTINCT p.`id_product`) AS total, SUM(product_shop.`price`) / COUNT(product_shop.`price`) AS average_price, COUNT(DISTINCT i.`id_image`) AS images
				FROM `'._DB_PREFIX_.'product` p
				'.Shop::addSqlAssociation('product', 'p').'
				LEFT JOIN `'._DB_PREFIX_.'image` i ON i.`id_product` = p.`id_product`
				'.$this->_join.'
				WHERE product_shop.`active` = 1
					'.$this->_where;
		return DB::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
	}

	public function getTotalPageViewed()
	{
		$sql = 'SELECT SUM(pv.`counter`) AS viewed
				FROM `'._DB_PREFIX_.'product` p
				'.Shop::addSqlAssociation('product', 'p').'
				LEFT JOIN `'._DB_PREFIX_.'page` pa ON p.`id_product` = pa.`id_object`
				LEFT JOIN `'._DB_PREFIX_.'page_type` pt ON (pt.`id_page_type` = pa.`id_page_type` AND pt.`name` = \'product.php\')
				LEFT JOIN `'._DB_PREFIX_.'page_viewed` pv ON pv.`id_page` = pa.`id_page`
				'.$this->_join.'
				WHERE product_shop.`active` = 1
					'.$this->_where;
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
		return isset($result['viewed']) ? $result['viewed'] : 0;
	}

	public function getTotalProductViewed()
	{
		$sql = 'SELECT COUNT(DISTINCT pa.`id_object`)
				FROM `'._DB_PREFIX_.'page_viewed` pv
				LEFT JOIN `'._DB_PREFIX_.'page` pa ON pv.`id_page` = pa.`id_page`
				LEFT JOIN `'._DB_PREFIX_.'page_type` pt ON pt.`id_page_type` = pa.`id_page_type`
				LEFT JOIN `'._DB_PREFIX_.'product` p ON p.`id_product` = pa.`id_object`
				'.Shop::addSqlAssociation('product', 'p').'
				'.$this->_join.'
				WHERE pt.`name` = \'product.php\'
					AND product_shop.`active` = 1
					'.$this->_where;
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
	}

	public function getTotalBought()
	{
		$sql = 'SELECT SUM(od.`product_quantity`) AS bought
				FROM `'._DB_PREFIX_.'orders` o
				LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON o.`id_order` = od.`id_order`
				LEFT JOIN `'._DB_PREFIX_.'product` p ON p.`id_product` = od.`product_id`
				'.$this->_join.'
				WHERE o.valid = 1
					'.$this->_where;
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
		return isset($result['bought']) ? $result['bought'] : 0;
	}

	public function getProductsNB($id_lang)
	{
		$sql = 'SELECT p.`id_product`
				FROM `'._DB_PREFIX_.'orders` o
				LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON o.`id_order` = od.`id_order`
				LEFT JOIN `'._DB_PREFIX_.'product` p ON p.`id_product` = od.`product_id`
				'.Shop::addSqlAssociation('product', 'p').'
				'.$this->_join.'
				WHERE o.valid = 1
					'.$this->_where.'
					AND product_shop.`active` = 1
				GROUP BY p.`id_product`';
		$precalc = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

		$precalc2 = array();
		foreach ($precalc as $array)
			$precalc2[] = (int)$array['id_product'];

		$sql = 'SELECT p.id_product, pl.name, pl.link_rewrite
				FROM `'._DB_PREFIX_.'product` p
				'.Shop::addSqlAssociation('product', 'p').'
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
					ON (pl.`id_product` = p.`id_product` AND pl.id_lang = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').')
				'.$this->_join.'
				WHERE product_shop.`active` = 1
					'.(count($precalc2) ? 'AND p.`id_product` NOT IN ('.implode(',', $precalc2).')' : '').'
					'.$this->_where;
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
		return array('total' => Db::getInstance(_PS_USE_SQL_SLAVE_)->NumRows(), 'result' => $result);
	}

	public function hookAdminStatsModules($params)
	{
		$categories = Category::getCategories($this->context->language->id, true, false);
		$productToken = Tools::getAdminToken('AdminProducts'.(int)(Tab::getIdFromClassName('AdminProducts')).(int)$this->context->employee->id);
		$irow = 0;

		if ($id_category = (int)(Tools::getValue('id_category')))
		{
			$this->_join = ' LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_product` = p.`id_product`)';
			$this->_where = ' AND cp.`id_category` = '.$id_category;
		}

		$result1 = $this->getQuery1(true);
		$total = $result1['total'];
		$averagePrice = $result1['average_price'];
		$totalPictures = $result1['images'];
		$averagePictures = $total ? $totalPictures / $total : 0;

		$neverBought = $this->getProductsNB($this->context->language->id);
		$totalNB = $neverBought['total'];
		$productsNB = $neverBought['result'];

		$totalBought = $this->getTotalBought();
		$averagePurchase = $total ? ($totalBought / $total) : 0;

		$totalPageViewed = $this->getTotalPageViewed();
		$averageViewed = $total ? ($totalPageViewed / $total) : 0;		
		$conversion = number_format((float)($totalPageViewed ? ($totalBought / $totalPageViewed) : 0), 2, '.', '');
		if ($conversionReverse = number_format((float)($totalBought ? ($totalPageViewed / $totalBought) : 0), 2, '.', ''))
			$conversion .= sprintf($this->l('(1 purchase / %d visits)'), $conversionReverse);

		$totalNV = $total - $this->getTotalProductViewed();

		$html = '
		<script type="text/javascript" language="javascript">$(\'#calendar\').slideToggle();</script>
		<div class="blocStats"><h2 class="icon-'.$this->name.'"><span></span>'.$this->displayName.'</h2>
			<div class="margin-form">
				<form action="" method="post" id="categoriesForm">
				<label>
				'.$this->l('Choose a category').'
				</label>
					<select name="id_category" onchange="$(\'#categoriesForm\').submit();">
						<option value="0">'.$this->l('All').'</option>';
		foreach ($categories as $category)
			$html .= '<option value="'.$category['id_category'].'"'.($id_category == $category['id_category'] ? ' selected="selected"' : '').'>'.
				$category['name'].'
			</option>';
		$html .= '
					</select>
				</form>
			</div>
			<table>
				'.$this->returnLine($this->l('Products available:'), (int)$total).'
				'.$this->returnLine($this->l('Average price (base price):'), Tools::displayPrice($averagePrice, $this->context->currency)).'
				'.$this->returnLine($this->l('Product pages viewed:'), (int)$totalPageViewed).'
				'.$this->returnLine($this->l('Products bought:'), (int)$totalBought).'
				'.$this->returnLine($this->l('Average number of page visits:'), number_format((float)$averageViewed, 2, '.', '')).'
				'.$this->returnLine($this->l('Average number of purchases:'), number_format((float)$averagePurchase, 2, '.', '')).'
				'.$this->returnLine($this->l('Images available:'), (int)$totalPictures).'
				'.$this->returnLine($this->l('Average number of images:'), number_format((float)$averagePictures, 2, '.', '')).'
				'.$this->returnLine($this->l('Products never viewed:'), (int)$totalNV.' / '.(int)$total).'
				'.$this->returnLine('<a style="cursor : pointer" onclick="$(\'#pnb\').slideToggle();">'.$this->l('Products never purchased:').'</a>', (int)$totalNB.' / '.(int)$total).'
				'.$this->returnLine($this->l('Conversion rate*:'), $conversion).'
			</table>
			<div style="margin-top: 20px;">
				<span style="color:red;font-weight:bold">*</span> 
				'.$this->l('Average conversion rate for the product page. It is possible to purchase a product without viewing the product page, so this rate can be greater than 1.').'
			</div>
		</div>';

		if (count($productsNB) && count($productsNB) < 50)
		{
			$html .= '<br />
			<div class="blocStats"><h2 class="icon-basket-delete"><span></span>'.$this->l('Products never purchased').'</h2>
				<table cellpadding="0" cellspacing="0" class="table">
					<tr><th>'.$this->l('ID').'</th><th>'.$this->l('Name').'</th><th>'.$this->l('Edit / View').'</th></tr>';
			foreach ($productsNB as $product)
				$html .= '
					<tr'.($irow++ % 2 ? ' class="alt_row"' : '').'>
						<td>'.$product['id_product'].'</td>
						<td style="width: 400px;">'.$product['name'].'</td>
						<td style="text-align: right">
							<a href="index.php?tab=AdminProducts&id_product='.$product['id_product'].'&addproduct&token='.$productToken.'" target="_blank"><img src="../modules/'.$this->name.'/page_edit.png" /></a>
							<a href="'.$this->context->link->getProductLink($product['id_product'], $product['link_rewrite']).'" target="_blank"><img src="../modules/'.$this->name.'/application_home.png" /></a>
						</td>
					</tr>';
			$html .= '
				</table>
			</div>';
		}
		return $html;
	}

	private function returnLine($label, $data)
	{
		return '<tr><td>'.$label.'</td><td style="color:green;font-weight:bold;padding-left:20px;">'.$data.'</td></tr>';
	}
}