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
use PrestaShop\PrestaShop\Core\Foundation\Exception\WarningException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use PrestaShop\PrestaShop\Core\Business\Context;
use PrestaShop\PrestaShop\Core\Foundation\Exception\ErrorException;
use PrestaShop\PrestaShop\Core\Foundation\Exception\DevelopmentErrorException;
use PrestaShop\PrestaShop\Core\Business\Routing\Router;

class ProductController extends AdminController
{
    use AutoObjectInflaterTrait; // auto inflate objects if pattern found in the route format.
    use AutoResponseFormatTrait; // try to auto fill template engine parameters according to the current action.
    use SfControllerResolverTrait; // dependency injection in sf way.

    /**
     * Get the Catalog page with stats banner, product list, bulk actions, filters, search, etc...
     * URL example: /product/catalog/40/20/id_product/asc
     *
     * TODO : cette page sera-t-elle à refaire ? Pour le moment on bypass et on renvoie vers la page legacy.
     *
     * @param Request $request
     * @param Response $response
     */
    public function productCatalogAction(Request &$request, Response &$response)
    {
        // FIXME: remove this redirection to legacy and develop catalog page when needed.
        $this->redirectToRoute(
            $request,
            'admin_product_catalog',
            array(
                'productOrderby' => $request->attributes->get('orderBy'),
                'productOrderway' => $request->attributes->get('orderWay')
            ),
            true, // force legacy URL
            false // temporary
        );

        // get Product list from productListAction subcall
        $productListParams = array(
            'ls_products_limit' => $request->attributes->get('limit'),
            'ls_products_offset' => $request->attributes->get('offset'),
            'ls_products_orderBy' => $request->attributes->get('orderBy'),
            'ls_products_orderWay' => $request->attributes->get('orderWay'),
            '_layout_mode' => 'none_html',
        );
        // URL example: /product/list/none_html/40/20/id_product/asc
        $response->addContentData('product_list', $this->subcall('admin_product_list', $productListParams));
        
        // Add layout top-right menu actions
        // TODO
    }

    /**
     * Get only the list of products to display on the main Admin Product page.
     * The full page that shows products list will subcall this action (from productListCatalogAction).
     * URL example: /product/list/layout_html/40/20/id_product/asc
     *
     * @param Request $request
     * @param Response $response
     * @param array $products The collection of products requested. Filled by AutoObjectInflaterTrait.
     * @return void The response format is automatically placed by the Router through _layout_mode attribute
     */
    public function productListAction(Request &$request, Response &$response, array $products)
    {
        // empty list case: change template
        if ($request->attributes->get('_layout_mode', 'layout_html') == 'none_html' && count($products) === 0) {
            $response->setTemplate('Core/Controller/Product/productListEmpty.tpl');
        }
    }

    public function productFormAction(Request &$request, Response &$response, $product)
    {
        // Should redirect to legacy or not?
        /*if (false) { // FIXME: option 'UseLegacyProductPage' a tester pour le savoir!
            $this->redirectToRoute(
                $request,
                'admin_product_form',
                $request->attributes->all(),
                true, // force legacy URL
                false // temporary
            );
        }*/

        $formFactory = new FormFactory();
        $builder = $formFactory->create();

        $form = $builder
            ->setAction('')
            ->add('title', 'text')
            ->add('description', 'textarea')
            ->getForm();

        $response->setEngineName('twig');
        $response->setLegacyControllerName('AdminProducts');
        $response->setTitle('My custom title');
        $response->setDisplayType('add');

        $response->addContentData('form', $form->createView());
    }
}
