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

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ImportController extends FrameworkBundleAdminController
{
    public function importAction(Request $request)
    {
        $legacyController = $request->attributes->get('_legacy_controller');

        $formHandler = $this->get('prestashop.admin.import.form_handler');
        $form = $formHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();
            // validate data before forwarding

            return $this->fowardRequestToLegacyResponse($request, $data);
        }

        $params = [
            'layoutHeaderToolbarBtn' => [],
            'layoutTitle' => $this->get('translator')->trans('Import', [], 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => true,
            'requireBulkActions' => false,
            'showContentHeader' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
            'form' => $form->createView(),
        ];

        return $this->render('@AdvancedParameters/import.html.twig', $params);
    }

    /**
     * Fowards submitted form data to legacy import page
     *
     * @param Request $request
     * @param array $data
     *
     * @return RedirectResponse
     */
    private function fowardRequestToLegacyResponse(Request $request, array $data)
    {
        $legacyController = $request->attributes->get('_legacy_controller');
        $legacyContext =  $this->get('prestashop.adapter.legacy.context');

        $legacyImportUrl = $legacyContext->getAdminLink($legacyController);

        return $this->redirect($legacyImportUrl, Response::HTTP_TEMPORARY_REDIRECT);
    }
}