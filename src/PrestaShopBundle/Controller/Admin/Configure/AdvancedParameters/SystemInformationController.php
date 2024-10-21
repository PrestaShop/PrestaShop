<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use PrestaShop\PrestaShop\Adapter\Requirement\CheckMissingOrUpdatedFiles;
use PrestaShop\PrestaShop\Adapter\Requirement\CheckRequirements;
use PrestaShop\PrestaShop\Adapter\System\SystemInformation;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible of "Configure > Advanced Parameters > Information" page display.
 */
class SystemInformationController extends PrestaShopAdminController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message: 'Access denied.')]
    public function indexAction(
        Request $request,
        CheckRequirements $checkRequirements,
        SystemInformation $systemInformation,
    ): Response {
        $legacyController = $request->get('_legacy_controller');
        $requirementsSummary = $checkRequirements->getSummary();
        $systemInformationSummary = $systemInformation->getSummary();

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/system_information.html.twig', [
            'layoutHeaderToolbarBtn' => [],
            'layoutTitle' => $this->trans('Information', [], 'Admin.Navigation.Menu'),
            'requireBulkActions' => false,
            'showContentHeader' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
            'requireFilterStatus' => false,
            'errorMessage' => 'ok',
            'system' => $systemInformationSummary,
            'requirements' => $requirementsSummary,
            'userAgent' => $request->headers->get('User-Agent'),
        ]);
    }

    /**
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message: 'Access denied.')]
    public function displayCheckFilesAction(
        CheckMissingOrUpdatedFiles $requiredFilesChecker,
    ): JsonResponse {
        return new JsonResponse($requiredFilesChecker->getListOfUpdatedFiles());
    }
}
