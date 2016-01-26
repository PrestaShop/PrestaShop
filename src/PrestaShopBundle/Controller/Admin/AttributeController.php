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

use PrestaShopBundle\Form\Admin\Product\ProductCombination;
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
        $translator = $this->container->get('prestashop.adapter.translator');
        $attributes = $this->container->get('prestashop.adapter.data_provider.attribute')->getAttributes($locales[0]['id_lang'], true);

        $dataGroupAttributes = [];
        $data = [];
        foreach ($attributes as $attribute) {
            /** Construct attribute group selector. Ex : Color : All */
            $dataGroupAttributes[$attribute['id_attribute_group']] = [
                'value' => 'group-'.$attribute['id_attribute_group'],
                'label' => $attribute['public_name'].' : '.$translator->trans('All', [], 'AdminTabs'),
                'data' => [
                    'id_group' => $attribute['id_attribute_group'],
                    'name' => $attribute['public_name'],
                ]
            ];

            $data[] = [
                'value' => $attribute['id_attribute'],
                'label' => $attribute['public_name'].' : '.$attribute['name'],
                'data' => [
                    'id_group' => $attribute['id_attribute_group'],
                    'name' => $attribute['name'],
                ]
            ];
        }

        $data = array_merge($dataGroupAttributes, $data);

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
        $product = $productAdapter->getProduct((int)$idProduct);

        if (!is_object($product) || empty($product->id) || empty($options) || !is_array($options)) {
            $response->setStatusCode(400);
            return $response;
        }

        $modelMapper = new ProductAdminModelAdapter(
            $product,
            $this->container->get('prestashop.adapter.legacy.context'),
            $this->container->get('prestashop.adapter.admin.wrapper.product'),
            $this->container->get('prestashop.adapter.tools'),
            $this->container->get('prestashop.adapter.data_provider.product'),
            $this->container->get('prestashop.adapter.data_provider.supplier'),
            $this->container->get('prestashop.adapter.data_provider.warehouse'),
            $this->container->get('prestashop.adapter.data_provider.feature'),
            $this->container->get('prestashop.adapter.data_provider.pack'),
            $this->container->get('prestashop.adapter.shop.context')
        );

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
                    $allGroupAttributes = $this->container->get('prestashop.adapter.data_provider.attribute')->getAttributeIdsByGroup((int)$idGroup, true);
                    foreach ($allGroupAttributes as $groupAttribute) {
                        $newOptions[$idGroup][$groupAttribute] = $groupAttribute;
                    }
                } else {
                    $newOptions[$idGroup][$attribute] = $attribute;
                }
            }
        }

        //create attributes
        $this->container->get('prestashop.adapter.admin.controller.attribute_generator')->processGenerate($product, $newOptions);

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

            $form = $this->createForm(new ProductCombination(
                $this->container->get('prestashop.adapter.translator'),
                $this->container->get('prestashop.adapter.legacy.context')
            ), $modelMapper->getFormCombination($attribute[0]));

            $formRender = $this->renderView(
                'PrestaShopBundle:Admin/Product/Include:form-combination.html.twig',
                array(
                    'form' => $form->createView(),
                    'id_product' => $idProduct
                )
            );

            $newCombinations[] = $formRender;
        }

        $response->setData($newCombinations);

        return $response;
    }

    /**
     * Delete a product attribute
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

        $response->setData(['message' => $translator->trans($res['message'], [], 'AdminProducts')]);
        return $response;
    }

    /**
     * Delete all product attributes
     *
     * @param int $idProduct The product ID
     * @param Request $request The request
     *
     * @return string
     */
    public function deleteAllAttributeAction($idProduct, Request $request)
    {
        $translator = $this->container->get('prestashop.adapter.translator');
        $attributeAdapter = $this->container->get('prestashop.adapter.data_provider.attribute');
        $response = new JsonResponse();

        //get all attribute for a product
        $combinations = $attributeAdapter->getProductCombinations($idProduct);

        if (!$combinations || !$request->isXmlHttpRequest()) {
            return $response;
        }

        foreach ($combinations as $combination) {
            $res = $this->container->get('prestashop.adapter.admin.controller.attribute_generator')
                ->ajaxProcessDeleteProductAttribute($combination['id_product_attribute'], $idProduct);

            if ($res['status'] == 'error') {
                $response->setStatusCode(400);
                break;
            }
        }

        $response->setData(['message' => $translator->trans($res['message'], [], 'AdminProducts')]);

        return $response;
    }

    /**
     * get All Combinations for a product
     *
     * @param int $idProduct The product id
     *
     * @return string Json
     */
    public function getProductCombinationsAction($idProduct)
    {
        $response = new JsonResponse();
        $attributeAdapter = $this->container->get('prestashop.adapter.data_provider.attribute');
        $combinations = $attributeAdapter->getProductCombinations($idProduct);

        //get product
        $productAdapter = $this->container->get('prestashop.adapter.data_provider.product');
        $product = $productAdapter->getProduct((int)$idProduct);

        //get combinations
        $modelMapper = new ProductAdminModelAdapter(
            $product,
            $this->container->get('prestashop.adapter.legacy.context'),
            $this->container->get('prestashop.adapter.admin.wrapper.product'),
            $this->container->get('prestashop.adapter.tools'),
            $this->container->get('prestashop.adapter.data_provider.product'),
            $this->container->get('prestashop.adapter.data_provider.supplier'),
            $this->container->get('prestashop.adapter.data_provider.warehouse'),
            $this->container->get('prestashop.adapter.data_provider.feature'),
            $this->container->get('prestashop.adapter.data_provider.pack'),
            $this->container->get('prestashop.adapter.shop.context')
        );

        $combinationList = [];
        foreach ($combinations as $combination) {
            $newCombination = $modelMapper->getFormCombination($combination);
            $combinationList[] = ['id' => $newCombination['id_product_attribute'], 'name' => $newCombination['name']];
        }

        $response->setData($combinationList);

        return $response;
    }

    /**
     * get the images form for a product combinations
     *
     * @param int $idProduct The product id
     * @param Request $request The request
     *
     * @return string Json
     */
    public function getFormImagesAction($idProduct, Request $request)
    {
        $response = new JsonResponse();
        $productAdapter = $this->container->get('prestashop.adapter.data_provider.product');
        $attributeAdapter = $this->container->get('prestashop.adapter.data_provider.attribute');
        $locales = $this->container->get('prestashop.adapter.legacy.context')->getLanguages();

        //get product
        $product = $productAdapter->getProduct((int)$idProduct);

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
