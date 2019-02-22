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

namespace PrestaShopBundle\Controller\Admin\Sell\Catalog;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ManufacturerController is responsible for "Sell > Catalog > Brands & Suppliers" page.
 */
class ManufacturerController extends FrameworkBundleAdminController
{
    public function createAction()
    {
        //todo: implement
        return $this->redirectToRoute('admin_manufacturers_index');
    }

    /**
     * Edits manufacturer
     *
     * @param Request $request
     * @param $manufacturerId
     *
     * @return Response
     */
    public function editAction(Request $request, $manufacturerId)
    {
        $manufacturerFormHandler = $this->get('prestashop.core.form.identifiable_object.handler.manufacturer_form_handler');
        $manufacturerFormBuilder = $this->get('prestashop.core.form.identifiable_object.builder.manufacturer_form_builder');
        $manufacturerForm = $manufacturerFormBuilder->getFormFor($manufacturerId);
        $manufacturerForm->handleRequest($request);

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Manufacturer/edit.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
            'manufacturerForm' => $manufacturerForm->createView(),
            'manufacturerName' => 'test',
        ]);
    }

    /**
     * Provides error messages for exceptions
     *
     * @return array
     */
    private function getErrorMessages()
    {
        //@todo: implement
        return [];
    }
}
