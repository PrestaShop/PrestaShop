<?php
/**
 * 2007-2018 PrestaShop.
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Admin controller for the attribute / attribute group.
 */
class SpecificPriceController extends FrameworkBundleAdminController
{
    /**
     * get specific price list for a product.
     *
     * @param $idProduct The product ID
     *
     * @return string JSON
     */
    public function listAction($idProduct)
    {
        $response = new JsonResponse();

        $contextAdapter = $this->get('prestashop.adapter.legacy.context');
        $locales = $contextAdapter->getLanguages();
        $productAdapter = $this->get('prestashop.adapter.data_provider.product');
        $adminProductWrapper = $this->get('prestashop.adapter.admin.wrapper.product');
        $shopContextAdapter = $this->get('prestashop.adapter.shop.context');
        $shops = $shopContextAdapter->getShops();
        $countries = $this->get('prestashop.adapter.data_provider.country')->getCountries($locales[0]['id_lang']);
        $currencies = $this->get('prestashop.adapter.data_provider.currency')->getCurrencies();
        $groups = $this->get('prestashop.adapter.data_provider.group')->getGroups($locales[0]['id_lang']);

        //get product
        $product = $productAdapter->getProduct((int) $idProduct);
        if (!is_object($product) || empty($product->id)) {
            $response->setStatusCode(400);

            return $response;
        }

        $response->setData($adminProductWrapper->getSpecificPricesList(
            $product,
            $contextAdapter->getContext()->currency,
            $shops,
            $currencies,
            $countries,
            $groups
        ));

        return $response;
    }

    /**
     * Add specific price Form process.
     *
     * @param Request $request The request
     *
     * @return string
     */
    public function addAction(Request $request)
    {
        $response = new JsonResponse();
        $idProduct = isset($request->get('form')['id_product']) ? $request->get('form')['id_product'] : null;

        $adminProductWrapper = $this->get('prestashop.adapter.admin.wrapper.product');
        $errors = $adminProductWrapper->processProductSpecificPrice($idProduct, $request->get('form')['step2']['specific_price']);

        if (!empty($errors)) {
            $response->setData(implode(', ', $errors));
            $response->setStatusCode(400);
        }

        return $response;
    }

    /**
     * Delete a specific price.
     *
     * @param int $idSpecificPrice The specific price ID
     * @param Request $request The request
     *
     * @return string
     */
    public function deleteAction($idSpecificPrice, Request $request)
    {
        $response = new JsonResponse();

        $adminProductWrapper = $this->get('prestashop.adapter.admin.wrapper.product');
        $res = $adminProductWrapper->deleteSpecificPrice((int) $idSpecificPrice);

        if ($res['status'] == 'error') {
            $response->setStatusCode(400);
        }

        $response->setData($res['message']);

        return $response;
    }
}
