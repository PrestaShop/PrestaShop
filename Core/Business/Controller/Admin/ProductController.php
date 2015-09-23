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
namespace PrestaShop\PrestaShop\Core\Business\Controller\Admin;

use PrestaShop\PrestaShop\Core\Business\Controller\AdminController;
use PrestaShop\PrestaShop\Core\Foundation\Routing\Response;
use Symfony\Component\HttpFoundation\Request;
use PrestaShop\PrestaShop\Core\Business\Controller\AutoObjectInflaterTrait;
use PrestaShop\PrestaShop\Core\Business\Controller\AutoResponseFormatTrait;
use PrestaShop\PrestaShop\Core\Business\Controller\SfControllerResolverTrait;
use PrestaShop\PrestaShop\Core\Foundation\Form\FormFactory;
use PrestaShop\PrestaShop\Core\Business\Product\Form as ProductForms;
use PrestaShop\PrestaShop\Core\Foundation\Controller\BaseController;

/**
 * Admin controller for the Product pages:
 * - categories
 * - product list
 * - product details
 * - product attributes
 * - ...
 */
class ProductController extends AdminController
{
    use AutoObjectInflaterTrait; // auto inflate objects if pattern found in the route format.
    use AutoResponseFormatTrait; // try to auto fill template engine parameters according to the current action.
    use SfControllerResolverTrait; // dependency injection in sf way.

    /**
     * Get the Catalog page with stats banner, product list, bulk actions, filters, search, etc...
     *
     * URL example: /product/catalog/40/20/id_product/asc
     *
     * @param Request $request
     * @param Response $response
     */
    public function productCatalogAction(Request &$request, Response &$response)
    {
        
        // FIXME: remove this redirection to legacy and develop catalog page when needed.
//         $this->redirectToRoute(
//             $request,
//             'admin_product_catalog',
//             array(
//                 'productOrderby' => $request->attributes->get('orderBy'),
//                 'productOrderway' => $request->attributes->get('orderWay')
//             ),
//             true, // force legacy URL
//             false // temporary
//         );

        // Retrieve persisted filter parameters
        $persistedFilterParameters = $this->container->make('CoreAdapter:Product\\AdminProductDataProvider')->getPersistedFilterParameters('ls_products_');

        // Transmit persisted filter parameters updated by posted ones variables
        $persistedFilterParameters = array_replace($persistedFilterParameters, $request->request->all());
        $response->addContentData(null, $persistedFilterParameters);

        // get Product list from productListAction subcall
        $productListParams = array(
            'ls_products_limit' => $request->attributes->get('limit'),
            'ls_products_offset' => $request->attributes->get('offset'),
            'ls_products_orderBy' => $request->attributes->get('orderBy'),
            'ls_products_orderWay' => $request->attributes->get('orderWay'),
            '_layout_mode' => 'none_html',
        );
        $subResponse = $this->subcall('admin_product_list', $productListParams, BaseController::RESPONSE_PARTIAL_VIEW, true);
        $response->addContentData('product_list', $subResponse->getContent());

        // URL action form params
        $formParams = array(
            'limit' => $request->attributes->get('limit'),
            'offset' => $request->attributes->get('offset'),
            'orderBy' => $request->attributes->get('orderBy'),
            'orderWay' => $request->attributes->get('orderWay')
        );
        $response->addContentData('post_url', $this->generateUrl('admin_product_catalog', $formParams));

        // Alternative layout for empty list
        $totalProductCount = $subResponse->getContentData('product_count');
        if ($totalProductCount === 0) {
            $response->setTemplate('Core/Controller/Product/productCatalogEmpty.tpl');
        } else {
            $response->addContentData('product_count', $totalProductCount);

            // Navigator
            $navigator = $this->fetchNavigator($request, $totalProductCount);
            $response->addContentData('navigator', $navigator);

            // Category tree
            $formFactory = new FormFactory();
            // FIXME: ajouter la selection par defaut, provenant de ls_products_category_filter OU
            $form = $formFactory->create(new ChoiceCategorysTreeType('##Categories', \Category::getNestedCategories(), false));
            $engine = new \PrestaShop\PrestaShop\Core\Foundation\View\ViewFactory($this->container, 'twig');
            $response->addContentData(
                'categories',
                $engine->view->render('Core/Controller/Product/categoriesTreeSelector.html.twig', array('form' => $form->createView()))
            );
            $response->addJs(_PS_JS_DIR_.'Core/Admin/Categories.js');
        }

        // Add layout top-right menu actions
        $response->setHeaderToolbarBtn(
            array(
                'legacy' => array(
                    'href' => '#', // FIXME
                    'desc' => $this->container->make('CoreAdapter:Translator')->trans('##No! Give me the old page!', array(), 'AdminProducts'),
                    'icon' => 'process-icon-toggle-on',
                    'help' => '##The new page cannot fit your needs now? Fallback to the old one, and tell us why!'
                ),
                'add' => array(
                    'href' => $this->generateUrl('admin_product_form'),
                    'desc' => '##Add euh niou product',
                    'icon' => 'process-icon-new'
                ),
            )
        );
    }

    /**
     * Get only the list of products to display on the main Admin Product page.
     * The full page that shows products list will subcall this action (from productListCatalogAction).
     * URL example: /product/list/layout_html/40/20/id_product/asc
     *
     * @param Request $request
     * @param Response $response
     * @param Product[] $products The collection of products requested. Filled by AutoObjectInflaterTrait.
     * @return void The response format is automatically placed by the Router through _layout_mode attribute
     */
    public function productListAction(Request &$request, Response &$response, array $products)
    {
        $totalCount = 0;
        // Adds controller info (URLs, etc...) to product list
        foreach ($products as &$product) {
            $totalCount = $product['total'];
            $product['url'] = $this->generateUrl('admin_product_form', array('id_product' => $product['id_product']));
        }

        $response->replaceContentData('products', $products);
        $response->addContentData('product_count', $totalCount);
    }

    public function productFormAction(Request &$request, Response &$response, $product)
    {
        $formFactory = new FormFactory();
        $builder = $formFactory->createBuilder();

        $response->setJs(array(
            _PS_JS_DIR_.'tiny_mce/tiny_mce.js',
            _PS_JS_DIR_.'admin/tinymce.inc.js',
            _PS_JS_DIR_.'admin/tinymce_loader.js',
        ));

        $form = $builder
            ->add('step1', new ProductForms\ProductStep1($this->container))
            ->getForm();

        $response->setEngineName('twig');
        $response->setLegacyControllerName('AdminProducts');
        $response->setTitle('My custom title');
        $response->setDisplayType('add');

        $response->addContentData('form', $form->createView());
    }
}
