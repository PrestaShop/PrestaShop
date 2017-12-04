<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Responsible of "Configure > Advanced Parameters > Information" page display
 */
class SystemInformationController extends FrameworkBundleAdminController
{
    /**
     * @var string The controller name for routing.
     */
    const CONTROLLER_NAME = 'AdminInformation';

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $requirementsSummary = $this->getRequirementsChecker()->getSummary();
        $systemInformationSummary = $this->getSystemInformation()->getSummary();

        $twigValues = array(
            'layoutHeaderToolbarBtn' => [],
            'layoutTitle' => $this->get('translator')->trans('Information', array(), 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => true,
            'requireBulkActions' => false,
            'showContentHeader' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink('AdminInformation'),
            'requireFilterStatus' => false,
            'level' => $this->authorizationLevel($this::CONTROLLER_NAME),
            'errorMessage' => 'ok',
            'system' => $systemInformationSummary,
            'requirements' => $requirementsSummary,
            'userAgent' => $request->headers->get('User-Agent'),
        );

        return $this->render('@AdvancedParameters/system_information.html.twig', $twigValues);
    }

    /**
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
