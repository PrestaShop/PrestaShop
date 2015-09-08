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
use PrestaShop\PrestaShop\Core\Foundation\Controller\SfControllerResolverTrait;
use PrestaShop\PrestaShop\Core\Foundation\Exception\WarningException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use PrestaShop\PrestaShop\Core\Business\Context;

class ProductController extends AdminController
{
    use AutoObjectInflaterTrait; // auto inflate objects if pattern found in the route format.
    use AutoResponseFormatTrait; // try to auto fill template engine parameters according to the current action.
    use SfControllerResolverTrait; // dependency injection in sf way.

    /**
     * Get only the list of products to display on the main Admin Product page.
     * The full page that shows products list will subcall this action (from productListCatalogAction).
     * URL example: /product/list/layout_html/40/20/id/asc
     *
     * @param Request $request
     * @param Response $response
     * @param array $products The collection of products requested. Filled by AutoObjectInflaterTrait.
     * @return void The response format is automatically placed by the Router through _layout_mode attribute
     */
    public function productListAction(Request &$request, Response &$response, $products)
    {
        //$this->getRouter()->redirectToRoute($request, 'admin_product_list_2', array('_layout_mode' => 'none_html'));
        //$this->getRouter()->forward($request, 'admin_product_list_2', array('_layout_mode' => 'none_html'));
    }
    
    public function productList2Action(Request &$request, Response &$response, $products)
    {
    }
    
    public function productCatalogAction(Request &$request, Response &$response)
    {
        $subcallParams = array(
            'ls_products_limit' => 42
        );
        $response->addContentData('product_list', $this->subcall('admin_product_list', $subcallParams));
        $response->addContentData('manu_forced', $this->generateUrl('admin_product_catalog', array('titi' => 'tutu'), true, UrlGeneratorInterface::ABSOLUTE_URL));
        $response->addContentData('auto_forced', $this->generateUrl('admin_product_categories'));
    }
}
