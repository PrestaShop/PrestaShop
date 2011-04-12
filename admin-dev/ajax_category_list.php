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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

	define('PS_ADMIN_DIR', getcwd());
	include_once('../config/config.inc.php');
	include_once('tabs/AdminCatalog.php');
	include_once('tabs/AdminProducts.php');
	include_once('init.php');

	if (Tools::getValue('token') != Tools::getAdminTokenLite('AdminCatalog'))
		die(1);

	$catalog = new AdminCatalog();
	$adminProducts = new AdminProducts();

	global $cookie;

	echo '			<tr>
						<td class="col-left"><label for="id_category_default" class="t">'.$adminProducts->getL('Default category:').'</label></td>
						<td>
							<select id="id_category_default" name="id_category_default" onchange="checkDefaultCategory(this.value);">';
		$categories = Category::getCategories((int)($cookie->id_lang), false);
		Category::recurseCategory($categories, $categories[0][1], 1, (int)(Tools::getValue('id_category_default')));
		echo '			</select>
						</td>
					</tr>
					<tr>
						<td class="col-left">'.$adminProducts->getL('Catalog:').'</td>
						<td>
							<div style="overflow: auto; min-height: 300px; padding-top: 0.6em;" id="categoryList">
								<script type="text/javascript">
								$(document).ready(function() {
									$(\'div#categoryList input.categoryBox\').click(function (){
										if ($(this).is(\':not(:checked)\') && $(\'div#categoryList input.id_category_default\').val() == $(this).val())
											alert(\''.utf8_encode(html_entity_decode($adminProducts->getL('Consider changing the default category.'))).'\');
									});
								});
								</script>
								<table cellspacing="0" cellpadding="0" class="table">
									<tr>
										<th><input type="checkbox" name="checkme" class="noborder" onclick="checkDelBoxes(this.form, \'categoryBox[]\', this.checked)" /></th>
										<th>'.$adminProducts->getL('ID').'</th>
										<th style="width: 600px">'.$adminProducts->getL('Name').'</th>
									</tr>';
			$done = array();
			$index = array();
			
			$categoryBox = Tools::getValue('categoryBox');
			if ($categoryBox != '')		
			{
				$categoryBox = @unserialize($categoryBox);
				foreach ($categoryBox AS $k => $row)
					$index[] = $row;
			}
			elseif ((int)Tools::getValue('id_product'))
				$index = Product::getProductCategories((int)Tools::getValue('id_product'));
			$adminProducts->recurseCategoryForInclude((int)(Tools::getValue('id_product')), $index, $categories, $categories[0][1], 1, (int)(Tools::getValue('id_category_default')));
			echo '				</table>
								<p style="padding:0px; margin:0px 0px 10px 0px;">'.$adminProducts->getL('Mark all checkbox(es) of categories in which product is to appear').'<sup> *</sup></p>
							</div>
					</tr>';
