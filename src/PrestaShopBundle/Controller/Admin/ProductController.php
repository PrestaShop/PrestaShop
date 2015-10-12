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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use PrestaShopBundle\TransitionalBehavior\AdminPagePreferenceInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Admin controller for the Product pages using the Symfony architecture:
 * - categories
 * - product list
 * - product details
 * - product attributes
 * - ...
 *
 * This controller is the first one to be refactored to the new Symfony Architecture.
 * The retro-compatibility is dropped for the corresponding Admin pages.
 * A set of hooks are integrated and an Adapter is made to wrap the new EventDispatcher
 * component to the existing hook system. So existing hooks are always triggered, but from the new
 * code (and so needs to be adapted on the module side ton comply on the new parameters formats, the new UI style, etc...).
 *
 * FIXME: to adapt after 1.7.0 when alternative behavior will be removed (@see AdminPagePreferenceInterface::getTemporaryShouldUseLegacyPage()).
 */
class ProductController extends Controller
{
    /**
     * Get the Catalog page with KPI banner, product list, bulk actions, filters, search, etc...
     *
     * URL example: /product/catalog/40/20/id_product/asc
     *
     * @param Request $request
     * @param string $orderBy To order product list
     * @param string $orderWay To order product list
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function catalogAction(Request $request, $orderBy = 'id_product', $orderWay = 'asc')
    {
        // Redirect to legacy controller (FIXME: temporary behavior)
        $pagePreference = $this->container->get('prestashop.core.admin.page_preference_interface');
        /* @var $pagePreference AdminPagePreferenceInterface */
        if ($pagePreference->getTemporaryShouldUseLegacyPage('product')) {
            $legacyUrlGenerator = $this->container->get('prestashop.core.admin.url_generator_legacy');
            /* @var $legacyUrlGenerator UrlGeneratorInterface */
            $redirectionParams = array(
                // do not tranmit limit & offset: go to the first page when redirecting
                'productOrderby' => $orderBy,
                'productOrderway' => $orderWay
            );
            return $this->redirect($legacyUrlGenerator->generate('admin_product_catalog', $redirectionParams), 302);
        }

        $productDataProvider = $this->container->get('prestashop.core.admin.data_provider.factory')->forProduct();
        // TODO !1: continue
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
        ));
    }

    /**
     * @Template
     *
     * Product form
     *
     * @param int $id The product ID
     *
     * @return array Send datas to view
     */
    public function formAction($id)
    {
        // TODO !2: add legacy should redirect or not. (xgouley)
        // TODO !3: add legacy options on route
        $request = $this->get('request'); //example call request service
        $legacyContext = $this->container->get('prestashop.adapter.legacy.context');

        return array(
            'title' => $id ? 'Modifier' : 'Ajouter',
        );
    }
}
