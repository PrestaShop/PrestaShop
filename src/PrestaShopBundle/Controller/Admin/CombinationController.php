<?php
/**
 * 2007-2016 PrestaShop
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
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


namespace PrestaShopBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use PrestaShop\PrestaShop\Adapter\CombinationDataProvider;
use PrestaShopBundle\Form\Admin\Product\ProductCombination;
use PrestaShopBundle\Model\Product\AdminModelAdapter as ProductAdminModelAdapter;

class CombinationController extends Controller
{
    public function generateCombinationFormAction($combinationIds)
    {
        $response = new Response();
        $result = '';

        $combinations = explode('-', $combinationIds);
        if ($combinationIds == 0 || count($combinations) == 0) {
            return $response;
        }

        $combinationDataProvider = $this->get('prestashop.adapter.data_provider.combination');

        foreach ($combinations as $combinationId) {
            $form = $this->get('form.factory')
                ->createNamed(
                    "combination_$combinationId",
                    'PrestaShopBundle\Form\Admin\Product\ProductCombination',
                    $combinationDataProvider->getFormCombination($combinationId)
                );
            $result .= $this->renderView(
                'PrestaShopBundle:Admin/Product/Include:form_combination.html.twig',
                array(
                    'form' => $form->createView(),
                )
            );
        }

        return $response->create($result);
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

        $combinations = $modelMapper->getAttributesResume();

        $combinationList = array();

        if (is_array($combinations)) {
            foreach ($combinations as $combination) {
                $combinationList[] = ['id' => $combination['id_product_attribute'], 'name' => $combination['attribute_designation']];
            }
        }

        $response->setData($combinationList);

        return $response;
    }
}
