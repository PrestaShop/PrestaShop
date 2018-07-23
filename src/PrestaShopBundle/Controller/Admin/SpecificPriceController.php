<?php
/**
 * 2007-2018 PrestaShop
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

use DateTime;
use PrestaShop\PrestaShop\Adapter\Entity\PrestaShopObjectNotFoundException;
use PrestaShop\PrestaShop\Adapter\Product\AdminProductWrapper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/**
 * Admin controller for the attribute / attribute group
 */
class SpecificPriceController extends FrameworkBundleAdminController
{
    /**
     * Get the specific price list for a product
     * to be displayed
     *
     * @param int $idProduct
     *
     * @return string JSON
     */
    public function listAction($idProduct)
    {
        $response = new JsonResponse();

        $contextAdapter = $this->get('prestashop.adapter.legacy.context');
        $locales = $contextAdapter->getLanguages();
        $productAdapter = $this->get('prestashop.adapter.data_provider.product');
        /** @var AdminProductWrapper $adminProductWrapper */
        $adminProductWrapper = $this->get('prestashop.adapter.admin.wrapper.product');
        $shopContextAdapter = $this->get('prestashop.adapter.shop.context');
        $shops = $shopContextAdapter->getShops();
        $countries = $this->get('prestashop.adapter.data_provider.country')->getCountries($locales[0]['id_lang']);
        $currencies = $this->get('prestashop.adapter.data_provider.currency')->getCurrencies();
        $groups = $this->get('prestashop.adapter.data_provider.group')->getGroups($locales[0]['id_lang']);

        //get product
        $product = $productAdapter->getProduct((int)$idProduct);
        if (!is_object($product) || empty($product->id)) {
            $response->setStatusCode(400);
            return $response;
        }

        $response->setData(
            $adminProductWrapper->getSpecificPricesList(
                $product,
                $contextAdapter->getContext()->currency,
                $shops,
                $currencies,
                $countries,
                $groups
            )
        );

        return $response;
    }

    /**
     * Handle form submission of 'add/update a specific price' action
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function addAction(Request $request)
    {
        $response = new JsonResponse();
        $idProduct = isset($request->get('form')['id_product']) ? $request->get('form')['id_product'] : null;

        $idSpecificPrice = null;
        if (isset($request->get('form')['step2']['specific_price']['sp_update_id'])) {
            $idSpecificPrice = $request->get('form')['step2']['specific_price']['sp_update_id'];

            if (is_numeric($idSpecificPrice)) {
                $idSpecificPrice = (int)$idSpecificPrice;
            } else {
                $idSpecificPrice = null;
            }
        }

        /** @var AdminProductWrapper $adminProductWrapper */
        $adminProductWrapper = $this->get('prestashop.adapter.admin.wrapper.product');
        $errors = $adminProductWrapper->processProductSpecificPrice(
            $idProduct,
            $request->get('form')['step2']['specific_price'],
            $idSpecificPrice
        );

        if (!empty($errors)) {
            $response->setData(implode(', ', $errors));
            $response->setStatusCode(400);
        }

        return $response;
    }

    /**
     * Get one specific price list for a product
     *
     * @Template("@PrestaShop/Admin/Product/ProductPage\Forms/form_specific_price.html.twig")
     *
     * @param int $idSpecificPrice
     *
     * @return array
     */
    public function getUpdateFormAction($idSpecificPrice)
    {
        /** @var AdminProductWrapper $adminProductWrapper */
        $adminProductWrapper = $this->get('prestashop.adapter.admin.wrapper.product');
        try {
            $price = $adminProductWrapper->getSpecificPriceDataById($idSpecificPrice);
        } catch (PrestaShopObjectNotFoundException $e) {
            return new Response(sprintf('Cannot find specific price %d', $idSpecificPrice), Response::HTTP_BAD_REQUEST);
        }

        $formData = $this->formatSpecificPriceToPrefillForm($idSpecificPrice, $price);

        $options = ['selected_product_attribute' => $price->id_product_attribute];
        $formBuilder = $this->createFormBuilder();
        $formBuilder->add('step2', 'PrestaShopBundle\Form\Admin\Product\EmbeddedSpecificPrice', $options);

        $form = $formBuilder->getForm();
        $form->setData($formData);

        return [
            'form' => $form->createView()->offsetGet('step2')->offsetGet('specific_price'),
            'has_combinations' => ($price->id_product_attribute !== '0')
        ];
    }

    /**
     * Delete a specific price
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
        $res = $adminProductWrapper->deleteSpecificPrice((int)$idSpecificPrice);

        if ($res['status'] == 'error') {
            $response->setStatusCode(400);
        }

        $response->setData($res['message']);
        return $response;
    }

    /**
     * @param int $id
     * @param \SpecificPrice $price
     *
     * @return array
     */
    private function formatSpecificPriceToPrefillForm($id, $price)
    {
        if ($price->reduction_type === 'percentage') {
            $reduction = $price->reduction*100;
        } else {
            $reduction = $price->reduction;
        }

        $formattedFormData = [
            'sp_update_id' => $id,
            'sp_id_shop' => $price->id_shop,
            'sp_id_currency' => $price->id_currency,
            'sp_id_country' => $price->id_country,
            'sp_id_group' => $price->id_group,
            'sp_id_customer' => null,
            'sp_id_product_attribute' => $price->id_product_attribute,
            'sp_from' => self::formatForDatePicker($price->from),
            'sp_to' => self::formatForDatePicker($price->to),
            'sp_from_quantity' => $price->from_quantity,
            'sp_price' => ($price->price !== '-1.000000') ? $price->price : '',
            'leave_bprice' => ($price->price === '-1.000000'),
            'sp_reduction' => $reduction,
            'sp_reduction_type' => $price->reduction_type,
            'sp_reduction_tax' => $price->reduction_tax,
        ];

        if ($price->id_customer !== '0') {
            $formattedFormData['sp_id_customer'] = ['data' => [$price->id_customer]];
        }

        $cleanedFormData = array_map(function ($item) {
            if (!$item) {
                return null;
            } else {
                return $item;
            }
        }, $formattedFormData);

        return ['step2' => ['specific_price' => $cleanedFormData]];
    }

    /**
     * @param string $dateAsString
     *
     * @return null|string If date is 0000-00-00 00:00:00, null is returned
     *
     * @throws \PrestaShopDatabaseExceptionCore if date is not valid
     */
    private static function formatForDatePicker($dateAsString)
    {
        if ('0000-00-00 00:00:00' === $dateAsString) {
            return null;
        }

        try {
            $dateTime = new DateTime($dateAsString);
        } catch (\Exception $e) {
            throw new \PrestaShopDatabaseExceptionCore(sprintf('Found bad date for specific price: %s', $dateAsString));
        }

        return $dateTime->format('Y-m-d');
    }
}
