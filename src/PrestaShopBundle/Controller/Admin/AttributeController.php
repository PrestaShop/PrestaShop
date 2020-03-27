<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin;

use Product;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Admin controller for the attribute / attribute group.
 */
class AttributeController extends FrameworkBundleAdminController
{
    /**
     * get All Attributes as json.
     *
     * @AdminSecurity("is_granted(['read'], request.get('_legacy_controller'))")
     *
     * @return JsonResponse
     */
    public function getAllAttributesAction()
    {
        $response = new JsonResponse();
        $locales = $this->get('prestashop.adapter.legacy.context')->getLanguages();
        $attributes = $this->get('prestashop.adapter.data_provider.attribute')->getAttributes($locales[0]['id_lang'], true);

        $dataGroupAttributes = [];
        $data = [];
        foreach ($attributes as $attribute) {
            /* Construct attribute group selector. Ex : Color : All */
            $dataGroupAttributes[$attribute['id_attribute_group']] = [
                'value' => 'group-' . $attribute['id_attribute_group'],
                'label' => $attribute['public_name'] . ' : ' . $this->trans('All', 'Admin.Global'),
                'data' => [
                    'id_group' => $attribute['id_attribute_group'],
                    'name' => $attribute['public_name'],
                ],
            ];

            $data[] = [
                'value' => $attribute['id_attribute'],
                'label' => $attribute['public_name'] . ' : ' . $attribute['name'],
                'data' => [
                    'id_group' => $attribute['id_attribute_group'],
                    'name' => $attribute['name'],
                ],
            ];
        }

        $data = array_merge($dataGroupAttributes, $data);

        $response->setData($data);

        return $response;
    }

    /**
     * Attributes generator.
     *
     * @AdminSecurity("is_granted(['create', 'update'], request.get('_legacy_controller'))")
     *
     * @param Request $request The request
     *
     * @return JsonResponse
     */
    public function attributesGeneratorAction(Request $request)
    {
        $response = new JsonResponse();
        $locales = $this->get('prestashop.adapter.legacy.context')->getLanguages();
        $options = $request->get('options');
        $idProduct = isset($request->get('form')['id_product']) ? $request->get('form')['id_product'] : null;

        //get product
        $productAdapter = $this->get('prestashop.adapter.data_provider.product');
        $product = $productAdapter->getProduct((int) $idProduct);

        if (!is_object($product) || empty($product->id) || empty($options) || !is_array($options)) {
            $response->setStatusCode(400);

            return $response;
        }

        //store exisiting product combinations
        $existingCombinationsIds = array_map(function ($o) {
            return $o['id_product_attribute'];
        }, $product->getAttributeCombinations(1, false));

        //get clean attributes ids
        $newOptions = [];
        foreach ($options as $idGroup => $attributes) {
            foreach ($attributes as $attribute) {
                //If attribute is a group attribute, replace group data by all attributes group
                if (false !== strpos($attribute, 'group')) {
                    $allGroupAttributes = $this->get('prestashop.adapter.data_provider.attribute')->getAttributeIdsByGroup((int) $idGroup, true);
                    foreach ($allGroupAttributes as $groupAttribute) {
                        $newOptions[$idGroup][$groupAttribute] = $groupAttribute;
                    }
                } else {
                    $newOptions[$idGroup][$attribute] = $attribute;
                }
            }
        }

        //create attributes
        $this->get('prestashop.adapter.admin.controller.attribute_generator')->processGenerate($product, $newOptions);

        //get all product combinations
        $allCombinations = $product->getAttributeCombinations(1, false);

        $allCombinationsIds = array_map(function ($o) {
            return $o['id_product_attribute'];
        }, $allCombinations);

        //get new created combinations Ids
        $newCombinationIds = array_diff($allCombinationsIds, $existingCombinationsIds);

        $attributes = $product->sortCombinationByAttributePosition($newCombinationIds, $locales[0]['id_lang']);
        $this->ensureProductHasDefaultCombination($product, $attributes);

        $response = new JsonResponse();
        $combinationDataProvider = $this->get('prestashop.adapter.data_provider.combination');
        $result = array(
            'ids_product_attribute' => array(),
            'form' => '',
        );

        foreach ($attributes as $attribute) {
            foreach ($attribute as $combination) {
                $form = $this->get('form.factory')
                    ->createNamed(
                        'combination_' . $combination['id_product_attribute'],
                        'PrestaShopBundle\Form\Admin\Product\ProductCombination',
                        $combinationDataProvider->getFormCombination($combination['id_product_attribute'])
                    );
                $result['form'] .= $this->renderView(
                    '@Product/ProductPage/Forms/form_combination.html.twig',
                    array(
                        'form' => $form->createView(),
                    )
                );
                $result['ids_product_attribute'][] = $combination['id_product_attribute'];
            }
        }

        return $response->create($result);
    }

    /**
     * @param Product $product
     * @param array $combinations
     */
    public function ensureProductHasDefaultCombination(Product $product, array $combinations)
    {
        if (count($combinations)) {
            $defaultProductAttributeId = $product->getDefaultIdProductAttribute();
            if (!$defaultProductAttributeId) {
                /*
                 * Combinations indexed by position, then attribute id
                 * ex: $combinations = [
                 *  3 => [ //4th position attribute
                 *      45 => [ //product_attribute id
                 *      ]
                 *  ]
                 * ]
                 */
                $firstPosition = array_keys($combinations)[0];
                if (!empty($combinations[$firstPosition])) {
                    $firstAttributeId = array_keys($combinations[$firstPosition])[0];
                    $product->setDefaultAttribute($firstAttributeId);
                }
            }
        }
    }

    /**
     * Delete a product attribute.
     *
     * @AdminSecurity("is_granted(['delete'], request.get('_legacy_controller'))")
     *
     * @param int $idProduct The product ID
     * @param Request $request The request
     *
     * @return JsonResponse
     */
    public function deleteAttributeAction($idProduct, Request $request)
    {
        $response = new JsonResponse();

        if (!$request->isXmlHttpRequest()) {
            return $response;
        }

        $legacyResponse = false;

        if ($request->request->has('attribute-ids')) {
            $attributeIds = $request->request->get('attribute-ids');
            foreach ($attributeIds as $attributeId) {
                $legacyResponse = $this->get('prestashop.adapter.admin.controller.attribute_generator')
                    ->ajaxProcessDeleteProductAttribute($attributeId, $idProduct);
            }

            if ($legacyResponse['status'] == 'error') {
                $response->setStatusCode(400);
            }

            $response->setData(['message' => $legacyResponse['message']]);
        }

        return $response;
    }

    /**
     * Delete all product attributes.
     *
     * @AdminSecurity("is_granted(['delete'], request.get('_legacy_controller'))")
     *
     * @param int $idProduct The product ID
     * @param Request $request The request
     *
     * @return JsonResponse
     */
    public function deleteAllAttributeAction($idProduct, Request $request)
    {
        $attributeAdapter = $this->get('prestashop.adapter.data_provider.attribute');
        $response = new JsonResponse();

        //get all attribute for a product
        $combinations = $attributeAdapter->getProductCombinations($idProduct);

        if (!$combinations || !$request->isXmlHttpRequest()) {
            return $response;
        }

        $res = false;

        foreach ($combinations as $combination) {
            $res = $this->get('prestashop.adapter.admin.controller.attribute_generator')
                ->ajaxProcessDeleteProductAttribute($combination['id_product_attribute'], $idProduct);

            if ($res['status'] == 'error') {
                $response->setStatusCode(400);

                break;
            }
        }

        $response->setData(['message' => $res['message']]);

        return $response;
    }

    /**
     * get the images form for a product combinations.
     *
     * @AdminSecurity("is_granted(['read'], request.get('_legacy_controller'))")
     *
     * @param int $idProduct The product id
     * @param Request $request The request
     *
     * @return JsonResponse
     */
    public function getFormImagesAction($idProduct, Request $request)
    {
        $response = new JsonResponse();
        $productAdapter = $this->get('prestashop.adapter.data_provider.product');
        $attributeAdapter = $this->get('prestashop.adapter.data_provider.attribute');
        $locales = $this->get('prestashop.adapter.legacy.context')->getLanguages();

        //get product
        $product = $productAdapter->getProduct((int) $idProduct);

        //get product images
        $productImages = $productAdapter->getImages($idProduct, $locales[0]['id_lang']);

        if (!$request->isXmlHttpRequest() || !is_object($product) || empty($product->id)) {
            $response->setStatusCode(400);

            return $response;
        }

        $data = [];
        $combinations = $attributeAdapter->getProductCombinations($idProduct);
        foreach ($combinations as $combination) {
            //get combination images
            $combinationImages = array_map(function ($o) {
                return $o['id'];
            }, $attributeAdapter->getImages($combination['id_product_attribute']));

            $newProductImages = $productImages;
            foreach ($newProductImages as $k => $image) {
                $newProductImages[$k]['id_image_attr'] = false;
                if (in_array($image['id'], $combinationImages)) {
                    $newProductImages[$k]['id_image_attr'] = true;
                }
            }

            $data[$combination['id_product_attribute']] = $newProductImages;
        }

        $response->setData($data);

        return $response;
    }
}
