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

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Responsible of "Configure > Advanced Parameters > Information" page display.
 */
class SystemInformationController extends FrameworkBundleAdminController
{
    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message="Access denied.")
     * @Template("@PrestaShop/Admin/Configure/AdvancedParameters/system_information.html.twig")
     *
     * @param Request $request
     *
     * @return array<string, array|bool|string|null>
     */
    public function indexAction(Request $request)
    {
        $legacyController = $request->get('_legacy_controller');
        $requirementsSummary = $this->getRequirementsChecker()->getSummary();
        $systemInformationSummary = $this->getSystemInformation()->getSummary();

        return [
            'layoutHeaderToolbarBtn' => [],
            'layoutTitle' => $this->trans('Information', 'Admin.Navigation.Menu'),
            'requireBulkActions' => false,
            'showContentHeader' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
            'requireFilterStatus' => false,
            'errorMessage' => 'ok',
            'system' => $systemInformationSummary,
            'requirements' => $requirementsSummary,
            'userAgent' => $request->headers->get('User-Agent'),
        ];
    }

    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message="Access denied.")
     *
     * @return JsonResponse
     */
    public function displayCheckFilesAction()
    {
        return new JsonResponse($this->getRequiredFilesChecker()->getListOfUpdatedFiles());
    }

    /**
     * @return \PrestaShop\PrestaShop\Adapter\System\SystemInformation
     */
    private function getSystemInformation()
    {
        return $this->get('prestashop.adapter.system_information');
    }

    /**
     * @return \PrestaShop\PrestaShop\Adapter\Requirement\CheckRequirements
     */
    private function getRequirementsChecker()
    {
        return $this->get('prestashop.adapter.check_requirements');
    }

    /**
     * @return \PrestaShop\PrestaShop\Adapter\Requirement\CheckMissingOrUpdatedFiles
     */
    private function getRequiredFilesChecker()
    {
        return $this->get('prestashop.adapter.check_missing_files');
    }
}
