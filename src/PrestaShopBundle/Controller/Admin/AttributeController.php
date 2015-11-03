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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use PrestaShopBundle\Model\Product\AdminModelAdapter as ProductAdminModelAdapter;

/**
 * Admin controller for the attribute / attribute group
 */
class AttributeController extends FrameworkBundleAdminController
{
    /**
     * get All Attributes as json
     *
     * @return string
     */
    public function getAllAttributesAction()
    {
        $response = new JsonResponse();
        $locales = $this->container->get('prestashop.adapter.legacy.context')->getLanguages();
        $attributes = $this->container->get('prestashop.adapter.data_provider.attribute')->getAttributes($locales[0]['id_lang'], true);

        $data = [];
        foreach ($attributes as $attribute) {
            $data[] = [
                'value' => $attribute['id_attribute'],
                'label' => $attribute['public_name'].' : '.$attribute['name'],
                'data' => [
                    'id_group' => $attribute['id_attribute_group'],
                    'name' => $attribute['name'],
                ]
            ];
        }

        $response->setData($data);
        return $response;
    }

    /**
     * Attributes generator
     *
     * @param Request $request The request
     *
     * @return string
     */
    public function attributesGeneratorAction(Request $request)
    {
        $response = new JsonResponse();
        $options = $request->get('options');
        $idProduct = isset($request->get('form')['id_product']) ? $request->get('form')['id_product'] : null;

        //get product
        $productAdapter = $this->container->get('prestashop.adapter.data_provider.product');
        $product = $productAdapter->getProduct((int)$idProduct, true);

        if (!is_object($product) || empty($product->id) || empty($options) || !is_array($options)) {
            $response->setStatusCode(400);
            return $response;
        }

        $modelMapper = new ProductAdminModelAdapter($product->id, $this->container);

        //store exisiting product combinations
        $existingCombinationsIds = array_map(function ($o) {
            return $o['id_product_attribute'];
        }, $product->getAttributeCombinations(1, false));

        //create attributes
        $this->container->get('prestashop.adapter.admin.controller.attribute_generator')->processGenerate($product, $options);

        //get all product combinations
        $allCombinations = $product->getAttributeCombinations(1, false);

        $allCombinationsIds = array_map(function ($o) {
            return $o['id_product_attribute'];
        }, $allCombinations);

        //get new created combinations Ids
        $newCombinationIds = array_diff($allCombinationsIds, $existingCombinationsIds);

        $newCombinations = [];
        foreach ($newCombinationIds as $combinationId) {
            $attribute = $product->getAttributeCombinationsById($combinationId, 1);
            $newCombinations[] = $modelMapper->getFormCombination($attribute[0]);
        }

        $response->setData($newCombinations);

        return $response;
    }

    /**
     * Delete a product atrtibute
     *
     * @param int $idAttribute The attribute ID
     * @param int $idProduct The product ID
     * @param Request $request The request
     *
     * @return string
     */
    public function deleteAttributeAction($idAttribute, $idProduct, Request $request)
    {
        $translator = $this->container->get('prestashop.adapter.translator');
        $response = new JsonResponse();

        if (!$request->isXmlHttpRequest()) {
            return $response;
        }

        $res = $this->container->get('prestashop.adapter.admin.controller.attribute_generator')
            ->ajaxProcessDeleteProductAttribute($idAttribute, $idProduct);

        if ($res['status'] == 'error') {
            $response->setStatusCode(400);
        }

        $response->setData(['message' => $translator->trans($res['message'])]);
        return $response;
    }
}
