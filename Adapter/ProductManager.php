<?php
/**
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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Adapter;

class ProductManager
{
    /**
     * Returns a collection of products, using default language, currency and others, from Context.
     *
     * @param integer $offset
     * @param integer $limit
     * @param string $orderBy Field name to sort during SQL query
     * @param string $orderWay 'asc' or 'desc'
     * @return array A list of products, as a collection of legacy Product objects.
     */
    public function getProductList($offset, $limit, $orderBy, $orderWay)
    {
        // TODO
        return array('toto', 'titi', $offset, $limit, $orderBy, $orderWay);
    }
    
    public function mapLegacyParametersProductForm($coreParameters = array())
    {
        $params = array();
        if ($coreParameters['id_product'] == 'new') {
            $params['addproduct'] = 1;
        } else {
            $params['updateproduct'] = 1;
            $params['id_product'] = $coreParameters['id_product'];
        }
        return $params;
    }
}
