<?php
/*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
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
			<div class="panel-heading">
				'.$this->displayName.'
			</div>
			<form action="" method="post" id="categoriesForm" class="form-horizontal">
				<div class="row row-margin-bottom">
					<label class="control-label col-lg-3">
						'.$this->l('Choose a category').'
					</label>
					<div class="col-lg-6">
						<select name="id_category" onchange="$(\'#categoriesForm\').submit();">
							<option value="0">'.$this->l('All').'</option>';
						foreach ($categories as $category)
							$html .= '<option value="'.$category['id_category'].'"'.($id_category == $category['id_category'] ? ' selected="selected"' : '').'>'.
								$category['name'].'
							</option>';
				$html .= '
						</select>
					</div>
				</div>
			</form>
			<ul class="list-group">
				<li class="list-group-item">'.$this->returnLine($this->l('Products available:'), '<span class="badge">'.(int)$total).'</span></li>
				<li class="list-group-item">'.$this->returnLine($this->l('Average price (base price):'), '<span class="badge">'.Tools::displayPrice($averagePrice, $this->context->currency)).'</span></li>
				<li class="list-group-item">'.$this->returnLine($this->l('Product pages viewed:'), '<span class="badge">'.(int)$totalPageViewed).'</span></li>
				<li class="list-group-item">'.$this->returnLine($this->l('Products bought:'), '<span class="badge">'.(int)$totalBought).'</span></li>
				<li class="list-group-item">'.$this->returnLine($this->l('Average number of page visits:'), '<span class="badge">'.number_format((float)$averageViewed, 2, '.', '')).'</span></li>
				<li class="list-group-item">'.$this->returnLine($this->l('Average number of purchases:'), '<span class="badge">'.number_format((float)$averagePurchase, 2, '.', '')).'</span></li>
				<li class="list-group-item">'.$this->returnLine($this->l('Images available:'), '<span class="badge">'.(int)$totalPictures).'</span></li>
				<li class="list-group-item">'.$this->returnLine($this->l('Average number of images:'), '<span class="badge">'.number_format((float)$averagePictures, 2, '.', '')).'</span></li>
				<li class="list-group-item">'.$this->returnLine($this->l('Products never viewed:'), '<span class="badge">'.(int)$totalNV.' / '.(int)$total).'</span></li>
				<li class="list-group-item">'.$this->returnLine('<a style="cursor : pointer" onclick="$(\'#pnb\').slideToggle();">'.$this->l('Products never purchased:').'</a>', '<span class="badge">'.(int)$totalNB.' / '.(int)$total).'</span></li>
				<li class="list-group-item">'.$this->returnLine($this->l('Conversion rate*:'), '<span class="badge">'.$conversion).'</span></li>
			</ul>
			<div class="row row-margin-bottom">
				<p>
					<i class="icon-asterisk"></i>'.$this->l('Define the average conversion rate for the product page (It is possible to purchase a product without viewing the product page, so this rate can be greater than 1).').'
				</p>
			</div>';

		if (count($productsNB) && count($productsNB) < 50)
		{
			$html .= '
				<div class="panel-heading">'.$this->l('Products never purchased').'</div>
				<table class="table">
					<thead>
						<tr>
							<th><span class="title_box active">'.$this->l('ID').'</span></th>
							<th><span class="title_box active">'.$this->l('Name').'</span></th>
							<th><span class="title_box active">'.$this->l('Edit / View').'</span></th>
						</tr>
					</thead>
					<tbody>';
			foreach ($productsNB as $product)
				$html .= '
					<tr'.($irow++ % 2 ? ' class="alt_row"' : '').'>
						<td>'.$product['id_product'].'</td>
						<td>'.$product['name'].'</td>
						<td class="left">
							<div class="btn-group btn-group-action">
								<a class="btn btn-default" href="index.php?tab=AdminProducts&id_product='.$product['id_product'].'&addproduct&token='.$productToken.'" target="_blank">
									<i class="icon-edit"></i> '.$this->l('Edit').'
								</a>
								<button data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button">
									<span class="caret">&nbsp;</span>
								</button>
								<ul class="dropdown-menu">
									<li>
										<a href="'.$this->context->link->getProductLink($product['id_product'], $product['link_rewrite']).'" target="_blank">
											<i class="icon-eye-open"></i> '.$this->l('See').'
										</a>
									</li>
								</ul>
							</div>
						</td>
					</tr>';
			$html .= '
					</tbody>
				</table>';
		}
		return $html;
	}

	private function returnLine($label, $data)
	{
		return '<tr><td>'.$label.'</td><td>'.$data.'</td></tr>';
	}
}