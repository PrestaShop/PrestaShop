<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Adapter\Product;

use Symfony\Component\HttpFoundation\Request;

/**
 * Extracted from Product Controller, used to cleanup the request.
 */
class FilterCategoriesRequestPurifier
{
    /**
     * Changes the filter category values in case it is not numeric or signed.
     * @param Request $request
     * @return Request
     */
    public function purify(Request $request)
    {
        if ($request->isMethod('POST')) {
            foreach ($request->request->all() as $param => $value) {
                if ($param === 'filter_category' && (!is_numeric($value) || $value < 0)) {
                    $request->request->set($param, '');
                }
            }
        }
        return $request;
    }
}
